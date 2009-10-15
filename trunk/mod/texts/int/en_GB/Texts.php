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
	# Define URLs;
	define ('TEXTS_TEXTS_URL',						'Text');
	define ('TEXTS_CATEGORY_URL',					'Category');
	define ('TEXTS_STATUS_URL',                     'Status');
	define ('TEXTS_STATUS_OK_URL',                  'Ok');

	# Define short messages;
    define ('MANAGE_TEXTS',                         'Texts');
    define ('MANAGE_TEXTS_CATEGORIES',              'Categories');
    define ('MANAGE_TEXTS_COMMENTS',				'Comments');
    define ('MANAGE_TEXTS_MOVE',                    'Operations');
    define ('MANAGE_TEXTS_CONFIG',                  'Configuration');

    define ('TEXTS_CATEGORY_NAME_CANNOT_BE_EMPTY',  'Category name cannot be empty!');
    define ('TEXTS_CATEGORY_ALREADY_EXISTS',        'Category already exists!');
    define ('TEXTS_CATEGORY_LAST_CHILD',            'last child');
    define ('TEXTS_CATEGORY_CHILD',                 'first child');
    define ('TEXTS_CATEGORY_BROTHER',               'previous brother');
    define ('TEXTS_CATEGORY_NEXT_BROTHER',          'next brother');
    define ('TEXTS_CATEGORY_MOVED_TO_CHILD',        'Cannot move this category to a child of it! It is illegal to do that!');
    define ('TEXTS_OF_CATEGORY',                    'Of category');
    define ('TEXTS_AS_A',                           'As a');

    define ('TEXTS_CAN_COMMENT',					'Can comment');
    define ('TEXTS_CAN_COMMENT_YES',				'Yes');
    define ('TEXTS_CAN_COMMENT_NO',					'No');

    define ('TEXTS_CATEGORY_NAME_LABEL',            'Category');
    define ('TEXTS_ADD_CATEGORY',                   'Add category');
    define ('TEXTS_EDIT_CATEGORY',                  'Edit category');
    define ('TEXTS_MOVE_ARTICLE',                   'Move texts');
    define ('TEXTS_OLD_CATEGORY',                   'Old category');
    define ('TEXTS_NEW_CATEGORY',                   'New category');

    define ('TEXTS_TITLE_CANNOT_BE_EMPTY',          'Title cannot be empty!');
    define ('TEXTS_CONTENT_CANNOT_BE_EMPTY',        'Content cannot be empty!');
    define ('TEXTS_ADD_ARTICLE',                    'Add text');
    define ('TEXTS_EDIT_ARTICLE',                   'Edit text');
    define ('TEXTS_UPDATE_CONFIGURATION',           'Update settings');
    define ('TEXTS_TITLE',                          'Title');
    define ('TEXTS_SEO',							'SEO (read-only)');
    define ('TEXTS_CONTENT',                        'Content');
    define ('TEXTS_TAGS',                           'Tags');
    define ('TEXTS_AUTHOR',                         'Author');
    define ('TEXTS_CANNOT_DELETE_CATEGORY_WA',      'Cannot delete category that contains articles!');
    define ('TEXTS_CONFIG_CHOOSE',                  'Choose');
    define ('TEXTS_COMMENT_APPROVED',				'Approved');
    define ('TEXTS_EDIT_COMMENT',					'Edit comment');
    define ('TEXTS_CONFIG_DO',                      'Do');
    define ('TEXTS_SEARCH_TITLE',                   'Title');
    define ('TEXTS_SEARCH_CONTENT',                 'Content');
    define ('TEXTS_SEARCH_BY',                      'Search');
    define ('TEXTS_SEARCH_IN',                      'In');
    define ('TEXTS_SHOW_ALL_CATEGORIES',            'Show ALL sections (categories)');
    define ('TEXTS_USER_MUST_BE_LOGGED_TO_COMMENT',	'Users should be logged in to comment ...');
    define ('TEXTS_PER_CATEGORY',					'How many texts should be shown per category ...');
    define ('TEXTS_PER_PAGE',						'Per page');
    define ('TEXTS_CONFIG_PER_PAGE_ERROR',			'Wrong input! You must enter a number ...');
    define ('TEXTS_COMMENT',						'Comment');
    define ('TEXTS_COMMENTS_NEED_DEFINED_TEXT',		'Comment widget needs to be invoked only on static text pages ...');
    define ('TEXTS_COMMENTS_NEED_DEFINED_TEXT_FIX',	'Do not use the comment widget on this page ...');
    define ('TEXTS_TITLE_MUST_BE_UNIQUE',           'Text identifier needs to be unique ...');
    define ('TEXTS_URL_MUST_BE_UNIQUE',             'The auto-generated URL must be unique ...');
    define ('TEXTS_COMMENT_HAS_BEEN_POSTED',        'Notification! A comment has been posted!');
?>