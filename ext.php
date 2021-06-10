<?php
/**
 *
 * Smart Subjects
 *
 * @copyright (c) 2021 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\smartsubjects;

use phpbb\extension\base;

class ext extends base
{
	/**
	 * {@inheritDoc}
	 */
	public function is_enableable()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');
	}
}
