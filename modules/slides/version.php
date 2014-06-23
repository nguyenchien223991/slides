<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Fri, 20 Jun 2014 06:27:47 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$module_version = array(
		'name' => 'Slides',
		'modfuncs' => 'main,detail,search',
		'submenu' => 'main,detail,search',
		'is_sysmod' => 0,
		'virtual' => 1,
		'version' => '4.0.0',
		'date' => 'Fri, 20 Jun 2014 06:27:48 GMT',
		'author' => 'VINADES (contact@vinades.vn)',
		'uploads_dir' => array($module_name,$module_name.'/thumb',$module_name.'/tmp'),
		'note' => ''
	);