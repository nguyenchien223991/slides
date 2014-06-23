<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 18 Jun 2014 13:04:31 GMT
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
	$id_student = $nv_Request->get_int( 'id_student', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $id_student;
	if( $new_vid > 0 )
	{
		$sql = 'SELECT id_student FROM ' . $db_config['prefix'] . '_' . $module_data . '_student WHERE id_student!=' . $id_student . ' ORDER BY weight ASC';
		$result = $db->query( $sql );
		$weight = 0;
		while( $row = $result->fetch() )
		{
			++$weight;
			if( $weight == $new_vid ) ++$weight;
			$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_student SET weight=' . $weight . ' WHERE id_student=' . $row['id_student'];
			$db->query( $sql );
		}
		$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_student SET weight=' . $new_vid . ' WHERE id_student=' . $id_student;
		$db->query( $sql );
		$content = 'OK_' . $id_student;
	}
	nv_del_moduleCache( $module_name );
	include NV_ROOTDIR . '/includes/header.php';
	echo $content;
	include NV_ROOTDIR . '/includes/footer.php';
	exit();
}

if ( $nv_Request->isset_request( 'delete_id_student', 'get' ) and $nv_Request->isset_request( 'delete_checkss', 'get' ))
{
	$id_student = $nv_Request->get_int( 'delete_id_student', 'get' );
	$delete_checkss = $nv_Request->get_string( 'delete_checkss', 'get' );
	if( $id_student > 0 and $delete_checkss == md5( $id_student . NV_CACHE_PREFIX . $client_info['session_id'] ) )
	{
		$weight=0;
		$sql = 'SELECT weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_student WHERE id_student =' . $db->quote( $id_student );
		$result = $db->query( $sql );
		list( $weight) = $result->fetch( 3 );
		
		$db->query('DELETE FROM ' . $db_config['prefix'] . '_' . $module_data . '_student  WHERE id_student = ' . $db->quote( $id_student ) );
		if( $weight > 0)
		{
			$sql = 'SELECT id_student, weight FROM ' . $db_config['prefix'] . '_' . $module_data . '_student WHERE weight >' . $weight;
			$result = $db->query( $sql );
			while(list( $id_student, $weight) = $result->fetch( 3 ))
			{
				$weight--;
				$db->query( 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_student SET weight=' . $weight . ' WHERE id_student=' . intval( $id_student ));
			}
		}
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
}

$row = array();
$error = array();
$row['id_student'] = $nv_Request->get_int( 'id_student', 'post,get', 0 );
if ( $nv_Request->isset_request( 'submit', 'post' ) )
{
	$row['name_student'] = $nv_Request->get_title( 'name_student', 'post', '' );
	$row['alias'] = $nv_Request->get_title( 'alias', 'post', '' );
	$row['alias'] = ( empty($row['alias'] ))? change_alias( $row['title'] ) : change_alias( $row['alias'] );
	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string( 'datetime', 'post' ), $m ) )
	{
		$_hour = 0;
		$_min = 0;
		$row['datetime'] = mktime( $_hour, $_min, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$row['datetime'] = 0;
	}
	$row['address'] = $nv_Request->get_title( 'address', 'post', '' );

	if( empty( $row['name_student'] ) )
	{
		$error[] = $lang_module['error_required_name_student'];
	}
	elseif( empty( $row['address'] ) )
	{
		$error[] = $lang_module['error_required_address'];
	}

	if( empty( $error ) )
	{
		try
		{
			if( empty( $row['id_student'] ) )
			{

				$row['name_class'] = '';
				$row['science'] = '';
				$row['scientific'] = '';
				$row['timeout'] = 0;

				$stmt = $db->prepare( 'INSERT INTO ' . $db_config['prefix'] . '_' . $module_data . '_student (name_student, alias, datetime, address, name_class, science, scientific, timeout, weight) VALUES (:name_student, :alias, :datetime, :address, :name_class, :science, :scientific, :timeout, :weight)' );

				$stmt->bindParam( ':name_class', $row['name_class'], PDO::PARAM_STR );
				$stmt->bindParam( ':science', $row['science'], PDO::PARAM_STR );
				$stmt->bindParam( ':scientific', $row['scientific'], PDO::PARAM_STR );
				$stmt->bindParam( ':timeout', $row['timeout'], PDO::PARAM_INT );
				$weight = $db->query( 'SELECT max(weight) FROM ' . $db_config['prefix'] . '_' . $module_data . '_student' )->fetchColumn();
				$weight = intval( $weight ) + 1;
				$stmt->bindParam( ':weight', $weight, PDO::PARAM_INT );


			}
			else
			{
				$stmt = $db->prepare( 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_student SET name_student = :name_student, alias = :alias, datetime = :datetime, address = :address WHERE id_student=' . $row['id_student'] );
			}
			$stmt->bindParam( ':name_student', $row['name_student'], PDO::PARAM_STR );
			$stmt->bindParam( ':alias', $row['alias'], PDO::PARAM_STR );
			$stmt->bindParam( ':datetime', $row['datetime'], PDO::PARAM_INT );
			$stmt->bindParam( ':address', $row['address'], PDO::PARAM_STR );

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
elseif( $row['id_student'] > 0 )
{
	$row = $db->query( 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_student WHERE id_student=' . $row['id_student'] )->fetch();
	if( empty( $row ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
}
else
{
	$row['id_student'] = 0;
	$row['name_student'] = '';
	$row['alias'] = '';
	$row['datetime'] = 0;
	$row['address'] = '';
}

if( empty( $row['datetime'] ) )
{
	$row['datetime'] = '';
}
else
{
	$row['datetime'] = date( 'd/m/Y', $row['datetime'] );
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
		->from( '' . $db_config['prefix'] . '_' . $module_data . '_student' );

	if( ! empty( $q ) )
	{
		$db->where( 'name_student LIKE :q_name_student OR alias LIKE :q_alias OR datetime LIKE :q_datetime OR address LIKE :q_address' );
	}
	$sth = $db->prepare( $db->sql() );

	if( ! empty( $q ) )
	{
		$sth->bindValue( ':q_name_student', '%' . $q . '%' );
		$sth->bindValue( ':q_alias', '%' . $q . '%' );
		$sth->bindValue( ':q_datetime', '%' . $q . '%' );
		$sth->bindValue( ':q_address', '%' . $q . '%' );
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
		$sth->bindValue( ':q_name_student', '%' . $q . '%' );
		$sth->bindValue( ':q_alias', '%' . $q . '%' );
		$sth->bindValue( ':q_datetime', '%' . $q . '%' );
		$sth->bindValue( ':q_address', '%' . $q . '%' );
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
		$view['datetime'] = ( empty( $view['datetime'] )) ? '' : nv_date( 'd/m/Y', $view['datetime'] );
		$view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id_student=' . $view['id_student'];
		$view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id_student=' . $view['id_student'] . '&amp;delete_checkss=' . md5( $view['id_student'] . NV_CACHE_PREFIX . $client_info['session_id'] );
		$xtpl->assign( 'VIEW', $view );
		$xtpl->parse( 'main.view.loop' );
	}
	$xtpl->parse( 'main.view' );
}


$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$page_title = $lang_module['add_student'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';