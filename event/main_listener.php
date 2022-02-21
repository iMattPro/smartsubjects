<?php
/**
 *
 * Smart Subjects
 *
 * @copyright (c) 2015 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\smartsubjects\event;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var auth */
	protected $auth;

	/* @var driver_interface */
	protected $db;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var string */
	protected $forums_table;

	/** @var string */
	protected $posts_table;

	/**
	 * Constructor
	 *
	 * @param auth             $auth         Permissions object
	 * @param driver_interface $db           Database object
	 * @param language         $language     Language object
	 * @param request          $request      Request object
	 * @param string           $forums_table Database forums table
	 * @param string           $posts_table  Database posts table
	 */
	public function __construct(auth $auth, driver_interface $db, language $language, request $request, $forums_table, $posts_table)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->forums_table = $forums_table;
		$this->posts_table = $posts_table;
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.permissions'						=> 'add_permission',
			'core.posting_modify_template_vars'		=> 'setup',
			'core.posting_modify_submit_post_after' => 'update_subjects',
		];
	}

	/**
	 * Add administrative permissions to manage forums
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 */
	public function add_permission($event)
	{
		$event->update_subarray('permissions', 'f_smart_subjects', ['lang' => 'ACL_F_SMART_SUBJECTS', 'cat' => 'post']);
	}

	/**
	 * Setup Smart Subjects
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 */
	public function setup($event)
	{
		$this->language->add_lang('smartsubjects', 'vse/smartsubjects');

		$event->update_subarray('page_data', 'S_SMART_SUBJECTS_MOD', $this->forum_auth($event['forum_id']) && $this->auth->acl_get('m_', $event['forum_id']));
	}

	/**
	 * Update the post subjects in a topic
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 */
	public function update_subjects($event)
	{
		// Only proceed if editing the first post in a topic and smart subjects is allowed
		if ($event['mode'] !== 'edit' || (int) $event['data']['topic_first_post_id'] !== (int) $event['post_id'] || !$this->forum_auth($event['forum_id']))
		{
			return;
		}

		// Overwrite will update all reply subjects (including non-matching)
		$overwrite = $this->request->is_set_post('overwrite_subjects');

		if ($overwrite || $event['update_subject'])
		{
			// Re: is actually hardcoded within phpBB ¯\_(ツ)_/¯
			$old_subject = 'Re: ' . $event['data']['topic_title'];
			$new_subject = 'Re: ' . $event['post_data']['post_subject'];

			// Update the topic's subjects
			$sql = 'UPDATE ' . $this->posts_table . "
				SET post_subject = '" . $this->db->sql_escape($new_subject) . "'
				WHERE topic_id = " . (int) $event['topic_id'] .
					((!$overwrite) ? " AND post_subject = '" . $this->db->sql_escape($old_subject) . "'" : ' AND post_id <> ' . (int) $event['post_id']);
			$this->db->sql_query($sql);

			// Update the forum last post subject if applicable
			$sql = 'UPDATE ' . $this->forums_table . "
				SET forum_last_post_subject = '" . $this->db->sql_escape($new_subject) . "'
				WHERE forum_last_post_id = " . (int) $event['data']['topic_last_post_id'] . '
					AND forum_id = ' . (int) $event['forum_id'] .
					((!$overwrite) ? " AND forum_last_post_subject = '" . $this->db->sql_escape($old_subject) . "'" : '');
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Is the forum authorised to use smart subjects
	 *
	 * @param int $forum_id Forum identifier
	 * @return bool True if allowed, false if not
	 */
	protected function forum_auth($forum_id)
	{
		return (bool) $this->auth->acl_get('f_smart_subjects', $forum_id);
	}
}
