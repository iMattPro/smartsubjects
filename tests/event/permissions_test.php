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

class permissions_test extends listener_base
{
	public function add_permissions_test_data()
	{
		return array(
			array(
				array(),
				array(
					'f_smart_subjects' => array('lang' => 'ACL_F_SMART_SUBJECTS', 'cat' => 'post'),
				),
			),
			array(
				array(
					'a_foo' => array('lang' => 'ACL_A_FOO', 'cat' => 'misc'),
				),
				array(
					'a_foo' => array('lang' => 'ACL_A_FOO', 'cat' => 'misc'),
					'f_smart_subjects' => array('lang' => 'ACL_F_SMART_SUBJECTS', 'cat' => 'post'),
				),
			),
		);
	}

	/**
	 * @dataProvider add_permissions_test_data
	 * @param $data
	 * @param $expected
	 */
	public function test_add_permissions($data, $expected)
	{
		$data = new \phpbb\event\data(array(
			'permissions'	=> $data
		));

		$this->set_listener();

		$this->listener->add_permission($data);

		$this->assertSame($data['permissions'], $expected);
	}
}
