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
	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \vse\smartsubjects\event\main_listener */
	protected $listener;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\request\request */
	protected $request;

	protected static function setup_extensions()
	{
		return ['vse/smartsubjects'];
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/fixtures/posts.xml');
	}

	/**
	 * Setup test environment
	 */
	protected function setUp(): void
	{
		parent::setUp();

		// Mock extension manager for the user class
		global $phpbb_extension_manager, $phpbb_root_path, $phpEx;
		$phpbb_extension_manager = new \phpbb_mock_extension_manager($phpbb_root_path);

		$this->db = $this->new_dbal();
		$this->auth = $this->createMock('\phpbb\auth\auth');
		$this->request = $this->createMock('\phpbb\request\request');

		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$lang_loader->set_extension_manager($phpbb_extension_manager);
		$this->lang = new \phpbb\language\language($lang_loader);
	}

	/**
	 * Create our event listener
	 */
	protected function set_listener()
	{
		$this->listener = new \vse\smartsubjects\event\main_listener(
			$this->auth,
			$this->db,
			$this->lang,
			$this->request,
			'phpbb_forums',
			'phpbb_posts'
		);
	}
}
