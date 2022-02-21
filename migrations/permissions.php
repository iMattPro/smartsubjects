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
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v310\gold'];
	}

	/**
	 * @inheritdoc
	 */
	public function update_data()
	{
		return [
			// Add permission
			['permission.add', ['f_smart_subjects', false]],

			// Set permission roles
			['permission.permission_set', ['ROLE_FORUM_FULL', 'f_smart_subjects']],
			['permission.permission_set', ['ROLE_FORUM_LIMITED', 'f_smart_subjects']],
			['permission.permission_set', ['ROLE_FORUM_LIMITED_POLLS', 'f_smart_subjects']],
			['permission.permission_set', ['ROLE_FORUM_POLLS', 'f_smart_subjects']],
			['permission.permission_set', ['ROLE_FORUM_STANDARD', 'f_smart_subjects']],
		];
	}
}
