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

class listener_test extends listener_base
{
	public function test_construct()
	{
		$this->set_listener();
		$this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.permissions',
			'core.posting_modify_template_vars',
			'core.posting_modify_submit_post_after',
		), array_keys(\vse\smartsubjects\event\main_listener::getSubscribedEvents()));
	}
}
