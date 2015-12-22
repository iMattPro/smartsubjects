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

class setup_test extends listener_base
{
	public function setup_test_data()
	{
		return array(
			array(
				true,
				array(),
				array('S_SMART_SUBJECTS' => true),
			),
			array(
				false,
				array(),
				array('S_SMART_SUBJECTS' => false),
			),
			array(
				true,
				array('FOO_BAR' => 'foo bar'),
				array('FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS' => true),
			),
			array(
				false,
				array('FOO_BAR' => 'foo bar'),
				array('FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS' => false),
			),
		);
	}

	/**
	 * @dataProvider setup_test_data
	 * @param $auth
	 * @param $data
	 * @param $expected
	 */
	public function test_setup($auth, $data, $expected)
	{
		$this->auth->expects($this->any())
			->method('acl_get')
			->with($this->stringContains('f_smart_subjects'), $this->anything())
			->will($this->returnValue($auth));

		$data = new \phpbb\event\data(array(
			'page_data'	=> $data
		));

		$this->set_listener();

		$this->listener->setup($data);

		$this->assertSame($data['page_data'], $expected);

		// Verify the lang file is loaded
		$this->assertArrayHasKey('OVERWRITE_SUBJECTS', $this->user->lang);
	}
}
