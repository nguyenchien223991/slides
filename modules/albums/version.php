<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 18 Jun 2014 13:03:01 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$module_version = array(
		'name' => 'Albums',
		'modfuncs' => 'main,detail,search,add-student',
		'submenu' => 'main,detail,search,add-student',
		'is_sysmod' => 0,
		'virtual' => 1,
		'version' => '4.0.0',
		'date' => 'Wed, 18 Jun 2014 13:03:01 GMT',
		'author' => 'VINADES (contact@vinades.vn)',
		'uploads_dir' => array($module_name,$module_name.'/thumb',$module_name.'/tmp'),
		'note' => ''
	);