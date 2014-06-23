<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Fri, 20 Jun 2014 06:27:47 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_picture`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_slide`";


$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_picture` (
  `pictureid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Tên ảnh',
  `slideid` int(11) NOT NULL COMMENT 'Tên slide',
  `path` varchar(255) NOT NULL COMMENT 'Ảnh đại diện',
  `description` text NOT NULL COMMENT 'mô tả',
  `numview` int(11) NOT NULL COMMENT 'số lượng',
  `thumb_name` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`pictureid`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_slide` (
  `slideid` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL COMMENT 'Liên kế tĩnh',
  `name` varchar(255) NOT NULL COMMENT 'Tên slide',
  `description` text NOT NULL COMMENT 'mô tả',
  `createddate` int(11) NOT NULL COMMENT 'Ngày thêm',
  `num_photo` int(11) NOT NULL,
  `path_img` varchar(255) NOT NULL COMMENT 'Ảnh đại diện',
  `num_view` int(11) NOT NULL,
  `active` int(11) NOT NULL COMMENT 'Hoạt động',
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`slideid`)
) ENGINE=MyISAM;";