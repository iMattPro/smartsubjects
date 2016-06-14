<?php
/**
 *
 * Smart Subjects
 *
 * @copyright (c) 2015 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\smartsubjects\tests\functional;

require_once __DIR__ . '/../../../../../includes/functions.php';

/**
 * @group functional
 */
class edit_subject_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('vse/smartsubjects');
	}

	public function setUp()
	{
		parent::setUp();

		$this->add_lang_ext('vse/smartsubjects', 'smartsubjects');
	}

	public function test_edit_options_admin()
	{
		$this->login();
		$crawler = self::request('GET', "posting.php?mode=edit&f=2&p=1&sid={$this->sid}");
		$this->assertContainsLang('OVERWRITE_SUBJECTS', $crawler->filter('html')->text());
	}

	public function test_edit_options_other()
	{
		$this->create_user('anothertestuser');
		$this->login('anothertestuser');
		$crawler = self::request('GET', "posting.php?mode=edit&f=2&p=1&sid={$this->sid}");
		$this->assertNotContainsLang('OVERWRITE_SUBJECTS', $crawler->filter('html')->text());
	}

	public function test_smart_subjects()
	{
		$this->login();
		$this->admin_login();

		// Create a topic with some reply posts
		$post1 = $this->create_topic(2, 'Smart Subjects Automated Test', 'This is a test post posted by the testing framework.');
		$post2 = $this->create_post(2, $post1['topic_id'], 'Re: Smart Subjects Automated Test', 'This is a test post posted by the testing framework.');
		$post3 = $this->create_post(2, $post1['topic_id'], 'Custom Foo Bar Subject', 'This is a test post posted by the testing framework.');

		// Generate post_id for first post since create_topic() does not give us the post's id
		$post1['post_id'] = $post2['post_id'] - 1;

		// Edit first post
		$this->edit_post(2, $post1['post_id'], 'Edited Subject Test', 'This is an edited test post posted by the testing framework.');

		// Check the results
		$crawler = self::request('GET', "viewtopic.php?p={$post1['post_id']}&sid={$this->sid}");
		$this->assertContains('Edited Subject Test', $crawler->filter("#post_content{$post1['post_id']} > h3 > a")->text());
		$this->assertContains('Re: Edited Subject Test', $crawler->filter("#post_content{$post2['post_id']} > h3 > a")->text());
		$this->assertContains('Custom Foo Bar Subject', $crawler->filter("#post_content{$post3['post_id']} > h3 > a")->text());

		// Edit first post again, with overwrite mode
		$this->edit_post(2, $post1['post_id'], 'Re-Edited Subject Test', 'This is an edited test post posted by the testing framework.', array('overwrite_subjects' => true));

		// Check the results
		$crawler = self::request('GET', "viewtopic.php?p={$post1['post_id']}&sid={$this->sid}");
		$this->assertContains('Re-Edited Subject Test', $crawler->filter("#post_content{$post1['post_id']} > h3 > a")->text());
		$this->assertContains('Re: Re-Edited Subject Test', $crawler->filter("#post_content{$post2['post_id']} > h3 > a")->text());
		$this->assertContains('Re: Re-Edited Subject Test', $crawler->filter("#post_content{$post3['post_id']} > h3 > a")->text());
	}

	/**
	 * Edit a post
	 *
	 * @param int $forum_id
	 * @param int $post_id
	 * @param string $subject
	 * @param string $message
	 * @param array $additional_form_data Any additional form data to be sent in the request
	 * @param string $expected Lang var of expected message after posting
	 * @return array|null post_id, topic_id if message is empty
	 */
	protected function edit_post($forum_id, $post_id, $subject, $message, array $additional_form_data = array(), $expected = '')
	{
		$posting_url = "posting.php?mode=edit&f={$forum_id}&p={$post_id}&sid={$this->sid}";

		$form_data = array_merge(array(
			'subject'		=> $subject,
			'message'		=> $message,
			'post'			=> true,
		), $additional_form_data);

		return self::submit_post($posting_url, 'EDIT_POST', $form_data, $expected);
	}
}
