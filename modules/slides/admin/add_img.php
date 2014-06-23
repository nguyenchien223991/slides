<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Fri, 20 Jun 2014 06:32:07 GMT
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( $nv_Request->isset_request( 'ajax_action', 'post' ) )
{
	$pictureid = $nv_Request->get_int( 'pictureid', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $pictureid;
	if( $new_vid > 0 )
	{
		$sql = 'SELECT pictureid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_picture WHERE pictureid!=' . $pictureid . ' ORDER BY weight ASC';
		$result = $db->query( $sql );
		$weight = 0;
		while( $row = $result->fetch() )
		{
			++$weight;
			if( $weight == $new_vid ) ++$weight;
			$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_picture SET weight=' . $weight . ' WHERE pictureid=' . $row['pictureid'];
			$db->query( $sql );
		}
		$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_picture SET weight=' . $new_vid . ' WHERE pictureid=' . $pictureid;
		$db->query( $sql );
		$content = 'OK_' . $pictureid;
	}
	nv_del_moduleCache( $module_name );
	include NV_ROOTDIR . '/includes/header.php';
	echo $content;
	include NV_ROOTDIR . '/includes/footer.php';
	exit();
}
if ( $nv_Request->isset_request( 'delete_pictureid', 'get' ) and $nv_Request->isset_request( 'delete_checkss', 'get' ))
{
	$pictureid = $nv_Request->get_int( 'delete_pictureid', 'get' );
	$delete_checkss = $nv_Request->get_string( 'delete_checkss', 'get' );
	if( $pictureid > 0 and $delete_checkss == md5( $pictureid . NV_CACHE_PREFIX . $client_info['session_id'] ) )
	{
		$weight=0;
		$sql = 'SELECT weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_picture WHERE pictureid =' . $db->quote( $pictureid );
		$result = $db->query( $sql );
		list( $weight) = $result->fetch( 3 );
		
		$db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_picture  WHERE pictureid = ' . $db->quote( $pictureid ) );
		if( $weight > 0)
		{
			$sql = 'SELECT pictureid, weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_picture WHERE weight >' . $weight;
			$result = $db->query( $sql );
			while(list( $pictureid, $weight) = $result->fetch( 3 ))
			{
				$weight--;
				$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_picture SET weight=' . $weight . ' WHERE pictureid=' . intval( $pictureid ));
			}
		}
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
}

$row = array();
$error = array();
$row['pictureid'] = $nv_Request->get_int( 'pictureid', 'post,get', 0 );
if ( $nv_Request->isset_request( 'submit', 'post' ) )
{
	$row['name'] = $nv_Request->get_title( 'name', 'post', '' );
	$row['slideid'] = $nv_Request->get_int( 'slideid', 'post', 0 );
	$row['path'] = $nv_Request->get_title( 'path', 'post', '' );
	if( is_file( NV_DOCUMENT_ROOT . $row['path'] ) )
	{
		$row['path'] = substr( $row['path'], strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' ) );
	}
	else
	{
		$row['path'] = '';
	}
	$row['description'] = $nv_Request->get_string( 'description', 'post', '' );

	if( empty( $error ) )
	{
		try
		{
			if( empty( $row['pictureid'] ) )
			{

				$row['numview'] = 0;
				$row['thumb_name'] = '';

				$stmt = $db->prepare( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_picture (name, slideid, path, description, numview, thumb_name, weight) VALUES (:name, :slideid, :path, :description, :numview, :thumb_name, :weight)' );

				$stmt->bindParam( ':numview', $row['numview'], PDO::PARAM_INT );
				$stmt->bindParam( ':thumb_name', $row['thumb_name'], PDO::PARAM_STR );
				$weight = $db->query( 'SELECT max(weight) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_picture' )->fetchColumn();
				$weight = intval( $weight ) + 1;
				$stmt->bindParam( ':weight', $weight, PDO::PARAM_INT );


			}
			else
			{
				$stmt = $db->prepare( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_picture SET name = :name, slideid = :slideid, path = :path, description = :description WHERE pictureid=' . $row['pictureid'] );
			}
			$stmt->bindParam( ':name', $row['name'], PDO::PARAM_STR );
			$stmt->bindParam( ':slideid', $row['slideid'], PDO::PARAM_INT );
			$stmt->bindParam( ':path', $row['path'], PDO::PARAM_STR );
			$stmt->bindParam( ':description', $row['description'], PDO::PARAM_STR, strlen($row['description']) );

			$exc = $stmt->execute();
			if( $exc )
			{
				Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
				die();
			}
		}
		catch( PDOException $e )
		{
			trigger_error( $e->getMessage() );
			die( $e->getMessage() ); //Remove this line after checks finished
		}
	}
}
elseif( $row['pictureid'] > 0 )
{
	$row = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_picture WHERE pictureid=' . $row['pictureid'] )->fetch();
	if( empty( $row ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
}
else
{
	$row['pictureid'] = 0;
	$row['name'] = '';
	$row['slideid'] = 0;
	$row['path'] = '';
	$row['description'] = '';
}
if( ! empty( $row['path'] ) and is_file( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $row['path'] ) )
{
	$row['path'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $row['path'];
}

$q = $nv_Request->get_title( 'q', 'post,get' );

// Fetch Limit
$show_view = false;
if ( ! $nv_Request->isset_request( 'id', 'post,get' ) )
{
	$show_view = true;
	$per_page = 5;
	$page = $nv_Request->get_int( 'page', 'post,get', 1 );
	$db->sqlreset()
		->select( 'COUNT(*)' )
		->from( '' . NV_PREFIXLANG . '_' . $module_data . '_picture' );

	if( ! empty( $q ) )
	{
		$db->where( 'name LIKE :q_name OR slideid LIKE :q_slideid OR path LIKE :q_path OR description LIKE :q_description' );
	}
	$sth = $db->prepare( $db->sql() );

	if( ! empty( $q ) )
	{
		$sth->bindValue( ':q_name', '%' . $q . '%' );
		$sth->bindValue( ':q_slideid', '%' . $q . '%' );
		$sth->bindValue( ':q_path', '%' . $q . '%' );
		$sth->bindValue( ':q_description', '%' . $q . '%' );
	}
	$sth->execute();
	$num_items = $sth->fetchColumn();

	$db->select( '*' )
		->order( 'weight ASC' )
		->limit( $per_page )
		->offset( ( $page - 1 ) * $per_page );
	$sth = $db->prepare( $db->sql() );

	if( ! empty( $q ) )
	{
		$sth->bindValue( ':q_name', '%' . $q . '%' );
		$sth->bindValue( ':q_slideid', '%' . $q . '%' );
		$sth->bindValue( ':q_path', '%' . $q . '%' );
		$sth->bindValue( ':q_description', '%' . $q . '%' );
	}
	$sth->execute();
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
$xtpl->assign( 'ROW', $row );
$xtpl->assign( 'Q', $q );

if( $show_view )
{
	$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
	if( ! empty( $q ) )
	{
		$base_url .= '&q=' . $q;
	}
	$xtpl->assign( 'NV_GENERATE_PAGE', nv_generate_page( $base_url, $num_items, $per_page, $page) );

	while( $view = $sth->fetch() )
	{
		for( $i = 1; $i <= $num_items; ++$i )
		{
			$xtpl->assign( 'WEIGHT', array(
				'key' => $i,
				'title' => $i,
				'selected' => ( $i == $view['weight'] ) ? ' selected="selected"' : '') );
			$xtpl->parse( 'main.view.loop.weight_loop' );
		}
		$view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;pictureid=' . $view['pictureid'];
		$view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_pictureid=' . $view['pictureid'] . '&amp;delete_checkss=' . md5( $view['pictureid'] . NV_CACHE_PREFIX . $client_info['session_id'] );
		$xtpl->assign( 'VIEW', $view );
		$xtpl->parse( 'main.view.loop' );
	}
	$xtpl->parse( 'main.view' );
}


$array_select_slideid = array();

$array_select_slideid[0] = $lang_global['no'];
$array_select_slideid[1] = $lang_global['yes'];
foreach( $array_select_slideid as $key => $title )
{
	$xtpl->assign( 'OPTION', array(
		'key' => $key,
		'title' => $title,
		'selected' => ($key == $row['slideid']) ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.select_slideid' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$page_title = $lang_module['add_img'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';