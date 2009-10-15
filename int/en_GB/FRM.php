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

    Author: Catalin Z. Alexandru;
*/
    define ('INVALID_INPUT_TYPE_OR_NOT_RECOGNIZED',         'You asked the form generator to build an invalid input type.');
    define ('INVALID_INPUT_TYPE_OR_NOT_RECOGNIZED_FIX',     'Make sure you do not have a typo in your code!');
    define ('PHP_REGEXP_CHECK_ERRMSG',                      'Error message for the PHP reg-exp check not defined!');
    define ('PHP_REGEXP_CHECK_ERRMSG_FIX',                  'Define an error message to be shown upon error!');
    define ('INPUT_NAME_NOT_SET',                           'You did not define a name="" attribute for the input!');
    define ('INPUT_NAME_NOT_SET_FIX',                       'Please define it, before trying to assing a value!');
    define ('CANNOT_UPDATE_NON_EXISTING_ID',                'You cannot make an update form, on a non-existent id!');
    define ('CANNOT_UPDATE_NON_EXISTING_ID_FIX',            'Please make sure that data is entered previous to update!');
    define ('UPLOAD_ERROR',                                 'Something weird happened during file upload.');
    define ('UPLOAD_ERROR_FIX',                             'Check permissions, available space or file it as a bug!');
    define ('CANNOT_SET_MULTIPLE_ON_NON_SELECT',            'Cannot set multiple="" attribute on non-select input!');
    define ('CANNOT_SET_MULTIPLE_ON_NON_SELECT_FIX',        'Fix this bug before going further!');
    define ('CANNOT_SET_ACCEPT_ON_NON_FILE',                'Cannot set accept="" attribute on non-file input!');
    define ('CANNOT_SET_ACCEPT_ON_NON_FILE_FIX',            'This attribute can be set only on file inputs!');
    define ('CANNOT_SET_ALT_ON_NON_IMAGE',                  'Cannot set alt="" attribute on non-image input!');
    define ('CANNOT_SET_ALT_ON_NON_IMAGE_FIX',              'This attribute can be set only on image inputs!');
    define ('CANNOT_SET_SRC_ON_NON_IMAGE',                  'Cannot set src="" attribute on non-image input!');
    define ('CANNOT_SET_SRC_ON_NON_IMAGE_FIX',              'This attribute can be set only on image inputs!');
    define ('CANNOT_SET_CHK_ON_NON_CHKRADIO',               'Cannot set checked on non-radio or non-checkbox input!');
    define ('CANNOT_SET_CHK_ON_NON_CHKRADIO_FIX',           'This attribute can be set only on radio or checkbox inputs!');
    define ('INVALID_FILE_TYPE',                            'Invalid file type! Accepted files:');
    define ('UNKNOWN_FILE_TYPE_WAS_UPLOADED',               'An unknown file type was uploaded!');
    define ('UPLOAD_CANNOT_WRITE_DISK',                     'Upload cannot write to disk! Contact administrators!');
    define ('UPLOAD_EMPTY_FILE_SPECIFIED',                  'You did not specify a file!');
    define ('UPLOAD_WAS_PARTIAL',                           'Only a partial upload was done. Please re-upload!');
    define ('UPLOADED_FILE_EXCEEDS_MAXFILESIZE',            'The size of the uploaded file exceeds our limit!');
    define ('UPLOADED_FILE_EXCEEDS_INI_SIZE',               'The size of the uploaded file exceeds our server limit!');
    define ('UPLOAD_DIR_NOT_SPECIFIED',                     'The upload dir was not specified!');
    define ('UPLOAD_DIR_NOT_SPECIFIED_FIX',                 'Please specify an upload dir if you have file inputs!');
    define ('CANNOT_USE_FILE_CONTROLLER',                   'The controller can only be used on file inputs!');
    define ('CANNOT_USE_FILE_CONTROLLER_FIX',               'Please make sure you use it only on file inputs!');
    define ('FORM_METHOD_IS_INVALID',                       'Form method is invalid!');
    define ('FORM_METHOD_IS_INVALID_FIX',                   'Please specify either a POST or GET method. No other!');

    # Define messages, that are LONG ...
    define ('UPLOAD_CHECKBOX_TOOLTIP',                      'By default, we do make UPLOAD inputs non-required. This means that
    if you need to upload an image, file, etc. you need to check this box here, and the file upload input will be enabled for you,
    giving you the possibility to choose a file from your computer ...');
?>