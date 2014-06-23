<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 18 Jun 2014 13:03:01 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS `" . $db_config['prefix'] . "_" . $module_data . "_student`";


$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE `" . $db_config['prefix'] . "_" . $module_data . "_student` (
  `id_student` int(11) NOT NULL AUTO_INCREMENT,
  `name_student` varchar(255) NOT NULL COMMENT 'Tên sinh viên',
  `alias` varchar(255) NOT NULL COMMENT 'Liên kết tĩnh',
  `datetime` int(11) NOT NULL COMMENT 'Ngày sinh',
  `address` varchar(255) NOT NULL COMMENT 'Địa chỉ',
  `name_class` varchar(255) NOT NULL COMMENT 'Tên lớp ',
  `science` varchar(255) NOT NULL COMMENT 'Khóa',
  `scientific` varchar(255) NOT NULL COMMENT 'Khoa học',
  `timeout` int(11) NOT NULL COMMENT 'Thời hạn',
  `weight` int(11) NOT NULL COMMENT 'Stt',
  PRIMARY KEY (`id_student`)
) ENGINE=MyISAM;";