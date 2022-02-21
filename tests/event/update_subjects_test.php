<?php
/**
 *
 * Smart Subjects
 *
 * @copyright (c) 2015 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\smartsubjects\tests\event;

class update_subjects_test extends listener_base
{
	/**
	 * Two models of event data to use in the tests
	 *
	 * @return array
	 */
	protected function import_topic_data()
	{
		return [
			1 => [
				'mode' 				=> 'edit',
				'update_subject'	=> true,
				'post_id'			=> 1,
				'topic_id'			=> 1,
				'forum_id'			=> 1,
				'data'				=> [
					'topic_title'			=> 'Test Topic 1',
					'topic_first_post_id'	=> 1,
					'topic_last_post_id'	=> 3,
				],
				'post_data' 		=> [
					'post_subject'	=> 'New Topic Title 1',
				],
			],

			2 => [
				'mode' 				=> 'edit',
				'update_subject'	=> true,
				'post_id'			=> 4,
				'topic_id'			=> 2,
				'forum_id'			=> 1,
				'data'				=> [
					'topic_title'			=> 'Test Topic 2',
					'topic_first_post_id'	=> 4,
					'topic_last_post_id'	=> 6,
				],
				'post_data' 		=> [
					'post_subject'	=> 'New Topic Title 2',
				],
			],
		];
	}

	/**
	 * Test data for test_update_subjects
	 *
	 * @return array
	 */
	public function update_subjects_test_data()
	{
		$data = $this->import_topic_data();

		return [
			[
				// standard title update
				$data[1],
				[
					['f_smart_subjects', 1, true],
				],
				false,
				[
					['post_id' => 2, 'post_subject' => 'Re: New Topic Title 1'],
					['post_id' => 3, 'post_subject' => 'Re: New Topic Title 1'],
				],
			],
			[
				// standard title update
				$data[2],
				[
					['f_smart_subjects', 1, true],
				],
				false,
				[
					['post_id' => 5, 'post_subject' => 'Re: New Topic Title 2'],
					['post_id' => 6, 'post_subject' => 'Custom Post Title'],
				],
			],
			[
				// update with overwrite mode on
				$data[2],
				[
					['f_smart_subjects', 1, true],
				],
				true,
				[
					['post_id' => 5, 'post_subject' => 'Re: New Topic Title 2'],
					['post_id' => 6, 'post_subject' => 'Re: New Topic Title 2'],
				],
			],
			[
				// not editing a post
				array_merge($data[1], ['mode' => 'post']),
				[
					['f_smart_subjects', 1, true],
				],
				false,
				[
					['post_id' => 2, 'post_subject' => 'Re: Test Topic 1'],
					['post_id' => 3, 'post_subject' => 'Re: Test Topic 1'],
				],
			],
			[
				// not updating a title
				array_merge($data[1], ['update_subject' => false]),
				[
					['f_smart_subjects', 1, true],
				],
				false,
				[
					['post_id' => 2, 'post_subject' => 'Re: Test Topic 1'],
					['post_id' => 3, 'post_subject' => 'Re: Test Topic 1'],
				],
			],
			[
				// not editing the first post post
				array_merge($data[1], ['post_id' => 2]),
				[
					['f_smart_subjects', 1, true],
				],
				false,
				[
					['post_id' => 1, 'post_subject' => 'Test Topic 1'],
					['post_id' => 3, 'post_subject' => 'Re: Test Topic 1'],
				],
			],
			[
				// unauthorized forum
				$data[1],
				[
					['f_smart_subjects', 2, false],
				],
				false,
				[
					['post_id' => 2, 'post_subject' => 'Re: Test Topic 1'],
					['post_id' => 3, 'post_subject' => 'Re: Test Topic 1'],
				],
			],

		];
	}

	/**
	 * Test the update_subjects method, check expected post subjects
	 *
	 * @dataProvider update_subjects_test_data
	 * @param $data
	 * @param $permissions
	 * @param $overwrite
	 * @param $expected
	 */
	public function test_update_subjects($data, $permissions, $overwrite, $expected)
	{
		// Set permission variable
		$this->auth->method('acl_get')
			->with(self::stringContains('_'), self::anything())
			->willReturnMap($permissions);

		// Set request variable
		$this->request->method('is_set_post')
			->with(self::equalTo('overwrite_subjects'))
			->willReturn($overwrite);

		// Define the event object
		$event = new \phpbb\event\data($data);

		// Set the listener object
		$this->set_listener();

		// Perform update subjects
		$this->listener->update_subjects($event);

		// Get the reply subjects now in the db
		$result = $this->db->sql_query('SELECT post_id, post_subject
			FROM phpbb_posts
			WHERE topic_id = ' . (int) $data['topic_id'] . '
				AND post_id <> ' . (int) $data['post_id'] . '
			ORDER BY post_id');
		self::assertEquals($expected, $this->db->sql_fetchrowset($result));
		$this->db->sql_freeresult($result);
	}

	/**
	 * Test data for test_update_forum_subject
	 *
	 * @return array
	 */
	public function update_forum_subject_test_data()
	{
		$data = $this->import_topic_data();

		return [
			[$data[1], 'Re: New Topic Title 1'], // forum subject is updated
			[$data[2], 'Re: Test Topic 1'], // forum subject is not updated
		];
	}

	/**
	 * Test the update_subjects method, check expected forum last post subject
	 *
	 * @dataProvider update_forum_subject_test_data
	 * @param $data
	 * @param $expected
	 */
	public function test_update_forum_subject($data, $expected)
	{
		// Set permission variable
		$this->auth->method('acl_get')
			->with(self::stringContains('f_smart_subjects'), self::anything())
			->willReturn(true);

		// Define the event object
		$event = new \phpbb\event\data($data);

		// Set the listener object
		$this->set_listener();

		// Perform update subjects
		$this->listener->update_subjects($event);

		// Get the last forum reply subject now in the db
		$result = $this->db->sql_query('SELECT forum_last_post_subject
			FROM phpbb_forums
			WHERE forum_id = ' . (int) $data['forum_id']);
		self::assertEquals($expected, $this->db->sql_fetchfield('forum_last_post_subject'));
		$this->db->sql_freeresult($result);
	}
}
