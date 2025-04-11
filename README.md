# Smart Subjects extension for phpBB

With this extension, editing the subject of the first post in a topic will update all subsequent posts with matching (Re:) subjects to the new subject.

[![Build Status](https://github.com/iMattPro/smartsubjects/actions/workflows/tests.yml/badge.svg)](https://github.com/iMattPro/smartsubjects/actions)
[![codecov](https://codecov.io/gh/iMattPro/smartsubjects/branch/master/graph/badge.svg?token=CAF93B29MK)](https://codecov.io/gh/iMattPro/smartsubjects)
[![Latest Stable Version](https://poser.pugx.org/vse/smartsubjects/v/stable)](https://www.phpbb.com/customise/db/extension/smart_subjects/)

## Features
* Changing the first post's subject (title) will update all __matching__ replies with the new subject (title).
* Admins and moderators have the option when editing a first post to update __all__ reply subjects (including non-matching subjects) to match the first post's subject.
* Forum based permissions allow you to control which forums and users can use Smart Subjects.

## Minimum Requirements
* phpBB 3.2.0 or newer
* PHP 5.4 or newer

## Install
1. Download and unzip the [latest release](https://www.phpbb.com/customise/db/extension/smart_subjects/) and copy it to the `ext` directory of your phpBB board.
2. Navigate in the ACP to `Customise -> Manage extensions`.
3. Look for `Smart Subjects` under the Disabled Extensions list and click its `Enable` link.

## Usage
* Forum based permissions can be configured to disable Smart Subjects in certain forums, or for certain users and usergroups in each forum. They can be found in `Forum Based Permissions -> Forum Permissions` under the `Post` group.

## Uninstall
1. Navigate in the ACP to `Customise -> Manage extensions`.
2. Click the `Disable` link for Smart Subjects.
3. To permanently uninstall, click `Delete Data`, then delete the `Smart Subjects` folder from `phpBB/ext/vse/`.

## License
[GNU General Public License v2](license.txt)

© 2015 - Matt Friedman
