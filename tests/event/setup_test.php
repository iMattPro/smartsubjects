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
	public static function setup_test_data()
	{
		return [
			'allowed, overwrite requested' => [
				2,
				true,
				true,
				true,
				[],
				[
					'S_SMART_SUBJECTS_MOD'		=> true,
					'S_SMART_SUBJECTS_CHECKED'	=> true,
				],
			],
			'allowed, overwrite not requested' => [
				2,
				true,
				true,
				false,
				[],
				[
					'S_SMART_SUBJECTS_MOD'		=> true,
					'S_SMART_SUBJECTS_CHECKED'	=> false,
				],
			],
			'forum permission denied' => [
				2,
				false,
				true,
				true,
				[],
				[
					'S_SMART_SUBJECTS_MOD'		=> false,
					'S_SMART_SUBJECTS_CHECKED'	=> false,
				],
			],
			'moderator permission denied' => [
				2,
				true,
				false,
				true,
				[],
				[
					'S_SMART_SUBJECTS_MOD'		=> false,
					'S_SMART_SUBJECTS_CHECKED'	=> false,
				],
			],
			'existing page data, allowed and overwrite requested' => [
				3,
				true,
				true,
				true,
				['FOO_BAR' => 'foo bar'],
				[
					'FOO_BAR'					=> 'foo bar',
					'S_SMART_SUBJECTS_MOD'		=> true,
					'S_SMART_SUBJECTS_CHECKED'	=> true,
				],
			],
			'existing page data, allowed and overwrite not requested' => [
				3,
				true,
				true,
				false,
				['FOO_BAR' => 'foo bar'],
				[
					'FOO_BAR'					=> 'foo bar',
					'S_SMART_SUBJECTS_MOD'		=> true,
					'S_SMART_SUBJECTS_CHECKED'	=> false,
				],
			],
			'existing page data, forum permission denied' => [
				3,
				false,
				true,
				true,
				['FOO_BAR' => 'foo bar'],
				[
					'FOO_BAR'					=> 'foo bar',
					'S_SMART_SUBJECTS_MOD'		=> false,
					'S_SMART_SUBJECTS_CHECKED'	=> false,
				],
			],
			'existing page data, moderator permission denied' => [
				3,
				true,
				false,
				true,
				['FOO_BAR' => 'foo bar'],
				[
					'FOO_BAR'					=> 'foo bar',
					'S_SMART_SUBJECTS_MOD'		=> false,
					'S_SMART_SUBJECTS_CHECKED'	=> false,
				],
			],
		];
	}

	/**
	 * @dataProvider setup_test_data
	 * @param $fid
	 * @param $forum_auth
	 * @param $mod_auth
	 * @param $overwrite
	 * @param $page_data
	 * @param $expected
	 */
	public function test_setup($fid, $forum_auth, $mod_auth, $overwrite, $page_data, $expected)
	{
		$acl_get_map = [
			['f_smart_subjects', $fid, $forum_auth],
			['m_', $fid, $mod_auth],
		];

		$this->auth->expects(self::atLeastOnce())
			->method('acl_get')
			->with(self::stringContains('_'), self::anything())
			->willReturnMap($acl_get_map);

		$this->request->expects($forum_auth && $mod_auth ? self::once() : self::never())
			->method('is_set_post')
			->with(self::equalTo('overwrite_subjects'))
			->willReturn($overwrite);

		$data = new \phpbb\event\data([
			'page_data'	=> $page_data,
			'forum_id'	=> $fid,
		]);

		$this->set_listener();

		$this->listener->setup($data);

		self::assertSame($data['page_data'], $expected);

		// Verify the lang file is loaded
		self::assertTrue($this->lang->is_set('OVERWRITE_SUBJECTS'));
	}
}
