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

class listener_base extends \phpbb_database_test_case
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vse\smartsubjects\event\main_listener */
	protected $listener;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	static protected function setup_extensions()
	{
		return array('vse/smartsubjects');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/posts.xml');
	}

	/**
	 * Setup test environment
	 */
	public function setUp()
	{
		parent::setUp();

		// Mock extension manager for the user class
		global $phpbb_extension_manager, $phpbb_root_path;
		$phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);

		$this->db = $this->new_dbal();
		$this->auth = $this->getMock('\phpbb\auth\auth');
		$this->request = $this->getMock('\phpbb\request\request');
		$this->user = new \phpbb\user('\phpbb\datetime');
	}

	/**
	 * Create our event listener
	 */
	protected function set_listener()
	{
		$this->listener = new \vse\smartsubjects\event\main_listener(
			$this->auth,
			$this->db,
			$this->request,
			$this->user,
			'phpbb_forums',
			'phpbb_posts'
		);
	}
}
