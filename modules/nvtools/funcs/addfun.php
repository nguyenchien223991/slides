<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 19 Mar 2011 16:50:45 GMT
 */

if( !defined( 'NV_IS_MOD_NVTOOLS' ) ) die( 'Stop!!!' );

define( 'NV_ADMIN', true );

function nv_get_lang_mod_admin( $mod, $lang )
{
	global $global_config;

	$lang_module = array();
	if( file_exists( NV_ROOTDIR . '/modules/' . $mod . '/language/admin_' . $lang . '.php' ) )
	{
		include NV_ROOTDIR . '/modules/' . $mod . '/language/admin_' . $lang . '.php';
	}
	return $lang_module;
}

function nv_write_lang_mod_admin( $mod, $lang, $arr_new_lang )
{
	global $funname;
	if( !empty( $arr_new_lang ) )
	{
		if( file_exists( NV_ROOTDIR . '/modules/' . $mod . '/language/admin_' . $lang . '.php' ) )
		{
			$content_lang = file_get_contents( NV_ROOTDIR . '/modules/' . $mod . '/language/admin_' . $lang . '.php' );
			$content_lang = trim( $content_lang );
			$content_lang = rtrim( $content_lang, '?>' );
		}
		else
		{
			$content_lang = "<?php\n\n";
			$content_lang .= "/**\n";
			$content_lang .= "* @Project NUKEVIET 4.x\n";
			$content_lang .= "* @Author VINADES.,JSC (contact@vinades.vn)\n";
			$content_lang .= "* @Copyright (C) " . date( "Y" ) . " VINADES.,JSC. All rights reserved\n";
			$content_lang .= "* @Language " . $language_array[$dirlang]['name'] . "\n";
			$content_lang .= "* @License CC BY-SA (http://creativecommons.org/licenses/by-sa/4.0/)\n";
			$content_lang .= "* @Createdate " . gmdate( "M d, Y, h:i:s A", $createdate ) . "\n";
			$content_lang .= "*/\n";

			$content_lang .= "\nif( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) )";

			$content_lang .= " die( 'Stop!!!' );\n\n";

			$array_translator['info'] = ( isset( $array_translator['info'] )) ? $array_translator['info'] : "";

			$content_lang .= "\$lang_translator['author'] = 'VINADES.,JSC (contact@vinades.vn)';\n";
			$content_lang .= "\$lang_translator['createdate'] = '" . date( 'd/m/Y, H:i' ) . "';\n";
			$content_lang .= "\$lang_translator['copyright'] = 'Copyright (C) ' . date( 'Y' ) . ' VINADES.,JSC. All rights reserved';\n";
			$content_lang .= "\$lang_translator['info'] = '';\n";
			$content_lang .= "\$lang_translator['langtype'] = 'lang_module';\n";
			$content_lang .= "\n";
		}
		$content_lang .= "\n\n//Lang for function " . $funname . "\n";

		foreach( $arr_new_lang as $lang_key => $lang_value )
		{
			$lang_value = nv_unhtmlspecialchars( $lang_value );
			$lang_value = str_replace( "\'", "'", $lang_value );
			$lang_value = str_replace( "'", "\'", $lang_value );
			$lang_value = nv_nl2br( $lang_value );
			$lang_value = str_replace( '<br />', '<br />', $lang_value );
			$content_lang .= "\$lang_module['" . $lang_key . "'] = '" . $lang_value . "';\n";
		}
		file_put_contents( NV_ROOTDIR . '/modules/' . $mod . '/language/admin_' . $lang . '.php', $content_lang, LOCK_EX );
	}
}

if( $nv_Request->isset_request( 'loadmodname', 'get' ) )
{
	echo '<select class=\"form-control\" name="tablename"><option value=""> -- chọn bảng dữ liệu -- </option>';
	$loadmodname = $nv_Request->get_title( 'loadmodname', 'get', '' );
	if( preg_match( '/^[a-zA-Z0-9\_]+$/', $loadmodname ) )
	{
		$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( $db_config['prefix'] . '\_' . NV_LANG_DATA . '\_' . $loadmodname . '%' ) );
		while( $item = $result->fetch() )
		{
			echo '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
		}
		$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( $db_config['prefix'] . '\_' . $loadmodname . '%' ) );
		while( $item = $result->fetch() )
		{
			echo '<option value="' . $item['name'] . '">' . $item['name'] . '</option>';
		}
		echo '</select>';
	}
	die();
}
$page_title = $lang_module['SiteTitleModule'];
$key_words = $module_info['keywords'];

$tablename = $nv_Request->get_title( 'tablename', 'get,post' );
$modname = $nv_Request->get_title( 'modname', 'get,post' );
$funname = $nv_Request->get_title( 'funname', 'get,post' );

$xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

$xtpl->assign( 'MODNAME', $modname );
$xtpl->assign( 'TABLENAME', $tablename );
$xtpl->assign( 'FUNNAME', $funname );

$nb = 0;
if( preg_match( '/^[a-zA-Z0-9\_]+$/', $modname ) and preg_match( '/^[a-zA-Z0-9\_]+$/', $tablename ) )
{
	$array_views = $nv_Request->get_typed_array( 'views', 'post', 'string' );
	$array_requireds = $nv_Request->get_typed_array( 'requireds', 'post', 'int' );
	$array_hiddens = $nv_Request->get_typed_array( 'hiddens', 'post', 'int' );
	$array_listviews = $nv_Request->get_typed_array( 'listviews', 'post', 'int' );

	$array_title_vi = $nv_Request->get_typed_array( 'title_vi', 'post', 'string' );
	$array_title_en = $nv_Request->get_typed_array( 'title_en', 'post', 'string' );

	$setlangvi = $nv_Request->get_int( 'setlangvi', 'get', 0 );
	$setlangen = $nv_Request->get_int( 'setlangen', 'get', 0 );

	if( $nv_Request->isset_request( 'views', 'post' ) )
	{
		$generate_page = $nv_Request->get_int( 'generate_page', 'post', 0 );
		$search_page = $nv_Request->get_int( 'search_page', 'post', 0 );
		$weight_page = $nv_Request->get_title( 'weight_page', 'post', '' );
		if( !empty( $weight_page ) )
		{
			unset( $array_listviews[$weight_page] );
			$array_hiddens[$weight_page] = 1;
		}

		if( empty( $array_listviews ) )
		{
			$generate_page = $search_page = 0;
			$weight_page = '';
		}
	}
	else
	{
		$generate_page = $search_page = 1;
		$weight_page = 'weight';
	}

	$content_default = '';
	try
	{
		$primary = '';
		$array_columns = array();
		$array_field_js = array();

		$list_no_us = 'add, all, alter, analyze, and, as, asc, before, between, bigint, binary, both, by, call, cascade, case, change, char, character, check, collate, column, comment, condition, constraint, continue, convert, create, cross, current_user, cursor, database, databases, date, day_hour, day_minute, day_second, dec, decimal, declare, default, delayed, delete, desc, describe, distinct, distinctrow, drop, dual, else, elseif, enclosed, escaped, exists, exit, explain, false, fetch, file, float4, float8, for, force, foreign, from, fulltext, get, grant, group, having, high_priority, hour_minute, hour_second, identified, if, ignore, ignore_server_ids, in, index, infile, inner, insert, int1, int2, int3, int4, int8, integer, interval, into, is, iterate, join, key, keys, kill, leading, leave, left, level, like, limit, lines, load, lock, long, loop, low_priority, master_bind, master_heartbeat_period, master_ssl_verify_server_cert, match, middleint, minute_second, mod, mode, modify, natural, no_write_to_binlog, not, null, number, numeric, on, optimize, option, optionally, or, order, outer, outfile, partition, precision, primary, privileges, procedure, public, purge, read, real, references, release, rename, repeat, replace, require, resignal, restrict, return, revoke, right, rlike, rows, schema, schemas, select, separator, session, set, share, show, signal, spatial, sql_after_gtids, sql_before_gtids, sql_big_result, sql_calc_found_rows, sql_small_result, sqlstate, ssl, start, starting, straight_join, table, terminated, then, to, trailing, trigger, true, undo, union, unique, unlock, unsigned, update, usage, use, user, using, values, varcharacter, varying, view, when, where, while, with, write, year_month, zerofill';
		$array_no_us = explode( ',', $list_no_us );
		$array_no_us = array_map( 'trim', $array_no_us );
		$array_no_us = array_unique( $array_no_us );

		$result = $db->query( "select * from information_schema.columns where `TABLE_SCHEMA` = '" . $db_config['dbname'] . "' and `TABLE_NAME` = '" . $tablename . "'" );
		//print_r($result->fetchAll());
		//die();
		while( $column = $result->fetch() )
		{
			$array_columns[$column['column_name']] = $column;
			if( $column['column_key'] == 'PRI' )
			{
				if( in_array( $column['column_name'], $array_no_us ) )
				{
					$contents = '<div class="alert alert-danger">' . sprintf( $lang_module['field_no_us'], $column['column_name'] ) . '</div>';
					include NV_ROOTDIR . '/includes/header.php';
					echo nv_site_theme( $contents );
					include NV_ROOTDIR . '/includes/footer.php';
					die();
				}
				$primary = $column['column_name'];
				if( $column['extra'] == 'auto_increment' )
				{
					continue;
				}
			}
			$nb++;

			if( isset( $array_title_en[$column['column_name']] ) )
			{
				$column['title_en'] = trim( $array_title_en[$column['column_name']] );
			}
			elseif( $setlangen )
			{
				$column['title_en'] = ucfirst( str_replace( '_', ' ', $column['column_name'] ) );
			}
			else
			{
				$column['title_en'] = '';
			}

			if( isset( $array_title_vi[$column['column_name']] ) )
			{
				$column['title_vi'] = trim( $array_title_vi[$column['column_name']] );
			}
			elseif( $setlangvi )
			{
				$column['title_vi'] = (!empty( $column['column_comment'] )) ? $column['column_comment'] : ucfirst( str_replace( '_', ' ', $column['column_name'] ) );
			}
			else
			{
				$column['title_vi'] = '';
			}

			$column['required_checked'] = isset( $array_requireds[$column['column_name']] ) ? ' checked="checked"' : '';
			$column['hidden_checked'] = isset( $array_hiddens[$column['column_name']] ) ? ' checked="checked"' : '';
			$column['listview_checked'] = isset( $array_listviews[$column['column_name']] ) ? ' checked="checked"' : '';

			if( strpos( $column['data_type'], 'text' ) !== false )
			{
				$field_type = (strpos( $column['data_type'], 'mediumtext' ) !== false or strpos( $column['data_type'], 'longtext' ) !== false) ? 'editor' : 'textarea';
				$array_field_type_i = array(
					'textarea' => $lang_module['field_type_textarea'],
					'editor' => $lang_module['field_type_editor'],
				);
			}
			elseif( strpos( $column['data_type'], 'int' ) !== false )
			{
				$field_type = 'number';
				$array_field_type_i = array(
					'number_int' => $lang_module['field_type_int'],
					'number_float' => $lang_module['field_type_float'],
					'date' => $lang_module['field_type_date'],
					'time' => $lang_module['field_type_time'],
					'textbox' => $lang_module['field_type_textbox'],
					'select' => $lang_module['field_type_select'],
					'radio' => $lang_module['field_type_radio'],
					'checkbox' => $lang_module['field_type_checkbox']
				);
				if( strpos( $column['data_type'], 'int' ) !== 0 )
				{
					unset( $array_field_type_i['date'] );
					unset( $array_field_type_i['time'] );
				}
			}
			else
			{
				if( $column['column_name'] == 'alias' )
				{
					$field_type = 'textalias';
				}
				else
				{
					$field_type = 'textbox';
				}
				$array_field_type_i = array(
					'email' => $lang_module['field_type_email'],
					'url' => $lang_module['field_type_url'],
					'textbox' => $lang_module['field_type_textbox'],
					'textfile' => $lang_module['field_type_textfile'],
					'textalias' => $lang_module['field_type_textalias'],
					'password' => $lang_module['field_type_password'],
					'select' => $lang_module['field_type_select'],
					'radio' => $lang_module['field_type_radio'],
					'checkbox' => $lang_module['field_type_checkbox']
				);
				if( strpos( $column['column_name'], 'groups_' ) !== false )
				{
					$field_type = 'checkbox_groups';
					$array_field_type_i['checkbox_groups'] = $lang_module['field_type_checkbox_groups'];
				}
			}

			if( isset( $array_views[$column['column_name']] ) )
			{
				$field_type = $array_views[$column['column_name']];
			}

			foreach( $array_field_type_i as $key => $value )
			{
				$xtpl->assign( 'FIELD_TYPE', array(
					'key' => $key,
					'value' => $value,
					'selected' => ($field_type == $key) ? ' selected="selected"' : ''
				) );
				$xtpl->parse( 'main.form.column.field_type' );
			}

			$xtpl->assign( 'COLUMN', $column );

			$xtpl->parse( 'main.form.column' );

			if( strpos( $column['data_type'], 'int' ) !== false and $column['column_name'] != $primary )
			{
				$xtpl->assign( 'FIELD_TYPE', array(
					'key' => $column['column_name'],
					'value' => $column['column_name'],
					'selected' => ($column['column_name'] == $weight_page) ? ' selected="selected"' : ''
				) );
				$xtpl->parse( 'main.form.weight_page' );
			}
		}

		$xtpl->assign( 'GENERATE_PAGE_CHECKED', ($generate_page) ? ' checked="checked"' : '' );
		$xtpl->assign( 'SEARCH_PAGE_CHECKED', ($search_page) ? ' checked="checked"' : '' );

		if( empty( $modname ) )
		{
			if( preg_match( '/^' . $db_config['prefix'] . '\_([a-z]+)\_([a-z0-9]+)\_/', $tablename, $m ) )
			{
				$modname = $m[2];
			}
		}

		if( !empty( $array_views ) )
		{
			//	wite file php
			$_tmp_key_insert = array();
			$_tmp_key_update = array();
			$_tmp_key_editor = array();
			$_tmp_key_file = array();
			$txt_bindParam = '';
			$txt_bindParam_default = '';
			$txt_post = '';
			$txt_date_view = '';

			$lang_mod_admin_vi = nv_get_lang_mod_admin( $modname, 'vi' );
			$lang_mod_admin_en = nv_get_lang_mod_admin( $modname, 'vi' );

			$lang_mod_admin_vi_new = array();
			$lang_mod_admin_en_new = array();

			if( !isset( $lang_mod_admin_vi[$funname] ) and !isset( $lang_mod_admin_vi_new[$funname] ) )
			{
				$lang_mod_admin_vi_new[$funname] = $funname;
			}

			if( !isset( $lang_mod_admin_en[$funname] ) and !isset( $lang_mod_admin_en_new[$funname] ) )
			{
				$lang_mod_admin_en_new[$funname] = $funname;
			}

			if( !isset( $lang_mod_admin_vi['edit'] ) and !isset( $lang_mod_admin_vi_new['edit'] ) )
			{
				$lang_mod_admin_vi_new['edit'] = 'Sửa';
			}

			if( !isset( $lang_mod_admin_en['edit'] ) and !isset( $lang_mod_admin_en_new['edit'] ) )
			{
				$lang_mod_admin_en_new['edit'] = 'edit';
			}

			if( !isset( $lang_mod_admin_vi['delete'] ) and !isset( $lang_mod_admin_vi_new['delete'] ) )
			{
				$lang_mod_admin_vi_new['delete'] = 'Xóa';
			}

			if( !isset( $lang_mod_admin_en['delete'] ) and !isset( $lang_mod_admin_en_new['delete'] ) )
			{
				$lang_mod_admin_en_new['delete'] = 'Delete';
			}

			if( !isset( $lang_mod_admin_vi['number'] ) and !isset( $lang_mod_admin_vi_new['number'] ) )
			{
				$lang_mod_admin_vi_new['number'] = 'STT';
			}

			if( !isset( $lang_mod_admin_en['number'] ) and !isset( $lang_mod_admin_en_new['number'] ) )
			{
				$lang_mod_admin_en_new['number'] = 'Number';
			}

			if( $search_page )
			{
				if( !isset( $lang_mod_admin_vi['search_title'] ) and !isset( $lang_mod_admin_vi_new['search_title'] ) )
				{
					$lang_mod_admin_vi_new['search_title'] = 'Nhập từ khóa tìm kiếm';
				}

				if( !isset( $lang_mod_admin_en['search_title'] ) and !isset( $lang_mod_admin_en_new['search_title'] ) )
				{
					$lang_mod_admin_en_new['search_title'] = 'Enter keywords searching';
				}

				if( !isset( $lang_mod_admin_vi['search_submit'] ) and !isset( $lang_mod_admin_vi_new['search_submit'] ) )
				{
					$lang_mod_admin_vi_new['search_submit'] = 'Tìm kiếm';
				}

				if( !isset( $lang_mod_admin_en['search_submit'] ) and !isset( $lang_mod_admin_en_new['search_submit'] ) )
				{
					$lang_mod_admin_en_new['search_submit'] = 'Search';
				}
			}

			// Lấy ngôn ngữ
			foreach( $array_views as $key => $_view_type )
			{
				if( !isset( $array_hiddens[$key] ) or isset( $array_requireds[$key] ) )
				{
					if( !isset( $lang_mod_admin_vi[$key] ) and !isset( $lang_mod_admin_vi_new[$key] ) )
					{
						$lang_mod_admin_vi_new[$key] = $array_title_vi[$key];
					}
					if( !isset( $lang_mod_admin_en[$key] ) and !isset( $lang_mod_admin_en_new[$key] ) )
					{
						$lang_mod_admin_en_new[$key] = $array_title_en[$key];
					}
				}
			}

			$array_check_error = array();
			// Kiểm tra các biến  bắt buộc phải nhập
			foreach( $array_requireds as $key => $value )
			{
				$array_check_error[] = "if( empty( \$row['" . $key . "'] ) )\n\t{\n\t\t\$error[] = \$lang_module['error_required_" . $key . "'];\n\t}";

				if( !isset( $lang_mod_admin_vi['error_required_' . $key] ) and !isset( $lang_mod_admin_vi_new['error_required_' . $key] ) )
				{
					$lang_mod_admin_vi_new['error_required_' . $key] = "Lỗi: bạn cần nhập dữ liệu cho " . $lang_mod_admin_vi_new[$key];
				}

				if( !isset( $lang_mod_admin_en['error_required_' . $key] ) and !isset( $lang_mod_admin_en_new['error_required_' . $key] ) )
				{
					$lang_mod_admin_en_new['error_required_' . $key] = "Error: Required fields enter the " . $lang_mod_admin_en_new[$key];
				}
			}

			$content = "<?php\n\n";
			$content .= NV_FILEHEAD . "\n\n";
			$content .= "if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );\n\n";

			if( in_array( 'textalias', $array_views ) )
			{
				$content .= "if ( \$nv_Request->isset_request( 'get_alias_title', 'post' ) )\n{\n";
				$content .= "\t\$alias = \$nv_Request->get_title( 'get_alias_title', 'post', '' );\n";
				$content .= "\t\$alias = change_alias( \$alias );\n";
				$content .= "\tdie( \$alias );\n";
				$content .= "}\n\n";
			}

			if( in_array( 'checkbox_groups', $array_views ) )
			{
				$content .= "\$groups_list = nv_groups_list();\n\n";
			}

			if( preg_match( '/^' . $db_config['prefix'] . '\_([a-z]{2}+)\_([a-z0-9]+)\_([a-z0-9\_]+)$/', $tablename, $m ) )
			{
				$tablename_save = "' . NV_PREFIXLANG . '_' . \$module_data . '_" . $m[3];
			}
			elseif( preg_match( '/^' . $db_config['prefix'] . '\_([a-z0-9]+)\_([a-z0-9\_]+)$/', $tablename, $m ) )
			{
				$tablename_save = "' . \$db_config['prefix'] . '_' . \$module_data . '_" . $m[2];
			}
			elseif( preg_match( '/^' . $db_config['prefix'] . '\_([a-z]{2}+)\_([a-z0-9]+)$/', $tablename, $m ) )
			{
				$tablename_save = "' . NV_PREFIXLANG . '_' . \$module_data . '";
			}
			else
			{
				$tablename_save = $tablename;
			}

			if( !empty( $weight_page ) )//neu co cot weight
			{
				$content .= "if( \$nv_Request->isset_request( 'ajax_action', 'post' ) )\n";
				$content .= "{\n";
				$content .= "\t\$" . $primary . " = \$nv_Request->get_int( '" . $primary . "', 'post', 0 );\n";
				$content .= "\t\$new_vid = \$nv_Request->get_int( 'new_vid', 'post', 0 );\n";
				$content .= "\t\$content = 'NO_' . \$" . $primary . ";\n";
				$content .= "\tif( \$new_vid > 0 )\n";
				$content .= "\t{\n";
				$content .= "\t\t\$sql = 'SELECT " . $primary . " FROM " . $tablename_save . " WHERE " . $primary . "!=' . \$" . $primary . " . ' ORDER BY " . $weight_page . " ASC';\n";
				$content .= "\t\t\$result = \$db->query( \$sql );\n";
				$content .= "\t\t\$" . $weight_page . " = 0;\n";
				$content .= "\t\twhile( \$row = \$result->fetch() )\n";
				$content .= "\t\t{\n";
				$content .= "\t\t\t++\$" . $weight_page . ";\n";
				$content .= "\t\t\tif( \$" . $weight_page . " == \$new_vid ) ++\$" . $weight_page . ";\n";
				$content .= "\t\t\t\$sql = 'UPDATE " . $tablename_save . " SET " . $weight_page . "=' . \$" . $weight_page . " . ' WHERE " . $primary . "=' . \$row['" . $primary . "'];\n";
				$content .= "\t\t\t\$db->query( \$sql );\n";
				$content .= "\t\t}\n";
				$content .= "\t\t\$sql = 'UPDATE " . $tablename_save . " SET " . $weight_page . "=' . \$new_vid . ' WHERE " . $primary . "=' . \$" . $primary . ";\n";
				$content .= "\t\t\$db->query( \$sql );\n";
				$content .= "\t\t\$content = 'OK_' . \$" . $primary . ";\n";
				$content .= "\t}\n";
				$content .= "\tnv_del_moduleCache( \$module_name );\n";
				$content .= "\tinclude NV_ROOTDIR . '/includes/header.php';\n";
				$content .= "\techo \$content;\n";
				$content .= "\tinclude NV_ROOTDIR . '/includes/footer.php';\n";
				$content .= "\texit();\n";
				$content .= "}\n";
			}

			$content .= "if ( \$nv_Request->isset_request( 'delete_" . $primary . "', 'get' ) and \$nv_Request->isset_request( 'delete_checkss', 'get' ))\n{\n";
			$content .= "\t\$" . $primary . " = \$nv_Request->get_int( 'delete_" . $primary . "', 'get' );\n";
			$content .= "\t\$delete_checkss = \$nv_Request->get_string( 'delete_checkss', 'get' );\n";
			$content .= "\tif( \$" . $primary . " > 0 and \$delete_checkss == md5( \$" . $primary . " . NV_CACHE_PREFIX . \$client_info['session_id'] ) )\n\t{\n";
			if( !empty( $weight_page ) )
			{
				$content .= "\t\t\$" . $weight_page . "=0;\n";
				$content .= "\t\t\$sql = 'SELECT " . $weight_page . " FROM " . $tablename_save . " WHERE " . $primary . " =' . \$db->quote( $" . $primary . " );\n";
				$content .= "\t\t\$result = \$db->query( \$sql );\n";
				$content .= "\t\tlist( \$" . $weight_page . ") = \$result->fetch( 3 );\n";
				$content .= "\t\t\n";
			}

			$content .= "\t\t\$db->query('DELETE FROM " . $tablename_save . "  WHERE " . $primary . " = ' . \$db->quote( $" . $primary . " ) );\n";
			if( !empty( $weight_page ) )
			{
				$content .= "\t\tif( \$" . $weight_page . " > 0)\n";
				$content .= "\t\t{\n";
				$content .= "\t\t\t\$sql = 'SELECT " . $primary . ", " . $weight_page . " FROM " . $tablename_save . " WHERE " . $weight_page . " >' . \$" . $weight_page . ";\n";
				$content .= "\t\t\t\$result = \$db->query( \$sql );\n";
				$content .= "\t\t\twhile(list( \$" . $primary . ", \$" . $weight_page . ") = \$result->fetch( 3 ))\n";
				$content .= "\t\t\t{\n";
				$content .= "\t\t\t\t\$" . $weight_page . "--;\n";
				$content .= "\t\t\t\t\$db->query( 'UPDATE " . $tablename_save . " SET " . $weight_page . "=' . \$" . $weight_page . " . ' WHERE " . $primary . "=' . intval( \$" . $primary . " ));\n";
				$content .= "\t\t\t}\n";
				$content .= "\t\t}\n";
			}
			$content .= "\t\tHeader( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . \$module_name . '&' . NV_OP_VARIABLE . '=' . \$op );\n";

			$content .= "\t\tdie();\n";
			$content .= "\t}\n";
			$content .= "}\n\n";

			$content .= "\$row = array();\n";
			$content .= "\$error = array();\n";
			if( !empty( $primary ) )
			{
				$content .= "\$row['" . $primary . "'] = \$nv_Request->get_int( '" . $primary . "', 'post,get', 0 );\n";
			}

			$content .= "if ( \$nv_Request->isset_request( 'submit', 'post' ) )\n{\n";

			foreach( $array_columns as $key => $column )
			{
				if( $key == $primary )
				{
					continue;
				}
				$_tmp_key_insert[] = ':' . $key;

				if( !isset( $array_hiddens[$key] ) or isset( $array_requireds[$key] ) )
				{
					$_tmp_key_update[] = $key . ' = :' . $key;
					$_view_type = $array_views[$key];

					$txt_bindParam .= "\t\t\t\$stmt->bindParam( ':" . $key . "', \$row['" . $key . "'], PDO::PARAM_";

					// Từ kiểm dữ liệu sẽ bắt biến theo cách đó dù cho form chọn kiểu ghì
					if( strpos( $column['data_type'], 'text' ) !== false )
					{
						$txt_bindParam .= "STR, strlen(\$row['" . $key . "'])";

						if( $_view_type == 'editor' )
						{
							$_tmp_key_editor[] = $key;
							$txt_post .= "\t\$row['" . $key . "'] = \$nv_Request->get_editor( '" . $key . "', '', NV_ALLOWED_HTML_TAGS );\n";
						}
						else
						{
							$txt_post .= "\t\$row['" . $key . "'] = \$nv_Request->get_string( '" . $key . "', 'post', '' );\n";
						}

					}
					elseif( strpos( $column['data_type'], 'int' ) !== false )
					{
						if( $_view_type == 'date' or $_view_type == 'time' )
						{
							$txt_date_view .= "\nif( empty( \$row['" . $key . "'] ) )\n";
							$txt_date_view .= "{\n";
							$txt_date_view .= "\t\$row['" . $key . "'] = '';\n";
							$txt_date_view .= "}\n";
							$txt_date_view .= "else\n";
							$txt_date_view .= "{\n";
							$txt_date_view .= "\t\$row['" . $key . "'] = date( 'd/m/Y', \$row['" . $key . "'] );\n";
							$txt_date_view .= "}\n";

							$txt_post .= "\tif( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})\$/', \$nv_Request->get_string( '" . $key . "', 'post' ), \$m ) )\n";
							$txt_post .= "\t{\n";
							if( $_view_type == 'time' )
							{
								$txt_post .= "\t\t\$_hour = \$nv_Request->get_int( '" . $key . "_hour', 'post' );\n";
								$txt_post .= "\t\t\$_min = \$nv_Request->get_int( '" . $key . "_min', 'post' );\n";
							}
							else
							{
								$txt_post .= "\t\t\$_hour = 0;\n";
								$txt_post .= "\t\t\$_min = 0;\n";
							}
							$txt_post .= "\t\t\$row['" . $key . "'] = mktime( \$_hour, \$_min, 0, \$m[2], \$m[1], \$m[3] );\n";
							$txt_post .= "\t}\n";
							$txt_post .= "\telse\n";
							$txt_post .= "\t{\n";
							$txt_post .= "\t\t\$row['" . $key . "'] = 0;\n";
							$txt_post .= "\t}\n";
						}
						else
						{
							$txt_post .= "\t\$row['" . $key . "'] = \$nv_Request->get_int( '" . $key . "', 'post', 0 );\n";
						}
						$txt_bindParam .= "INT";
					}
					elseif( $_view_type == 'checkbox_groups' )
					{
						$txt_post .= "\n\t\$_groups_post = \$nv_Request->get_array( '" . $key . "', 'post', array() );\n";
						$txt_post .= "\t\$row['" . $key . "'] = !empty( \$_groups_post ) ? implode( ',', nv_groups_post( array_intersect( \$_groups_post, array_keys( \$groups_list ) ) ) ) : '';\n";
						$txt_bindParam .= "STR";
					}
					else
					{
						$txt_post .= "\t\$row['" . $key . "'] = \$nv_Request->get_title( '" . $key . "', 'post', '' );\n";
						if( $_view_type == 'textfile' )
						{
							$_tmp_key_file[] = $key;
							$txt_post .= "\tif( is_file( NV_DOCUMENT_ROOT . \$row['" . $key . "'] ) )\n";
							$txt_post .= "\t{\n";
							$txt_post .= "\t\t\$row['" . $key . "'] = substr( \$row['" . $key . "'], strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . \$module_name . '/' ) );\n";
							$txt_post .= "\t}\n";
							$txt_post .= "\telse\n";
							$txt_post .= "\t{\n";
							$txt_post .= "\t\t\$row['" . $key . "'] = '';\n";
							$txt_post .= "\t}\n";
						}
						elseif( $_view_type == 'textalias' and !isset( $array_field_js['textalias'] ) )
						{
							$txt_post .= "\t\$row['" . $key . "'] = ( empty(\$row['" . $key . "'] ))? change_alias( \$row['title'] ) : change_alias( \$row['" . $key . "'] );\n";
							$array_field_js['textalias'] = $key;
						}

						$txt_bindParam .= "STR";
					}
					$txt_bindParam .= " );\n";

					if( $_view_type == 'email' )// Kiểm tra các biến nếu là email
					{
						$array_check_error[] = "if( ! empty( \$row['" . $key . "'] ) and ( \$error_email = nv_check_valid_email( \$row['" . $key . "'] ) ) != '' )\n\t{\n\t\t\$error[] = \$error_email;\n\t}";
					}
					elseif( $_view_type == 'url' )// Kiểm tra các biến nếu là url
					{
						$array_check_error[] = "if( ! empty( \$row['" . $key . "'] ) and ! nv_is_url( \$row['" . $key . "'] ) )\n\t{\n\t\t\$error[] = \$lang_module['error_url_" . $key . "'];\n\t}";

						if( !isset( $lang_mod_admin_vi['error_url_' . $key] ) and !isset( $lang_mod_admin_vi_new['error_url_' . $key] ) )
						{
							$lang_mod_admin_vi_new['error_url_' . $key] = "Lỗi: url ' . \$lang_module['" . $key . "'] không đíng";
						}

						if( !isset( $lang_mod_admin_en['error_url_' . $key] ) and !isset( $lang_mod_admin_en_new['error_url_' . $key] ) )
						{
							$lang_mod_admin_en_new['error_url_' . $key] = "Error: Url ' . \$lang_module['" . $key . "']";
						}
					}
				}
				else
				{
					//echo( $column['column_name'] . '---------' . $weight_page.'<br>' );
					if( strpos( $column['data_type'], 'int' ) !== false )
					{
						if( $column['column_name'] == $weight_page )
						{
							$txt_bindParam_default .= "\t\t\t\t\$weight = \$db->query( 'SELECT max(" . $weight_page . ") FROM " . $tablename_save . "' )->fetchColumn();\n";
							$txt_bindParam_default .= "\t\t\t\t\$weight = intval( \$weight ) + 1;\n";
							$txt_bindParam_default .= "\t\t\t\t\$stmt->bindParam( ':" . $key . "', \$weight, PDO::PARAM_INT );\n\n";
						}
						else
						{
							$content_default .= "\t\t\t\t\$row['" . $key . "'] = " . intval( $column['column_default'] ) . ";\n";
							$txt_bindParam_default .= "\t\t\t\t\$stmt->bindParam( ':" . $key . "', \$row['" . $key . "'], PDO::PARAM_INT );\n";
						}
					}
					else
					{
						if( $_view_type == 'checkbox_groups' )
						{
							$content_default .= "\t\t\t\t\$row['" . $key . "'] = '6';\n";
						}
						else
						{
							$content_default .= "\t\t\t\t\$row['" . $key . "'] = '" . $column['column_default'] . "';\n";
						}
						$txt_bindParam_default .= "\t\t\t\t\$stmt->bindParam( ':" . $key . "', \$row['" . $key . "'], PDO::PARAM_STR );\n";
					}
				}
			}
			//die($content_default);
			$content .= $txt_post;

			if( !empty( $array_check_error ) )
			{
				$content .= "\n\t" . implode( "\n\telse", $array_check_error ) . "\n";
			}

			// begin try catch
			$content .= "\n\tif( empty( \$error ) )\n";
			$content .= "\t{\n";
			$content .= "\t\ttry\n";
			$content .= "\t\t{\n";
			$content .= "\t\t\tif( empty( \$row['" . $primary . "'] ) )\n";
			$content .= "\t\t\t{\n";
			if( !empty( $content_default ) )
			{
				$content .= "\n" . $content_default . "\n";
			}
			$content .= "\t\t\t\t\$stmt = \$db->prepare( '";
			$content .= 'INSERT INTO ' . $tablename_save . ' (' . implode( ', ', array_keys( $array_views ) ) . ') VALUES (' . implode( ', ', $_tmp_key_insert ) . ')';
			$content .= "' );\n";
			if( !empty( $txt_bindParam_default ) )
			{
				$content .= "\n" . $txt_bindParam_default . "\n";
			}
			$content .= "\t\t\t}\n";
			if( !empty( $primary ) )
			{
				$content .= "\t\t\telse\n";
				$content .= "\t\t\t{\n";
				$content .= "\t\t\t\t\$stmt = \$db->prepare( '";
				$content .= 'UPDATE ' . $tablename_save . ' SET ' . implode( ', ', $_tmp_key_update ) . ' WHERE ' . $primary . '=\' . $row[\'' . $primary . '\']';
				$content .= " );\n";
				$content .= "\t\t\t}\n";
			}

			$content .= $txt_bindParam;
			$content .= "\n";
			$content .= "\t\t\t\$exc = \$stmt->execute();";
			$content .= "\n\t\t\tif( \$exc )\n";
			$content .= "\t\t\t{\n";
			$content .= "\t\t\t\tHeader( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . \$module_name . '&' . NV_OP_VARIABLE . '=' . \$op );\n";
			$content .= "\t\t\t\tdie();\n";
			$content .= "\t\t\t}\n";
			// end try catch
			$content .= "\t\t}\n";
			$content .= "\t\tcatch( PDOException \$e )\n";
			$content .= "\t\t{\n";
			$content .= "\t\t\ttrigger_error( \$e->getMessage() );\n";
			$content .= "\t\t\tdie( \$e->getMessage() ); //Remove this line after checks finished\n";
			$content .= "\t\t}\n";
			$content .= "\t}\n";

			$content .= "}\n";
			if( !empty( $primary ) )
			{
				$content .= "elseif( \$row['" . $primary . "'] > 0 )\n{\n";
				$content .= "\t\$row = \$db->query( 'SELECT * FROM " . $tablename_save . " WHERE " . $primary . "=' . \$row['" . $primary . "'] )->fetch();";
				$content .= "\n\tif( empty( \$row ) )\n";
				$content .= "\t{\n";
				$content .= "\t\tHeader( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . \$module_name . '&' . NV_OP_VARIABLE . '=' . \$op );\n";
				$content .= "\t\tdie();\n";
				$content .= "\t}\n";
				$content .= "}\n";
			}
			$content .= "else\n{\n";
			//print_r($array_columns);
			//die();
			foreach( $array_columns as $key => $_row )
			{
				if( !isset( $array_hiddens[$key] ) or isset( $array_requireds[$key] ) )
				{
					if( strpos( $_row['data_type'], 'int' ) !== false )
					{
						$content .= "\t\$row['" . $key . "'] = " . intval( $_row['column_default'] ) . ";\n";
					}
					elseif( $array_views[$key] == 'checkbox_groups' )
					{
						$content .= "\t\$row['" . $key . "'] = '6';\n";
					}
					else
					{
						$content .= "\t\$row['" . $key . "'] = '" . $_row['column_default'] . "';\n";
					}
				}
			}
			$content .= "}\n";

			$content .= $txt_date_view;

			// Gán lại giá trị cho chọn file
			foreach( $_tmp_key_file as $key )
			{
				$content .= "if( ! empty( \$row['" . $key . "'] ) and is_file( NV_UPLOADS_REAL_DIR . '/' . \$module_name . '/' . \$row['" . $key . "'] ) )\n";
				$content .= "{\n";
				$content .= "\t\$row['" . $key . "'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . \$module_name . '/' . \$row['" . $key . "'];\n";
				$content .= "}\n";
			}

			// Gán lại giá trị cho trình soạn thảo
			if( !empty( $_tmp_key_editor ) )
			{
				$content .= "\nif( defined( 'NV_EDITOR' ) ) require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';\n";
				foreach( $_tmp_key_editor as $key )
				{
					$content .= "\$row['" . $key . "'] = htmlspecialchars( nv_editor_br2nl( \$row['" . $key . "'] ) );\n";
					$content .= "if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )\n";
					$content .= "{\n";
					$content .= "\t\$row['" . $key . "'] = nv_aleditor( '" . $key . "', '100%', '300px', \$row['" . $key . "'] );\n";
					$content .= "}\n";
					$content .= "else\n";
					$content .= "{\n";
					$content .= "\t\$row['" . $key . "'] = '<textarea style=\"width:100%;height:300px\" name=\"" . $key . "\">' . \$row['" . $key . "'] . '</textarea>';\n";
					$content .= "}\n\n";
				}
			}
			if( !empty( $array_listviews ) )
			{
				$search_column = array();
				if( $search_page )
				{
					$content .= "\n\$q = \$nv_Request->get_title( 'q', 'post,get' );\n";
					foreach( $array_listviews as $key => $_tmp )
					{
						$search_column[] = $key . ' LIKE :q_' . $key;
					}
				}
				$content .= "\n// Fetch Limit\n";
				$content .= "\$show_view = false;\n";
				$content .= "if ( ! \$nv_Request->isset_request( 'id', 'post,get' ) )\n{\n";
				$content .= "\t\$show_view = true;\n";
				if( $generate_page or !empty( $weight_page ) )
				{
					$content .= "\t\$per_page = 5;\n";
					$content .= "\t\$page = \$nv_Request->get_int( 'page', 'post,get', 1 );\n";
					$content .= "\t\$db->sqlreset()\n";
					$content .= "\t\t->select( 'COUNT(*)' )\n";
					$content .= "\t\t->from( '" . $tablename_save . "' );\n";
					if( $search_page )
					{
						$content .= "\n\tif( ! empty( \$q ) )\n";
						$content .= "\t{\n";
						$content .= "\t\t\$db->where( '" . implode( ' OR ', $search_column ) . "' );\n";
						$content .= "\t}\n";
					}
					// Query Prepare
					$content .= "\t\$sth = \$db->prepare( \$db->sql() );\n";
					if( $search_page )
					{
						$content .= "\n\tif( ! empty( \$q ) )\n";
						$content .= "\t{\n";

						foreach( $array_listviews as $key => $_tmp )
						{
							$content .= "\t\t\$sth->bindValue( ':q_" . $key . "', '%' . \$q . '%' );\n";
						}
						$content .= "\t}\n";
					}
					$content .= "\t\$sth->execute();\n";

					$content .= "\t\$num_items = \$sth->fetchColumn();\n\n";
					$content .= "\t\$db->select( '*' )\n";
					if( !empty( $weight_page ) )
					{
						$content .= "\t\t->order( '" . $weight_page . " ASC' )\n";
					}
					else
					{
						$content .= "\t\t->order( '" . $primary . " DESC' )\n";
					}
					$content .= "\t\t->limit( \$per_page )\n";
					$content .= "\t\t->offset( ( \$page - 1 ) * \$per_page );\n";
					$content .= "\t\$sth = \$db->prepare( \$db->sql() );\n";
					if( $search_page )
					{
						$content .= "\n\tif( ! empty( \$q ) )\n";
						$content .= "\t{\n";
						foreach( $array_listviews as $key => $_tmp )
						{
							$content .= "\t\t\$sth->bindValue( ':q_" . $key . "', '%' . \$q . '%' );\n";
						}
						$content .= "\t}\n";
					}
				}
				else
				{
					$content .= "\t\$db->sqlreset()\n";
					$content .= "\t\t->select( '*' )\n";
					$content .= "\t\t->from( '" . $tablename_save . "' )\n";
					if( !empty( $weight_page ) )
					{
						$content .= "\t\t->order( '" . $weight_page . " ASC' );\n";
					}
					else
					{
						$content .= "\t\t->order( '" . $primary . " DESC' );\n";
					}
					if( $search_page )
					{
						$content .= "\n\tif( ! empty( \$q ) )\n";
						$content .= "\t{\n";
						$content .= "\t\t\$db->where( '" . implode( ' OR ', $search_column ) . "' );\n";
						$content .= "\t}\n";
					}
					$content .= "\t\$sth = \$db->prepare( \$db->sql() );\n";
					if( $search_page )
					{
						$content .= "\n\tif( ! empty( \$q ) )\n";
						$content .= "\t{\n";
						foreach( $array_listviews as $key => $_tmp )
						{
							$content .= "\t\t\$sth->bindValue( ':q_" . $key . "', '%' . \$q . '%' );\n";
						}
						$content .= "\t}\n";
					}
				}

				$content .= "\t\$sth->execute();\n";
				$content .= "}\n\n";
			}

			$content .= "\n\$xtpl = new XTemplate( \$op . '.tpl', NV_ROOTDIR . '/themes/' . \$global_config['module_theme'] . '/modules/' . \$module_file );\n";
			$content .= "\$xtpl->assign( 'LANG', \$lang_module );\n";
			$content .= "\$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );\n";
			$content .= "\$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );\n";
			$content .= "\$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );\n";
			$content .= "\$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );\n";
			$content .= "\$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );\n";
			$content .= "\$xtpl->assign( 'MODULE_NAME', \$module_name );\n";
			$content .= "\$xtpl->assign( 'OP', \$op );\n";
			$content .= "\$xtpl->assign( 'ROW', \$row );\n";
			if( !empty( $array_listviews ) )
			{
				if( $search_page )
				{
					$content .= "\$xtpl->assign( 'Q', \$q );\n";
				}
				$content .= "\nif( \$show_view )\n{\n";
				if( $generate_page )
				{
					$content .= "\t\$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . \$module_name . '&amp;' . NV_OP_VARIABLE . '=' . \$op;\n";
					if( $search_page )
					{
						$content .= "\tif( ! empty( \$q ) )\n";
						$content .= "\t{\n";
						$content .= "\t\t\$base_url .= '&q=' . \$q;\n";
						$content .= "\t}\n";
					}
					$content .= "\t\$xtpl->assign( 'NV_GENERATE_PAGE', nv_generate_page( \$base_url, \$num_items, \$per_page, \$page) );\n\n";
				}
				if( empty( $weight_page ) )
				{
					$content .= "\t\$number = 0;\n";
				}
				$content .= "\twhile( \$view = \$sth->fetch() )\n";
				$content .= "\t{\n";
				if( empty( $weight_page ) )
				{
					$content .= "\t\t\$view['number'] = ++\$number;\n";
				}
				else
				{
					$content .= "\t\tfor( \$i = 1; \$i <= \$num_items; ++\$i )\n";
					$content .= "\t\t{\n";
					$content .= "\t\t\t\$xtpl->assign( 'WEIGHT', array(\n";
					$content .= "\t\t\t\t'key' => \$i,\n";
					$content .= "\t\t\t\t'title' => \$i,\n";
					$content .= "\t\t\t\t'selected' => ( \$i == \$view['" . $weight_page . "'] ) ? ' selected=\"selected\"' : '') );\n";
					$content .= "\t\t\t\$xtpl->parse( 'main.view.loop." . $weight_page . "_loop' );\n";
					$content .= "\t\t}\n";
				}
				foreach( $array_views as $key => $input_type_i )
				{
					if( !isset( $array_hiddens[$key] ) or isset( $array_requireds[$key] ) )
					{
						if( $input_type_i == 'date' )
						{
							$content .= "\t\t\$view['" . $key . "'] = ( empty( \$view['" . $key . "'] )) ? '' : nv_date( 'd/m/Y', \$view['" . $key . "'] );\n";
						}
						elseif( $input_type_i == 'time' )
						{
							$content .= "\t\t\$view['" . $key . "'] = ( empty( \$view['" . $key . "'] )) ? '' : nv_date( 'H:i d/m/Y', \$view['" . $key . "'] );\n";
						}
					}
				}
				$content .= "\t\t\$view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . \$module_name . '&amp;' . NV_OP_VARIABLE . '=' . \$op . '&amp;" . $primary . "=' . \$view['" . $primary . "'];\n";
				$content .= "\t\t\$view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . \$module_name . '&amp;' . NV_OP_VARIABLE . '=' . \$op . '&amp;delete_" . $primary . "=' . \$view['" . $primary . "'] . '&amp;delete_checkss=' . md5( \$view['" . $primary . "'] . NV_CACHE_PREFIX . \$client_info['session_id'] );\n";
				$content .= "\t\t\$xtpl->assign( 'VIEW', \$view );\n";
				$content .= "\t\t\$xtpl->parse( 'main.view.loop' );\n";
				$content .= "\t}\n";
				$content .= "\t\$xtpl->parse( 'main.view' );\n";
				$content .= "}\n\n";
			}

			foreach( $array_views as $key => $input_type_i )
			{
				if( !isset( $array_hiddens[$key] ) or isset( $array_requireds[$key] ) )
				{
					if( $input_type_i == 'select' )
					{
						$content .= "\n\$array_" . $input_type_i . "_" . $key . " = array();\n";
						$content .= "\n\$array_" . $input_type_i . "_" . $key . "[0] = \$lang_global['no'];";
						$content .= "\n\$array_" . $input_type_i . "_" . $key . "[1] = \$lang_global['yes'];";
						$content .= "\nforeach( \$array_" . $input_type_i . "_" . $key . " as \$key => \$title )\n";
						$content .= "{\n";
						$content .= "\t\$xtpl->assign( 'OPTION', array(\n";
						$content .= "\t\t'key' => \$key,\n";
						$content .= "\t\t'title' => \$title,\n";
						$content .= "\t\t'selected' => (\$key == \$row['" . $key . "']) ? ' selected=\"selected\"' : ''\n";
						$content .= "\t) );\n";
						$content .= "\t\$xtpl->parse( 'main." . $input_type_i . "_" . $key . "' );\n";
						$content .= "}\n";
					}
					elseif( $input_type_i == 'radio' or $input_type_i == 'checkbox' )
					{
						$content .= "\n\$array_" . $input_type_i . "_" . $key . " = array();\n";
						if( $input_type_i == 'radio' )
						{
							$content .= "\n\$array_" . $input_type_i . "_" . $key . "[0] = \$lang_global['no'];";
						}
						$content .= "\n\$array_" . $input_type_i . "_" . $key . "[1] = \$lang_global['yes'];";
						$content .= "\nforeach( \$array_" . $input_type_i . "_" . $key . " as \$key => \$title )\n";
						$content .= "{\n";
						$content .= "\t\$xtpl->assign( 'OPTION', array(\n";
						$content .= "\t\t'key' => \$key,\n";
						$content .= "\t\t'title' => \$title,\n";
						$content .= "\t\t'checked' => (\$key == \$row['" . $key . "']) ? ' checked=\"checked\"' : ''\n";
						$content .= "\t) );\n";
						$content .= "\t\$xtpl->parse( 'main." . $input_type_i . "_" . $key . "' );\n";
						$content .= "}\n";
					}
					elseif( $input_type_i == 'checkbox_groups' )
					{
						$content .= "\n\$" . $key . " = explode( ',', \$row['" . $key . "'] );\n";
						$content .= "foreach( \$groups_list as \$key => \$title )\n";
						$content .= "{\n";
						$content .= "\t\$xtpl->assign( 'OPTION', array(\n";
						$content .= "\t\t'key' => \$key,\n";
						$content .= "\t\t'title' => \$title,\n";
						$content .= "\t\t'checked' => in_array( \$key, \$" . $key . " ) ? ' checked=\"checked\"' : ''\n";
						$content .= "\t) );\n";
						$content .= "\t\$xtpl->parse( 'main." . $key . "' );\n";
						$content .= "}\n";
					}
				}
			}

			$content .= "\n";
			$content .= "\$xtpl->parse( 'main' );\n";
			$content .= "\$contents = \$xtpl->text( 'main' );\n\n";

			$content .= "\$page_title = \$lang_module['" . $funname . "'];\n\n";
			$content .= "include NV_ROOTDIR . '/includes/header.php';\n";
			$content .= "echo nv_admin_theme( \$contents );\n";
			$content .= "include NV_ROOTDIR . '/includes/footer.php';";

			file_put_contents( NV_ROOTDIR . "/modules/" . $modname . "/admin/" . $funname . ".php", $content, LOCK_EX );

			//	wite file tpl
			$content_1 = "<!-- BEGIN: main -->\n";
			if( !empty( $array_listviews ) )
			{
				//listviews
				$content_1 .= "<!-- BEGIN: view -->\n";
				if( $search_page )
				{
					$content_1 .= "<form class=\"form-inline\" action=\"{NV_BASE_ADMINURL}index.php\" method=\"get\">\n";
					$content_1 .= "\t<input type=\"hidden\" name=\"{NV_LANG_VARIABLE}\"  value=\"{NV_LANG_DATA}\" />\n";
					$content_1 .= "\t<input type=\"hidden\" name=\"{NV_NAME_VARIABLE}\"  value=\"{MODULE_NAME}\" />\n";
					$content_1 .= "\t<input type=\"hidden\" name=\"{NV_OP_VARIABLE}\"  value=\"{OP}\" />\n";
					$content_1 .= "\t<strong>{LANG.search_title}</strong>&nbsp;<input class=\"form-control\" type=\"text\" value=\"{Q}\" name=\"q\" maxlength=\"255\" />&nbsp;\n";
					$content_1 .= "\t<input class=\"btn btn-primary\" type=\"submit\" value=\"{LANG.search_submit}\" />\n";
					$content_1 .= "</form>\n<br>\n\n";
				}

				$content_1 .= "<form class=\"form-inline\" action=\"{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}\" method=\"post\">\n";
				$content_1 .= "\t<div class=\"table-responsive\">\n\t\t<table class=\"table table-striped table-bordered table-hover\">\n";
				$content_1 .= "\t\t\t<thead>\n";
				$content_1 .= "\t\t\t\t<tr>\n";
				if( empty( $weight_page ) )
				{
					$content_1 .= "\t\t\t\t\t<th>{LANG.number}</th>\n";
				}
				else
				{
					$content_1 .= "\t\t\t\t\t<th>{LANG." . $weight_page . "}</th>\n";
				}
				foreach( $array_listviews as $key => $_tmp )
				{
					$content_1 .= "\t\t\t\t\t<th>{LANG." . $key . "}</th>\n";
				}
				$content_1 .= "\t\t\t\t\t<th>&nbsp;</th>\n";
				$content_1 .= "\t\t\t\t</tr>\n";
				$content_1 .= "\t\t\t</thead>\n";
				if( $generate_page )
				{
					$content_1 .= "\t\t\t<tfoot>\n";
					$content_1 .= "\t\t\t\t<tr>\n";
					$content_1 .= "\t\t\t\t\t<td colspan=\"" . (sizeof( $array_listviews ) + 2) . "\">{NV_GENERATE_PAGE}</td>\n";
					$content_1 .= "\t\t\t\t</tr>\n";
					$content_1 .= "\t\t\t</tfoot>\n";
				}
				$content_1 .= "\t\t\t<tbody>\n";
				$content_1 .= "\t\t\t\t<!-- BEGIN: loop -->\n";
				$content_1 .= "\t\t\t\t<tr>\n";

				if( empty( $weight_page ) )
				{
					$content_1 .= "\t\t\t\t\t<td> {VIEW.number} </td>\n";
				}
				else
				{
					$content_1 .= "\t\t\t\t\t<td>\n\t\t\t\t\t\t<select class=\"form-control\" id=\"id_weight_{VIEW." . $primary . "}\" onchange=\"nv_change_weight('{VIEW." . $primary . "}');\">\n\t\t\t\t\t\t<!-- BEGIN: " . $weight_page . "_loop -->\n\t\t\t\t\t\t\t<option value=\"{WEIGHT.key}\"{WEIGHT.selected}>{WEIGHT.title}</option>\n\t\t\t\t\t\t<!-- END: " . $weight_page . "_loop -->\n\t\t\t\t\t</select>\n\t\t\t\t</td>\n";
					$js_change_weight = "\tfunction nv_change_weight(id) {\n";
					$js_change_weight .= "\t\tvar nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);\n";
					$js_change_weight .= "\t\tvar new_vid = $('#id_weight_' + id).val();\n";
					$js_change_weight .= "\t\t$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=" . $funname . "&nocache=' + new Date().getTime(), 'ajax_action=1&" . $primary . "=' + id + '&new_vid=' + new_vid, function(res) {\n";
					$js_change_weight .= "\t\t\tvar r_split = res.split('_');\n";
					$js_change_weight .= "\t\t\tif (r_split[0] != 'OK') {\n";
					$js_change_weight .= "\t\t\t\talert(nv_is_change_act_confirm[2]);\n";
					$js_change_weight .= "\t\t\t}\n";
					$js_change_weight .= "\t\t\tclearTimeout(nv_timer);\n";
					$js_change_weight .= "\t\t\twindow.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=" . $funname . "';\n";
					$js_change_weight .= "\t\t\treturn;\n";
					$js_change_weight .= "\t\t});\n";
					$js_change_weight .= "\t\treturn;\n";
					$js_change_weight .= "\t}\n";
					$array_field_js['change_weight'] = $js_change_weight;
				}
				foreach( $array_listviews as $key => $_tmp )
				{
					$content_1 .= "\t\t\t\t\t<td> {VIEW." . $key . "} </td>\n";
				}
				$content_1 .= "\t\t\t\t\t<td class=\"text-center\"><i class=\"fa fa-edit fa-lg\">&nbsp;</i> <a href=\"{VIEW.link_edit}\">{LANG.edit}</a> - <em class=\"fa fa-trash-o fa-lg\">&nbsp;</em> <a href=\"{VIEW.link_delete}\" onclick=\"return confirm(nv_is_del_confirm[0]);\">{LANG.delete}</a></td>\n";
				$content_1 .= "\t\t\t\t</tr>\n";
				$content_1 .= "\t\t\t\t<!-- END: loop -->\n";

				$content_1 .= "\t\t\t</tbody>\n";

				$content_1 .= "\t\t</table>\n\t</div>\n";
				$content_1 .= "</form>\n";
				$content_1 .= "<!-- END: view -->\n\n";
			}
			//end listviews

			$content_2 = "<form class=\"form-inline\" action=\"{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}\" method=\"post\">\n";
			if( !empty( $primary ) )
			{
				$content_2 .= "\t<input type=\"hidden\" name=\"" . $primary . "\" value=\"{ROW." . $primary . "}\" />\n";
			}

			$content_2 .= "\t<div class=\"table-responsive\">\n\t\t<table class=\"table table-striped table-bordered table-hover\">\n";
			$content_2 .= "\t\t\t<tbody>\n";

			foreach( $array_views as $key => $input_type_i )
			{
				if( !isset( $array_hiddens[$key] ) or isset( $array_requireds[$key] ) )
				{
					$content_2 .= "\t\t\t\t<tr>\n";
					$content_2 .= "\t\t\t\t\t<td> {LANG." . $key . "} </td>\n";

					$content_2 .= "\t\t\t\t\t<td>";

					if( $input_type_i == 'time' )
					{
						$content_2 .= "<input class=\"form-control\" type=\"text\" pattern=\"^[0-9]{2,2}\$\" name=\"" . $key . "_hour\" value=\"{ROW." . $key . "_hour}\" >:";
						$content_2 .= "<input class=\"form-control\" type=\"text\" pattern=\"^[0-9]{2,2}\$\" name=\"" . $key . "_min\" value=\"{ROW." . $key . "_min}\" >&nbsp;";
					}

					if( $input_type_i == 'textarea' )
					{
						// Nếu là textarea
						$content_2 .= "<textarea class=\"form-control\" style=\"width: 98%; height:100px;\" cols=\"75\" rows=\"5\" name=\"" . $key . "\">{ROW." . $key . "}</textarea>";
					}
					elseif( $input_type_i == 'editor' )
					{
						// Nếu là trình soạn thảo
						$content_2 .= "{ROW." . $key . "}";
					}
					elseif( $input_type_i == 'select' )
					{
						$content_2 .= "<select class=\"form-control\" name=\"" . $key . "\">\n";
						$content_2 .= "\t\t\t\t\t<option value=\"\"> --- </option>\n";
						$content_2 .= "\t\t\t\t\t<!-- BEGIN: select_" . $key . " -->\n";
						$content_2 .= "\t\t\t\t\t<option value=\"{OPTION.key}\" {OPTION.selected}>{OPTION.title}</option>\n";
						$content_2 .= "\t\t\t\t\t<!-- END: select_" . $key . " -->\n";
						$content_2 .= "\t\t\t\t</select>";
					}
					elseif( $input_type_i == 'radio' or $input_type_i == 'checkbox' )
					{
						$type_html = ($input_type_i == 'radio') ? 'radio' : 'checkbox';
						$content_2 .= "\n\t\t\t\t\t<!-- BEGIN: " . $type_html . "_" . $key . " -->\n";
						$content_2 .= "\t\t\t\t\t<input class=\"form-control\" type=\"" . $type_html . "\" name=\"" . $key . "\" value=\"{OPTION.key}\" {OPTION.checked}";

						if( isset( $array_requireds[$key] ) )
						{
							$content_2 .= 'required="required" ';
							if( $oninvalid )
							{
								$content_2 .= "oninvalid=\"setCustomValidity( nv_required )\" oninput=\"setCustomValidity('')\" ";
							}
						}
						$content_2 .= ">{OPTION.title} &nbsp; \n";
						$content_2 .= "\t\t\t\t\t<!-- END: " . $type_html . "_" . $key . " -->\n";
						$content_2 .= "\t\t\t\t";
					}
					elseif( $input_type_i == 'checkbox_groups' )
					{
						$content_2 .= "\n\t\t\t\t\t<!-- BEGIN: " . $key . " -->\n";
						$content_2 .= "\t\t\t\t\t<div class=\"row\">\n";
						$content_2 .= "\t\t\t\t\t\t<label><input class=\"form-control\" type=\"checkbox\" name=\"" . $key . "[]\" value=\"{OPTION.key}\" {OPTION.checked}>{OPTION.title}</label>\n";
						$content_2 .= "\t\t\t\t\t</div>\n";
						$content_2 .= "\t\t\t\t\t<!-- END: " . $key . " -->\n";
						$content_2 .= "\t\t\t\t";
					}
					else
					{
						// Nếu là cá loại input khác
						switch ($input_type_i)
						{
							case 'email':
								$type_html = 'email';
								break;
							case 'url':
								$type_html = 'url';
								break;
							case 'password':
								$type_html = 'password';
								break;
							default:
								$type_html = 'text';
						}

						$oninvalid = true;
						$content_2 .= "<input class=\"form-control\" type=\"" . $type_html . "\" name=\"" . $key . "\" value=\"{ROW." . $key . "}\" ";
						if( $input_type_i == 'date' or $input_type_i == 'time' )
						{
							$content_2 .= 'id="' . $key . '" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" ';
							$array_field_js['date'][] = '#' . $key;
						}
						elseif( $input_type_i == 'textfile' )
						{
							$content_2 .= 'id="id_' . $key . '" ';
							$array_field_js['file'][] = $key;
						}
						elseif( $input_type_i == 'textalias' )
						{
							$content_2 .= 'id="id_' . $key . '" ';
						}
						elseif( $input_type_i == 'email' )
						{
							$content_2 .= "oninvalid=\"setCustomValidity( nv_email )\" oninput=\"setCustomValidity('')\" ";
							$oninvalid = false;
						}
						elseif( $input_type_i == 'url' )
						{
							$content_2 .= "oninvalid=\"setCustomValidity( nv_url )\" oninput=\"setCustomValidity('')\" ";
							$oninvalid = false;
						}
						elseif( $input_type_i == 'number_int' )
						{
							$content_2 .= "pattern=\"^[0-9]*$\"  oninvalid=\"setCustomValidity( nv_digits )\" oninput=\"setCustomValidity('')\" ";
							$oninvalid = false;
						}
						elseif( $input_type_i == 'number_float' )
						{
							$content_2 .= "pattern=\"^([0-9]*)(\.*)([0-9]+)$\" oninvalid=\"setCustomValidity( nv_number )\" oninput=\"setCustomValidity('')\" ";
							$oninvalid = false;
						}

						if( isset( $array_requireds[$key] ) )
						{
							$content_2 .= 'required="required" ';
							if( $oninvalid )
							{
								$content_2 .= "oninvalid=\"setCustomValidity( nv_required )\" oninput=\"setCustomValidity('')\" ";
							}
						}

						$content_2 .= "/>";
						if( $input_type_i == 'textfile' )
						{
							$content_2 .= '&nbsp;<button type="button" class="btn btn-info" id="img_' . $key . '"><i class="fa fa-folder-open-o">&nbsp;</i> Browse server </button>';
						}
						if( $input_type_i == 'textalias' and $array_field_js['textalias'] == $key )
						{
							$content_2 .= "&nbsp;<i class=\"fa fa-refresh fa-lg icon-pointer\" onclick=\"nv_get_alias('id_" . $key . "');\">&nbsp;</i>";
						}
					}
					$content_2 .= "</td>\n";
					$content_2 .= "\t\t\t\t</tr>\n";
				}
			}

			$content_2 .= "\t\t\t</tbody>\n";
			$content_2 .= "\t\t</table>\n";
			$content_2 .= "\t</div>\n";
			$content_2 .= "	<div style=\"text-align: center\"><input class=\"btn btn-primary\" name=\"submit\" type=\"submit\" value=\"{LANG.save}\" /></div>\n";
			$content_2 .= "</form>\n";

			if( !isset( $lang_mod_admin_vi['save'] ) and !isset( $lang_mod_admin_vi_new['save'] ) )
			{
				$lang_mod_admin_vi_new['save'] = 'Lưu thay đổi';
			}

			if( !isset( $lang_mod_admin_en['save'] ) and !isset( $lang_mod_admin_en_new['save'] ) )
			{
				$lang_mod_admin_en_new['save'] = 'Save';
			}

			if( !empty( $array_field_js ) )
			{
				if( isset( $array_field_js['date'] ) )
				{
					$content_1 .= "<link type=\"text/css\" href=\"{NV_BASE_SITEURL}js/ui/jquery.ui.core.css\" rel=\"stylesheet\" />\n";
					$content_1 .= "<link type=\"text/css\" href=\"{NV_BASE_SITEURL}js/ui/jquery.ui.theme.css\" rel=\"stylesheet\" />\n";
					$content_1 .= "<link type=\"text/css\" href=\"{NV_BASE_SITEURL}js/ui/jquery.ui.menu.css\" rel=\"stylesheet\" />\n";
					$content_1 .= "<link type=\"text/css\" href=\"{NV_BASE_SITEURL}js/ui/jquery.ui.datepicker.css\" rel=\"stylesheet\" />\n";
					$content_1 .= "\n";

					$content_2 .= "\n";
					$content_2 .= "<script type=\"text/javascript\" src=\"{NV_BASE_SITEURL}js/ui/jquery.ui.core.min.js\"></script>\n";
					$content_2 .= "<script type=\"text/javascript\" src=\"{NV_BASE_SITEURL}js/ui/jquery.ui.menu.min.js\"></script>\n";
					$content_2 .= "<script type=\"text/javascript\" src=\"{NV_BASE_SITEURL}js/ui/jquery.ui.datepicker.min.js\"></script>\n";
					$content_2 .= "<script type=\"text/javascript\" src=\"{NV_BASE_SITEURL}js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js\"></script>\n";
				}

				$content_2 .= "\n<script type=\"text/javascript\">\n";
				$content_2 .= "//<![CDATA[\n";

				if( isset( $array_field_js['textalias'] ) )
				{
					$content_2 .= "\tfunction nv_get_alias(id) {\n";
					$content_2 .= "\t	var title = strip_tags( $(\"[name='title']\").val() );\n";
					$content_2 .= "\t	if (title != '') {\n";
					$content_2 .= "\t		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=" . $funname . "&nocache=' + new Date().getTime(), 'get_alias_title=' + encodeURIComponent(title), function(res) {\n";
					$content_2 .= "\t			$(\"#\"+id).val( strip_tags( res ) );\n";
					$content_2 .= "\t		});\n";
					$content_2 .= "\t	}\n";
					$content_2 .= "\t	return false;\n";
					$content_2 .= "\t}\n";
				}
				if( isset( $array_field_js['date'] ) )
				{
					$content_2 .= "\t\$(\"" . implode( ',', $array_field_js['date'] ) . "\").datepicker({\n";
					$content_2 .= "\t\tshowOn : \"both\",\n";
					$content_2 .= "\t\tdateFormat : \"dd/mm/yy\",\n";
					$content_2 .= "\t\tchangeMonth : true,\n";
					$content_2 .= "\t\tchangeYear : true,\n";
					$content_2 .= "\t\tshowOtherMonths : true,\n";
					$content_2 .= "\t\tbuttonImage : nv_siteroot + \"images/calendar.gif\",\n";
					$content_2 .= "\t\tbuttonImageOnly : true\n";
					$content_2 .= "\t});\n\n";
				}
				if( isset( $array_field_js['file'] ) )
				{
					foreach( $array_field_js['file'] as $key )
					{
						$content_2 .= "\t\$(\"#img_" . $key . "\").click(function() {\n";
						$content_2 .= "\t\tvar area = \"id_" . $key . "\";\n";
						$content_2 .= "\t\tvar path = \"{NV_UPLOADS_DIR}/{MODULE_NAME}\";\n";
						$content_2 .= "\t\tvar currentpath = \"{NV_UPLOADS_DIR}/{MODULE_NAME}\";\n";
						$content_2 .= "\t\tvar type = \"image\";\n";
						$content_2 .= "\t\tnv_open_browse(script_name + \"?\" + nv_name_variable + \"=upload&popup=1&area=\" + area + \"&path=\" + path + \"&type=\" + type + \"&currentpath=\" + currentpath, \"NVImg\", 850, 420, \"resizable=no,scrollbars=no,toolbar=no,location=no,status=no\");\n";
						$content_2 .= "\t\treturn false;\n";
						$content_2 .= "\t});\n\n";
					}
				}
				if( isset( $array_field_js['change_weight'] ) )
				{
					$content_2 .= $array_field_js['change_weight'] . "\n\n";
				}
				$content_2 .= "//]]>\n";
				$content_2 .= "</script>\n";
			}
			$content_2 .= "<!-- END: main -->";

			file_put_contents( NV_ROOTDIR . "/themes/admin_default/modules/" . $modname . "/" . $funname . ".tpl", $content_1 . $content_2, LOCK_EX );

			nv_write_lang_mod_admin( $modname, 'en', $lang_mod_admin_en_new );
			nv_write_lang_mod_admin( $modname, 'vi', $lang_mod_admin_vi_new );
		}
	}
	catch( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}
}
if( empty( $nb ) )
{
	$modules_exit = nv_scandir( NV_ROOTDIR . '/modules', $global_config['check_module'] );
	foreach( $modules_exit as $mod_i )
	{
		$xtpl->assign( 'MODNAME', array(
			'value' => $mod_i,
			'selected' => ($modname == $mod_i) ? ' selected="selected"' : ''
		) );
		$xtpl->parse( 'main.tablename.modname' );
	}

	if( !empty( $modname ) )
	{
		$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( $db_config['prefix'] . '\_' . NV_LANG_DATA . '\_' . $modname . '%' ) );
		while( $item = $result->fetch() )
		{
			$xtpl->assign( 'MODNAME', array(
				'value' => $item['name'],
				'selected' => ($tablename == $item['name']) ? ' selected="selected"' : ''
			) );
			$xtpl->parse( 'main.tablename.loop' );
		}

		$result = $db->query( 'SHOW TABLE STATUS LIKE ' . $db->quote( $db_config['prefix'] . '\_' . $modname . '%' ) );
		while( $item = $result->fetch() )
		{
			$xtpl->assign( 'MODNAME', array(
				'value' => $item['name'],
				'selected' => ($tablename == $item['name']) ? ' selected="selected"' : ''
			) );
			$xtpl->parse( 'main.tablename.loop' );
		}
	}
	$xtpl->parse( 'main.tablename' );
}
else
{
	$xtpl->parse( 'main.form' );
}
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';