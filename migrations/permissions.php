<?php
/**
 *
 * Smart Subjects
 *
 * @copyright (c) 2015 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\smartsubjects\migrations;

class permissions extends \phpbb\db\migration\migration
{
	/**
	 * @inheritdoc
	 */
	public function effectively_installed()
	{
		$sql = 'SELECT * FROM ' . $this->table_prefix . "acl_options
			WHERE auth_option = 'f_smart_subjects'";
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row !== false;
	}

	/**
	 * @inheritdoc
	 */
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	/**
	 * @inheritdoc
	 */
	public function update_data()
	{
		return array(
			// Add permission
			array('permission.add', array('f_smart_subjects', false)),

			// Set permission roles
			array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_smart_subjects')),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED', 'f_smart_subjects')),
			array('permission.permission_set', array('ROLE_FORUM_LIMITED_POLLS', 'f_smart_subjects')),
			array('permission.permission_set', array('ROLE_FORUM_POLLS', 'f_smart_subjects')),
			array('permission.permission_set', array('ROLE_FORUM_STANDARD', 'f_smart_subjects')),
		);
	}
}
