<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
	define ('AUTHENTICATION_REGISTER_URL',					 'Register');
	define ('AUTHENTICATION_ACTIVATE_URL',					 'Activate');
	define ('AUTHENTICATION_YAHOO_BB_AUTH',                  'YahooBBOK');
	define ('AUTHENTICATION_PROFILE_URL',                    'Profile');
	define ('AUTHENTICATION_ACTION_URL',                     'Action');
	define ('AUTHENTICATION_LOGOUT_URL',                     'Log Out');
	define ('AUTHENTICATION_STATUS_URL',                     'Status');
	define ('AUTHENTICATION_STATUS_OK_URL',                  'Ok');
	define ('AUTHENTICATION_HASH_URL',                       'Hash');

    define ('AUTHENTICATION_LOGIN_FORM',            		 'Administrator! Not authenticated!');
    define ('AUTHENTICATION_USERNAME',              		 'Username');
    define ('AUTHENTICATION_PASSWORD',              		 'Password');
    define ('AUTHENTICATION_LOGIN_GO',              		 'Go!');
    define ('AUTHENTICATION_LOG_OUT_TEXT',                   'Log Out');

    define ('AUTHENTICATION_ACCESS_DENIED',         		 'Could not authorize you! Access denied!');
    define ('AUTHENTICATION_MANAGE_ZONES',             		 'Zones');
    define ('AUTHENTICATION_MANAGE_GROUPS',                  'Groups');
    define ('AUTHENTICATION_MANAGE_USERS',                   'Users');
    define ('AUTHENTICATION_MANAGE_ZONES_MAPPING',           'ACLs for groups');
    define ('AUTHENTICATION_MANAGE_ZONES_MAPPING_FOR_USERS', 'ACLs for users');
    define ('AUTHENTICATION_MANAGE_CONFIGURATION',  		 'Configuration');

    define ('CANNOT_ERASE_AUTHENTICATION_GROUP',    		 'Erasing the "Administrators" group not allowed!');
    define ('CANNOT_DELETE_ADMINISTRATOR_USER',     		 'Erasing the administrator is not allowed!');
    define ('CANNOT_DELETE_GROUP_WITH_USERS',       		 'Group contains users and cannot be erased!');

    define ('GROUP_NAME_CANNOT_BE_EMPTY',           		 'Group name cannot be empty! Fill in a name for the group!');
    define ('GROUP_ALREADY_EXISTS',                 		 'Group already exists! Groups cannot have the same name at once!');
    define ('ADD_NEW_GROUP_OF_USERS',               		 'Add a new group of users');

    define ('AUTHENTICATION_GROUP_NAME_LABEL',     			 'Group name');
    define ('AUTHENTICATION_AS_A',                  		 'As a');
    define ('AUTHENTICATION_OF_GROUP',              		 'Of group');
    define ('AUTHENTICATION_GROUP_CHILD',           		 'first child');
    define ('AUTHENTICATION_GROUP_LAST_CHILD',               'last child');
    define ('AUTHENTICATION_GROUP_BROTHER',                  'previous brother');
    define ('AUTHENTICATION_GROUP_NEXT_BROTHER',             'next brother');
    define ('AUTHENTICATION_GROUP_MOVED_TO_CHILD',           'Group cannot be moved to a child of it!');
    define ('AUTHENTICATION_ADD_GROUP',                      'Add group');
    define ('AUTHENTICATION_SHOW_ALL_CATEGORIES',            'Show all groups');

    define ('AUTHENTICATION_EDIT_GROUP',                     'Edit group');
    define ('CANNOT_DELETE_MAPPED_GROUPS',          		 'Group maps to a zone. Cannot erase group!');
    define ('CANNOT_DELETE_MAPPED_USERS',           		 'User maps to a zone. Cannot erase user!');
    define ('CANNOT_DELETE_MAPPED_ZONE',            		 'Zone maps either to a group or user. Cannot erase zone!');

    define ('AUTHENTICATION_USERNAME_IS_MANDATORY',          'Username field is empty! This is mandatory!');
    define ('AUTHENTICATION_USER_PASSWORDS_DONT_MATCH',      'The two passwords do not match!');

    define ('AUTHENTICATION_ARTICLES_BY_USER',				 'User is an author to a few articles. Cannot erase him ...');
    define ('AUTHENTICATION_ARTICLES_COMMENTS_BY_USER',      'User has commented on articles. Cannot erase him ...');
    define ('AUTHENTICATION_TEXTS_BY_USER',					 'User is an author to a few texts. Cannot erase him ...');
    define ('AUTHENTICATION_TEXTS_COMMENTS_BY_USER',         'User has commented on texts. Cannot erase him ...');
    define ('AUTHENTICATION_FILES_BY_USER',                  'User has approved uploaded files. Cannot erase him ...');
    define ('AUTHENTICATION_FILES_COMMENTS_BY_USER',         'User has commented on files. Cannot erase him ...');

    define ('AUTHENTICATION_PROFILE_EDIT',                   'Edit profile');
    define ('AUTHENTICATION_PROFILE_ADD',                    'Add profile');
    define ('AUTHENTICATION_PROFILE_USERNAME',               'Username');
    define ('AUTHENTICATION_PROFILE_PASSWORD',               'Password');
    define ('AUTHENTICATION_PROFILE_PASSWORD_CONFIRM',       'Confirm Pwd.');
    define ('AUTHENTICATION_PROFILE_EMAIL',                  'E-Mail');
    define ('AUTHENTICATION_PROFILE_PHONE',                  'Phone');
    define ('AUTHENTICATION_PROFILE_LAST_NAME',              'Last name');
    define ('AUTHENTICATION_PROFILE_FIRST_NAME',             'First name');
    define ('AUTHENTICATION_PROFILE_COUNTRY',                'Country');
    define ('AUTHENTICATION_PROFILE_GROUP',                  'Group');
    define ('AUTHENTICATION_PROFILE_ACTIVATED',              'Activated');
    define ('AUTHENTICATION_PROFILE_ACTIVATED_YES',          'Yes');
    define ('AUTHENTICATION_PROFILE_ACTIVATED_NO',           'No');
    define ('AUTHENTICATION_PROFILE_SIGNATURE',              'Signature');
    define ('AUTHENTICATION_PROFILE_DESCRIPTION',            'Description');
    define ('AUTHENTICATION_PROFILE_YM',                     'YM!');
    define ('AUTHENTICATION_PROFILE_MSN',                    'MSN');
    define ('AUTHENTICATION_PROFILE_ICQ',                    'ICQ');
    define ('AUTHENTICATION_PROFILE_AOL',                    'AOL');
    define ('AUTHENTICATION_PROFILE_CITY',                   'City');
    define ('AUTHENTICATION_PROFILE_AVATAR',                 'Avatar');

    define ('AUTHENTICATION_USERNAME_ALREADY_EXISTS',        'Username must be unique ...');
    define ('AUTHENTICATION_EMAIL_ALREADY_EXISTS',           'E-mail must be unique ...');
    define ('AUTHENTICATION_PHONE_TEN_CHARS',                'Phone # must have exactly 10 chars. (ex. 0314014590) ...');
    define ('AUTHENTICATION_INVALID_EMAIL',                  'Invalid e-mail address ...');

    define ('AUTHENTICATION_SEARCH_FIELD_IS_EMPTY',          'Search is empty!');
    define ('AUTHENTICATION_SEARCH_USER_BY',                 'Search');
    define ('AUTHENTICATION_SEARCH_USER_IN',                 'In');

    define ('AUTHENTICATION_ADD_ZONE',                       'Add zone');
    define ('AUTHENTICATION_EDIT_ZONE',                      'Edit zone');
    define ('AUTHENTICATION_ZONE_NAME_CANNOT_BE_EMPTY',      'Zone name must not be empty ...');
    define ('AUTHENTICATION_ZONE_NAME',                      'Zone');
    define ('AUTHENTICATION_ZONE_PRICE',                     'Price');
    define ('AUTHENTICATION_ZONE_DESCRIPTION',               'Description');

    define ('AUTHENTICATION_ADD_ACL',                        'Add ACL');
    define ('AUTHENTICATION_EDIT_ACL',                       'Edit ACL');
    define ('AUTHENTICATION_ACL_ENTITY',                     'Group or user');
    define ('AUTHENTICATION_ACL_ACCESS_TYPE',                'Access type');
    define ('AUTHENTICATION_ACL_ALLOWED',                    'ALLOWED');
    define ('AUTHENTICATION_ACL_DENIED',                     'DENIED');

    define ('AUTHENTICATION_PAGE_REGISTER_MESSAGE',          'Content');
    define ('AUTHENTICATION_PAGE_REGISTER_TITLE',            'Title');
    define ('AUTHENTICATION_CONFIG_DEFAULT',                 'Default');
    define ('AUTHENTICATION_REG_PAGE_FORM_CONTENT',			 'Message shown to user on the register page ...');
    define ('AUTHENTICATION_REG_PAGE_FORM_SUCCESS_CONTENT',	 'Message shown to user upon succesfull registration ...');
    define ('AUTHENTICATION_REG_PAGE_FORM_ACTIVATE_CONTENT', 'Message shown to user upon activation ...');
    define ('AUTHENTICATION_REG_PAGE_FORM_ACTIVATE_NOTOK',   'Message shown to user if activation is not ok ...');
?>