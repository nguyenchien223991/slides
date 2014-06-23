<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 18 Jun 2014 13:03:01 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );

$submenu['main'] = $lang_module['main'];
$submenu['config'] = $lang_module['config'];
$submenu['add_student'] = $lang_module['add_student'];

$allow_func = array( 'main', 'config', 'add_student');

define( 'NV_IS_FILE_ADMIN', true );

?>