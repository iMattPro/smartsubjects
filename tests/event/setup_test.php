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
				2,
				true,
				true,
				array(),
				array('S_SMART_SUBJECTS_MOD' => true),
			),
			array(
				2,
				false,
				false,
				array(),
				array('S_SMART_SUBJECTS_MOD' => false),
			),
			array(
				2,
				true,
				false,
				array(),
				array('S_SMART_SUBJECTS_MOD' => false),
			),
			array(
				2,
				false,
				true,
				array(),
				array('S_SMART_SUBJECTS_MOD' => false),
			),
			array(
				3,
				true,
				true,
				array('FOO_BAR' => 'foo bar'),
				array('FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => true),
			),
			array(
				3,
				false,
				false,
				array('FOO_BAR' => 'foo bar'),
				array('FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => false),
			),
			array(
				3,
				true,
				false,
				array('FOO_BAR' => 'foo bar'),
				array('FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => false),
			),
			array(
				3,
				false,
				true,
				array('FOO_BAR' => 'foo bar'),
				array('FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => false),
			),
		);
	}

	/**
	 * @dataProvider setup_test_data
	 * @param $fid
	 * @param $forum_auth
	 * @param $mod_auth
	 * @param $data
	 * @param $expected
	 */
	public function test_setup($fid, $forum_auth, $mod_auth, $data, $expected)
	{
		$acl_get_map = array(
			array('f_smart_subjects', $fid, $forum_auth),
			array('m_', $fid, $mod_auth),
		);

		$this->auth->expects(self::atLeastOnce())
			->method('acl_get')
			->with(self::stringContains('_'), self::anything())
			->willReturnMap($acl_get_map);

		$data = new \phpbb\event\data(array(
			'page_data'	=> $data,
			'forum_id'	=> $fid,
		));

		$this->set_listener();

		$this->listener->setup($data);

		self::assertSame($data['page_data'], $expected);

		// Verify the lang file is loaded
		self::assertTrue($this->lang->is_set('OVERWRITE_SUBJECTS'));
	}
}
