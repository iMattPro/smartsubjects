<?php
/**
 *
 * Smart Subjects
 *
 * @copyright (c) 2021 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\smartsubjects\tests;

class ext_test extends \phpbb_test_case
{
	public function test_ext()
	{
		/** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\DependencyInjection\ContainerInterface */
		$container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
			->disableOriginalConstructor()
			->getMock();

		/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\finder */
		$extension_finder = $this->getMockBuilder('\phpbb\finder')
			->disableOriginalConstructor()
			->getMock();

		/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\db\migrator */
		$migrator = $this->getMockBuilder('\phpbb\db\migrator')
			->disableOriginalConstructor()
			->getMock();

		$ext = new \vse\smartsubjects\ext(
			$container,
			$extension_finder,
			$migrator,
			'vse/smartsubjects',
			''
		);

		self::assertTrue($ext->is_enableable());
	}
}
