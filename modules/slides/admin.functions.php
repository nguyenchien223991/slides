<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Fri, 20 Jun 2014 06:27:47 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );

$submenu['main'] = $lang_module['main'];
$submenu['add_ab'] = $lang_module['add_ab'];
$submenu['listimg'] = $lang_module['listimg'];
$submenu['add_img'] = $lang_module['add_img'];

$allow_func = array( 'main', 'add_ab', 'listimg', 'add_img');

define( 'NV_IS_FILE_ADMIN', true );

?>