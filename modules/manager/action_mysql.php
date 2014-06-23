<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 18 Jun 2014 10:00:53 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_author`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_book`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_borrow`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_config`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_firtbook`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_librarians`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_pay`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_publishe`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_student`";

$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_toppic`";


$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_author` (
  `id_author` int(11) NOT NULL AUTO_INCREMENT,
  `name_author` varchar(255) NOT NULL COMMENT 'Tên tác giả',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kế tĩnh',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_author`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_book` (
  `id_book` int(11) NOT NULL AUTO_INCREMENT,
  `name_book` varchar(255) NOT NULL COMMENT 'Tên sách',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kết tĩnh',
  `id_toppic` int(11) NOT NULL,
  `name_publisher` varchar(255) NOT NULL COMMENT 'Tên nhà sản xuất',
  `datetime` datetime NOT NULL COMMENT 'Ngày sản xuất',
  `state` varchar(255) NOT NULL COMMENT 'Tình Trạng',
  `name_author` varchar(255) NOT NULL COMMENT 'Tên tác giả',
  `number_page` bigint(20) NOT NULL COMMENT 'Số trang',
  `price` double NOT NULL COMMENT 'Giá',
  `language` varchar(255) NOT NULL COMMENT 'Ngôn ngữ',
  `weight` int(255) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_book`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_borrow` (
  `id_borrow` int(11) NOT NULL AUTO_INCREMENT,
  `id_book` int(11) NOT NULL,
  `name_book` varchar(255) NOT NULL COMMENT 'Tên Sách',
  `name_studient` varchar(255) NOT NULL COMMENT 'Tên sinh viên',
  `date` date NOT NULL COMMENT 'Ngày mượn',
  `timeout` date NOT NULL COMMENT 'Ngày trả',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kết tĩnh',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_borrow`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_config` (
  `config_name` varchar(255) NOT NULL,
  `config_value` text NOT NULL
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_firtbook` (
  `id_firtbook` int(11) NOT NULL AUTO_INCREMENT,
  `name_firtbook` varchar(255) NOT NULL COMMENT 'Tên đầu sách',
  `id_toppic` int(11) NOT NULL,
  `id_author` int(11) NOT NULL,
  `id_publisher` int(11) NOT NULL,
  `date_firtbook` date NOT NULL COMMENT 'Ngày nhập',
  `flies` varchar(255) NOT NULL COMMENT 'Tập',
  `number_firtbook` int(11) NOT NULL COMMENT 'Số trang',
  `publisher` varchar(255) NOT NULL COMMENT 'Nhà sản xuất',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_firtbook`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_librarians` (
  `id_librarians` int(11) NOT NULL AUTO_INCREMENT,
  `name_librarians` varchar(255) NOT NULL COMMENT 'Tên thủ thư',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kế tĩnh',
  `datetime` date NOT NULL COMMENT 'Ngày sinh',
  `address` varchar(255) NOT NULL COMMENT 'Địa chỉ',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_librarians`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_pay` (
  `id_pay` int(11) NOT NULL AUTO_INCREMENT,
  `name_book` varchar(255) NOT NULL COMMENT 'Tên Sách',
  `id_book` int(11) NOT NULL,
  `name_student` varchar(255) NOT NULL COMMENT 'Tên sinh viên ',
  `date_borrow` date NOT NULL COMMENT 'Ngày mượn',
  `date_pay` date NOT NULL COMMENT 'Ngày trả',
  `price` double NOT NULL COMMENT 'Giá',
  `state` varchar(255) NOT NULL COMMENT 'Tình trạng',
  `timeout` date NOT NULL COMMENT 'Thời hạn trả',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_pay`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_publishe` (
  `id_publisher` int(11) NOT NULL AUTO_INCREMENT,
  `name_publisher` varchar(255) NOT NULL COMMENT 'Tên nhà xuất bản ',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kết tĩnh',
  `address` varchar(255) NOT NULL COMMENT 'Địa chỉ ',
  `phone` varchar(255) NOT NULL COMMENT 'SĐT',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_publisher`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_student` (
  `id_student` int(11) NOT NULL AUTO_INCREMENT,
  `name_ student` varchar(255) NOT NULL COMMENT 'Tên sinh viên',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kết tĩnh',
  `datetime` date NOT NULL COMMENT 'Ngày sinh',
  `address` varchar(255) NOT NULL COMMENT 'Địa chỉ',
  `name_class` varchar(255) NOT NULL COMMENT 'Tên lớp ',
  `science` varchar(255) NOT NULL COMMENT 'Khóa',
  `scientific` varchar(255) NOT NULL COMMENT 'Khoa học',
  `timeout` date NOT NULL COMMENT 'Thời hạn',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_student`)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_toppic` (
  `id_toppic` int(11) NOT NULL AUTO_INCREMENT,
  `name_toppic` varchar(255) NOT NULL COMMENT 'Tên Chủ đề',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kế tĩnh',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_toppic`)
) ENGINE=MyISAM;";