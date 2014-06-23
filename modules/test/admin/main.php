<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 11 Jun 2014 12:29:41 GMT
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

//$tieude = $nv_Request->get_title( 'tieude', 'post', '' );
//$luachon = $nv_Request->get_int( 'tieude', 'post', '' );


$title = $nv_Request->get_title('title', 'post, get, ','');
$content=$nv_Request->get_textarea('content', 'post', 'get', '');
$date=$nv_Request->get_int('date', 'post, get', '');

if(!empty($title))
{
	$sql='INSERT INTO `test`(`id`, `title`, `content`) VALUES (NULL , :title, :content)';
	$str=$db->prepare($sql);
	$str->bindParam('title', $title);
	$str->bindParam('content', $content);
	$str->execute();
	
}

$xtpl = new XTemplate( $op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
//$xtpl->assign( 'TITLE', $tieude );
//$xtpl->assign( 'LUACHON', $luachon );
$xtpl->assign('TITLE', $title);
$xtpl->assign('CONTENT',$content);
$xtpl->assign('DATE', $date);


$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$page_title = $lang_module['main'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';