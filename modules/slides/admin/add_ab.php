<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Fri, 20 Jun 2014 06:29:45 GMT
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if ( $nv_Request->isset_request( 'get_alias_title', 'post' ) )
{
	$alias = $nv_Request->get_title( 'get_alias_title', 'post', '' );
	$alias = change_alias( $alias );
	die( $alias );
}

if( $nv_Request->isset_request( 'ajax_action', 'post' ) )
{
	$slideid = $nv_Request->get_int( 'slideid', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $slideid;
	if( $new_vid > 0 )
	{
		$sql = 'SELECT slideid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slide WHERE slideid!=' . $slideid . ' ORDER BY weight ASC';
		$result = $db->query( $sql );
		$weight = 0;
		while( $row = $result->fetch() )
		{
			++$weight;
			if( $weight == $new_vid ) ++$weight;
			$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_slide SET weight=' . $weight . ' WHERE slideid=' . $row['slideid'];
			$db->query( $sql );
		}
		$sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_slide SET weight=' . $new_vid . ' WHERE slideid=' . $slideid;
		$db->query( $sql );
		$content = 'OK_' . $slideid;
	}
	nv_del_moduleCache( $module_name );
	include NV_ROOTDIR . '/includes/header.php';
	echo $content;
	include NV_ROOTDIR . '/includes/footer.php';
	exit();
}
if ( $nv_Request->isset_request( 'delete_slideid', 'get' ) and $nv_Request->isset_request( 'delete_checkss', 'get' ))
{
	$slideid = $nv_Request->get_int( 'delete_slideid', 'get' );
	$delete_checkss = $nv_Request->get_string( 'delete_checkss', 'get' );
	if( $slideid > 0 and $delete_checkss == md5( $slideid . NV_CACHE_PREFIX . $client_info['session_id'] ) )
	{
		$weight=0;
		$sql = 'SELECT weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slide WHERE slideid =' . $db->quote( $slideid );
		$result = $db->query( $sql );
		list( $weight) = $result->fetch( 3 );
		
		$db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slide  WHERE slideid = ' . $db->quote( $slideid ) );
		if( $weight > 0)
		{
			$sql = 'SELECT slideid, weight FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slide WHERE weight >' . $weight;
			$result = $db->query( $sql );
			while(list( $slideid, $weight) = $result->fetch( 3 ))
			{
				$weight--;
				$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_slide SET weight=' . $weight . ' WHERE slideid=' . intval( $slideid ));
			}
		}
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
}

$row = array();
$error = array();
$row['slideid'] = $nv_Request->get_int( 'slideid', 'post,get', 0 );
if ( $nv_Request->isset_request( 'submit', 'post' ) )
{
	$row['name'] = $nv_Request->get_title( 'name', 'post', '' );
	$row['description'] = $nv_Request->get_string( 'description', 'post', '' );
	$row['path_img'] = $nv_Request->get_title( 'path_img', 'post', '' );
	if( is_file( NV_DOCUMENT_ROOT . $row['path_img'] ) )
	{
		$row['path_img'] = substr( $row['path_img'], strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' ) );
	}
	else
	{
		$row['path_img'] = '';
	}
	$row['active'] = $nv_Request->get_int( 'active', 'post', 0 );

	if( empty( $error ) )
	{
		try
		{
			if( empty( $row['slideid'] ) )
			{

				$row['alias'] = '';
				$row['createddate'] = 0;
				$row['num_photo'] = 0;
				$row['num_view'] = 0;

				$stmt = $db->prepare( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_slide (alias, name, description, createddate, num_photo, path_img, num_view, active, weight) VALUES (:alias, :name, :description, :createddate, :num_photo, :path_img, :num_view, :active, :weight)' );

				$stmt->bindParam( ':alias', $row['alias'], PDO::PARAM_STR );
				$stmt->bindParam( ':createddate', $row['createddate'], PDO::PARAM_INT );
				$stmt->bindParam( ':num_photo', $row['num_photo'], PDO::PARAM_INT );
				$stmt->bindParam( ':num_view', $row['num_view'], PDO::PARAM_INT );
				$weight = $db->query( 'SELECT max(weight) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slide' )->fetchColumn();
				$weight = intval( $weight ) + 1;
				$stmt->bindParam( ':weight', $weight, PDO::PARAM_INT );


			}
			else
			{
				$stmt = $db->prepare( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_slide SET name = :name, description = :description, path_img = :path_img, active = :active WHERE slideid=' . $row['slideid'] );
			}
			$stmt->bindParam( ':name', $row['name'], PDO::PARAM_STR );
			$stmt->bindParam( ':description', $row['description'], PDO::PARAM_STR, strlen($row['description']) );
			$stmt->bindParam( ':path_img', $row['path_img'], PDO::PARAM_STR );
			$stmt->bindParam( ':active', $row['active'], PDO::PARAM_INT );

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
elseif( $row['slideid'] > 0 )
{
	$row = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_slide WHERE slideid=' . $row['slideid'] )->fetch();
	if( empty( $row ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
}
else
{
	$row['slideid'] = 0;
	$row['name'] = '';
	$row['description'] = '';
	$row['path_img'] = '';
	$row['active'] = 0;
}
if( ! empty( $row['path_img'] ) and is_file( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $row['path_img'] ) )
{
	$row['path_img'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $row['path_img'];
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
		->from( '' . NV_PREFIXLANG . '_' . $module_data . '_slide' );

	if( ! empty( $q ) )
	{
		$db->where( 'name LIKE :q_name OR description LIKE :q_description OR path_img LIKE :q_path_img OR active LIKE :q_active' );
	}
	$sth = $db->prepare( $db->sql() );

	if( ! empty( $q ) )
	{
		$sth->bindValue( ':q_name', '%' . $q . '%' );
		$sth->bindValue( ':q_description', '%' . $q . '%' );
		$sth->bindValue( ':q_path_img', '%' . $q . '%' );
		$sth->bindValue( ':q_active', '%' . $q . '%' );
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
		$sth->bindValue( ':q_description', '%' . $q . '%' );
		$sth->bindValue( ':q_path_img', '%' . $q . '%' );
		$sth->bindValue( ':q_active', '%' . $q . '%' );
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
		$view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;slideid=' . $view['slideid'];
		$view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_slideid=' . $view['slideid'] . '&amp;delete_checkss=' . md5( $view['slideid'] . NV_CACHE_PREFIX . $client_info['session_id'] );
		$xtpl->assign( 'VIEW', $view );
		$xtpl->parse( 'main.view.loop' );
	}
	$xtpl->parse( 'main.view' );
}


$array_checkbox_active = array();

$array_checkbox_active[1] = $lang_global['yes'];
foreach( $array_checkbox_active as $key => $title )
{
	$xtpl->assign( 'OPTION', array(
		'key' => $key,
		'title' => $title,
		'checked' => ($key == $row['active']) ? ' checked="checked"' : ''
	) );
	$xtpl->parse( 'main.checkbox_active' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$page_title = $lang_module['add_ab'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';