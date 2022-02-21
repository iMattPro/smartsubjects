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
		return [
			[
				2,
				true,
				true,
				[],
				['S_SMART_SUBJECTS_MOD' => true],
			],
			[
				2,
				false,
				false,
				[],
				['S_SMART_SUBJECTS_MOD' => false],
			],
			[
				2,
				true,
				false,
				[],
				['S_SMART_SUBJECTS_MOD' => false],
			],
			[
				2,
				false,
				true,
				[],
				['S_SMART_SUBJECTS_MOD' => false],
			],
			[
				3,
				true,
				true,
				['FOO_BAR' => 'foo bar'],
				['FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => true],
			],
			[
				3,
				false,
				false,
				['FOO_BAR' => 'foo bar'],
				['FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => false],
			],
			[
				3,
				true,
				false,
				['FOO_BAR' => 'foo bar'],
				['FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => false],
			],
			[
				3,
				false,
				true,
				['FOO_BAR' => 'foo bar'],
				['FOO_BAR' => 'foo bar', 'S_SMART_SUBJECTS_MOD' => false],
			],
		];
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
		$acl_get_map = [
			['f_smart_subjects', $fid, $forum_auth],
			['m_', $fid, $mod_auth],
		];

		$this->auth->expects(self::atLeastOnce())
			->method('acl_get')
			->with(self::stringContains('_'), self::anything())
			->willReturnMap($acl_get_map);

		$data = new \phpbb\event\data([
			'page_data'	=> $data,
			'forum_id'	=> $fid,
		]);

		$this->set_listener();

		$this->listener->setup($data);

		self::assertSame($data['page_data'], $expected);

		// Verify the lang file is loaded
		self::assertTrue($this->lang->is_set('OVERWRITE_SUBJECTS'));
	}
}
