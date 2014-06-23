<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 18 Jun 2014 10:00:53 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );

$submenu['main'] = $lang_module['main'];
$submenu['add_author'] = $lang_module['add_author'];
$submenu['add_book'] = $lang_module['add_book'];
$submenu['add_student'] = $lang_module['add_student'];
$submenu['add_publisher'] = $lang_module['add_publisher'];
$submenu['add_toppic'] = $lang_module['add_toppic'];
$submenu['add_librarians'] = $lang_module['add_librarians'];
$submenu['list_borrow'] = $lang_module['list_borrow'];
$submenu['list_pay'] = $lang_module['list_pay'];
$submenu['list_firtbook'] = $lang_module['list_firtbook'];
$submenu['config'] = $lang_module['config'];

$allow_func = array( 'main', 'add_author', 'add_book', 'add_student', 'add_publisher', 'add_toppic', 'add_librarians', 'list_borrow', 'list_pay', 'list_firtbook', 'config');

define( 'NV_IS_FILE_ADMIN', true );

?>