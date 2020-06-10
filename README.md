# Smart Subjects extension for phpBB

With this extension, editing the subject of the first post in a topic will update all subsequent posts with matching (Re:) subjects to the new subject.

[![Build Status](https://travis-ci.org/iMattPro/smartsubjects.svg)](https://travis-ci.org/iMattPro/smartsubjects)

## Features
* Renaming the first post's subject will update all __matching__ replies to the new title
* Admins and moderators have the option when editing a first post to update __all__ reply subjects (including non-matching subjects) to match the first post's subject
* Forum based permissions allow you to control which forums and users can use Smart Subjects

## Minimum Requirements
* phpBB 3.1.0 or phpBB 3.2.0
* PHP 5.3.3

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
