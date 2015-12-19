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

use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
	/* @var driver_interface */
	protected $db;

	/** @var request */
	protected $request;

	/** @var user */
	protected $user;

	/** @var string */
	protected $forums_table;

	/** @var string */
	protected $posts_table;

	/**
	 * Constructor
	 *
	 * @param driver_interface $db           Database object
	 * @param request          $request      Request object
	 * @param user             $user         User object
	 * @param string           $forums_table Database forums table
	 * @param string           $posts_table  Database posts table
	 */
	public function __construct(driver_interface $db, request $request, user $user, $forums_table, $posts_table)
	{
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->forums_table = $forums_table;
		$this->posts_table = $posts_table;
	}

	/**
	 * @inheritdoc
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.modify_posting_parameters'		=> 'setup',
			'core.posting_modify_submit_post_after' => 'update_subjects',
		);
	}

	public function setup()
	{
		$this->user->add_lang_ext('vse/smartsubjects', 'smartsubjects');
	}

	/**
	 * Update the post subjects in a topic
	 *
	 * @param object $event The event object
	 * @return null
	 */
	public function update_subjects($event)
	{
		// Only proceed if editing the first post in a topic
		if ($event['mode'] != 'edit' || $event['data']['topic_first_post_id'] != $event['post_id'])
		{
			return;
		}

		// Overwrite will update all reply subjects (including non-matching)
		$overwrite = $this->request->is_set_post('overwrite_subjects');

		if ($event['update_subject'] || $overwrite)
		{
			// Re: is actually hardcoded within phpBB ¯\_(ツ)_/¯
			$old_subject = 'Re: ' . $event['data']['topic_title'];
			$new_subject = 'Re: ' . $event['post_data']['post_subject'];

			// Update the topic's subjects
			$sql = 'UPDATE ' . $this->posts_table . "
				SET post_subject = '" . $this->db->sql_escape($new_subject) . "'
				WHERE topic_id = " . (int) $event['topic_id'] .
					((!$overwrite) ? " AND post_subject = '" . $this->db->sql_escape($old_subject) . "'" : ' AND post_id != ' . (int) $event['post_id']);
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
}
