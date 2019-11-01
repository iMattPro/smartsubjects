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

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \vse\smartsubjects\event\main_listener */
	protected $listener;

	/** @var \PHPUnit_Framework_MockObject_MockObject|\phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	protected static function setup_extensions()
	{
		return array('vse/smartsubjects');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/posts.xml');
	}

	/**
	 * Setup test environment
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Mock extension manager for the user class
		global $phpbb_extension_manager, $phpbb_root_path, $phpEx;
		$phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);

		$this->db = $this->new_dbal();
		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();
		$this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();

		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$lang_loader->set_extension_manager($phpbb_extension_manager);
		$this->lang = new \phpbb\language\language($lang_loader);
		$this->user = new \phpbb\user($this->lang, '\phpbb\datetime');
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
