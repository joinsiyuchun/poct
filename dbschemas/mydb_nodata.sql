-- MySQL dump 10.13  Distrib 5.7.25, for osx10.9 (x86_64)
--
-- Host: localhost    Database: operator
-- ------------------------------------------------------
-- Server version	5.7.25

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `think_admin`
--

DROP TABLE IF EXISTS `think_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `username` varchar(50) NOT NULL COMMENT '管理员用户名',
  `password` varchar(128) NOT NULL COMMENT '管理员密码',
  `role_id` int(4) unsigned NOT NULL COMMENT '角色id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1启用 0禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `last_login_time` int(10) unsigned DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_analysis`
--

DROP TABLE IF EXISTS `think_analysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_analysis` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `equipid` varchar(255) NOT NULL,
  `benefit` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) DEFAULT NULL,
  `usage` int(11) DEFAULT NULL,
  `downtime` decimal(12,2) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `cost_depreciation` decimal(12,2) DEFAULT NULL,
  `cost_repair` decimal(12,2) DEFAULT NULL,
  `cost_salary` decimal(12,2) DEFAULT NULL,
  `cost_elec` decimal(12,2) DEFAULT NULL,
  `cost_rent` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_calendar`
--

DROP TABLE IF EXISTS `think_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_calendar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `datelist` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_catagory`
--

DROP TABLE IF EXISTS `think_catagory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_catagory` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品目录id',
  `name` varchar(50) NOT NULL COMMENT '产品目录名称',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属节点id',
  `rules` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1启用 0禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `sort` int(11) DEFAULT '0',
  `img_url` varchar(255) DEFAULT '‘images/1.jpg’',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='产品目录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_daily_cost`
--

DROP TABLE IF EXISTS `think_daily_cost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_daily_cost` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `equipid` varchar(255) NOT NULL,
  `datelist` date NOT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT '0.00',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_dicom`
--

DROP TABLE IF EXISTS `think_dicom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_dicom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deviceId` varchar(255) DEFAULT NULL,
  `studyInstanceUid` varchar(255) DEFAULT NULL,
  `studyId` varchar(255) DEFAULT NULL,
  `patientId` varchar(255) DEFAULT NULL,
  `studyDate` varchar(255) DEFAULT NULL,
  `studyTime` varchar(255) DEFAULT NULL,
  `studyDescription` varchar(255) DEFAULT NULL,
  `modalitiesInStudy` varchar(255) DEFAULT NULL,
  `accessionNumber` varchar(255) DEFAULT NULL,
  `bodyPartExamined` varchar(255) DEFAULT NULL,
  `requestedProcedureDescription` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_group`
--

DROP TABLE IF EXISTS `think_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '维修组id',
  `name` varchar(30) NOT NULL COMMENT '维修组名称',
  `rules` varchar(255) NOT NULL COMMENT '维修组对应的规则',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='维修组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_hisorder`
--

DROP TABLE IF EXISTS `think_hisorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_hisorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `diagnosis_catagory` varchar(48) DEFAULT NULL COMMENT '''医嘱类别（1：门诊 2：住院）''',
  `request_id` int(11) DEFAULT NULL COMMENT '医技请求序号（门诊：处方明细序号，住院：医技请求序号0:未收费或已退费）',
  `diagnosis_no` int(11) DEFAULT NULL COMMENT '医嘱序号（门诊：划价明细序号，住院：医嘱序号）',
  `diagnosis_id` int(11) DEFAULT NULL COMMENT '''医嘱ID（0：查HIS门诊收费项目）''',
  `prescription_id` int(11) DEFAULT NULL COMMENT '''处方序号（门诊处方序号，仅当医嘱ID=0有效）''',
  `pricelist_id` int(11) DEFAULT NULL COMMENT '''费用明细ID（医嘱ID =0时为0）''',
  `diaglist_id` int(11) DEFAULT NULL COMMENT '''医嘱明细ID（yzid=0时为0）''',
  `item_code` varchar(45) DEFAULT NULL COMMENT '''项目编码''',
  `item_name` varchar(45) DEFAULT NULL COMMENT '''项目名称''',
  `item_uom` varchar(45) DEFAULT NULL COMMENT '''项目单位''',
  `item_unitprice` decimal(9,2) DEFAULT NULL COMMENT '项目单价',
  `item_quantity` decimal(9,2) DEFAULT NULL COMMENT '项目数量',
  `item_totalprice` decimal(9,2) DEFAULT NULL COMMENT '项目金额',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_item`
--

DROP TABLE IF EXISTS `think_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'item id',
  `code` varchar(50) NOT NULL COMMENT 'item名称',
  `pid` int(11) unsigned NOT NULL COMMENT '父item id',
  `sort` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `catagoryid` int(11) DEFAULT NULL COMMENT '图标',
  `is_kit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否是菜单项 1 不是 2 是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `brand` varchar(45) DEFAULT NULL,
  `model` varchar(45) DEFAULT NULL,
  `sn` varchar(50) NOT NULL COMMENT '节点路径',
  `pn` varchar(45) DEFAULT NULL,
  `org_list` varchar(255) DEFAULT NULL,
  `image_url` varchar(45) DEFAULT 'images/1.jpg',
  `is_backup` tinyint(1) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `longitude` decimal(24,12) DEFAULT NULL,
  `latitude` decimal(24,12) DEFAULT NULL,
  `purchase_price` decimal(24,12) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='item表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_item_cost`
--

DROP TABLE IF EXISTS `think_item_cost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_item_cost` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(10) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='成本补录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_login_log`
--

DROP TABLE IF EXISTS `think_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `login_username` varchar(30) NOT NULL COMMENT '登录管理员用户名',
  `login_status` tinyint(1) unsigned NOT NULL COMMENT '登录状态：1 登录成功 0 登录失败',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `login_ip` varchar(20) NOT NULL COMMENT '登录ip',
  `login_area` varchar(255) NOT NULL,
  `login_client_os` varchar(255) DEFAULT NULL COMMENT '登录客户端操作系统',
  `login_client_browser` varchar(255) DEFAULT NULL COMMENT '登录客户端浏览器',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8 COMMENT='管理员登录日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_measure_log`
--

DROP TABLE IF EXISTS `think_measure_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_measure_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `operator` varchar(30) NOT NULL COMMENT '检查人员姓名',
  `qc_status` tinyint(1) unsigned NOT NULL COMMENT '检查结果：2部分通过 1 完全通过 0 未通过 ',
  `qc_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '检查时间',
  `item_id` varchar(20) NOT NULL COMMENT '检查设备ID',
  `location` varchar(255) DEFAULT NULL COMMENT '检查设备地点',
  `org_id` varchar(255) DEFAULT NULL COMMENT '组织ID',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='计量检定日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_node`
--

DROP TABLE IF EXISTS `think_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '节点id',
  `name` varchar(50) NOT NULL COMMENT '节点名称',
  `path` varchar(50) NOT NULL COMMENT '节点路径',
  `pid` int(11) unsigned NOT NULL COMMENT '所属节点id',
  `sort` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `icon` varchar(50) DEFAULT NULL COMMENT '图标',
  `is_menu` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否是菜单项 1 不是 2 是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='权限节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_notification`
--

DROP TABLE IF EXISTS `think_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_notification` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '报修单id',
  `code` varchar(45) DEFAULT NULL,
  `creater_id` int(4) unsigned NOT NULL COMMENT '创建者id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用 0禁用',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `org_id` int(11) unsigned NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='报修单头表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_org`
--

DROP TABLE IF EXISTS `think_org`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_org` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构id',
  `name` varchar(30) NOT NULL COMMENT '机构名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `pid` int(11) DEFAULT '0',
  `sort` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='机构表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_org_catagory`
--

DROP TABLE IF EXISTS `think_org_catagory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_org_catagory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(10) unsigned NOT NULL DEFAULT '0',
  `catagory_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='类别组织关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_pricelist`
--

DROP TABLE IF EXISTS `think_pricelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_pricelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `insurance_code` text,
  `status` text,
  `start_date` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `price_code` text,
  `payment_method` text,
  `item_name` text,
  `item_desc` text,
  `exception` text,
  `pcs` text,
  `unit_price` double DEFAULT NULL,
  `memo` text,
  `coverage` text,
  `catagory` text,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10070 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_quality_log`
--

DROP TABLE IF EXISTS `think_quality_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_quality_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `operator` varchar(30) NOT NULL COMMENT '检查人员姓名',
  `qc_status` tinyint(1) unsigned NOT NULL COMMENT '检查结果：2部分通过 1 完全通过 0 未通过 ',
  `qc_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '检查时间',
  `item_id` varchar(20) NOT NULL COMMENT '检查设备ID',
  `location` varchar(255) DEFAULT NULL COMMENT '检查设备地点',
  `org_id` varchar(255) DEFAULT NULL COMMENT '组织ID',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='质量检查日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_role`
--

DROP TABLE IF EXISTS `think_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `name` varchar(30) NOT NULL COMMENT '角色名称',
  `rules` varchar(255) NOT NULL COMMENT '角色拥有的权限节点',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_setting`
--

DROP TABLE IF EXISTS `think_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_setting` (
  `name` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_settlement`
--

DROP TABLE IF EXISTS `think_settlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_settlement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `org_id` int(11) unsigned NOT NULL,
  `workorderlist` varchar(255) DEFAULT NULL COMMENT '报修单号列表',
  `settle_type` int(11) DEFAULT NULL COMMENT '结算类型：保内、保外',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `settled_by` tinyint(1) unsigned DEFAULT NULL COMMENT '接修者',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `amount` decimal(11,0) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='结算表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_singledia_info`
--

DROP TABLE IF EXISTS `think_singledia_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_singledia_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) DEFAULT NULL,
  `item_name` text,
  `item_id` text,
  `pid` text,
  `patient_source` text,
  `request_id` bigint(20) DEFAULT NULL,
  `diag_code` text,
  `diag_name` text,
  `function` text,
  `part` text,
  `report_doctor` text,
  `department` text,
  `prescribe_date` datetime DEFAULT NULL,
  `appoint_date` datetime DEFAULT NULL,
  `inspection_date` datetime DEFAULT NULL,
  `report_date` datetime DEFAULT NULL,
  `opertator_name` text,
  `reporter_name` text,
  `is_positive` text,
  `profit` decimal(10,2) DEFAULT '0.00',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1811 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_transfer_log`
--

DROP TABLE IF EXISTS `think_transfer_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_transfer_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `operator` varchar(30) NOT NULL COMMENT '经办人员姓名',
  `status` tinyint(1) unsigned NOT NULL COMMENT '归还状态：2部分损坏 1 报废 0 完好 ',
  `transfer_time` datetime NOT NULL COMMENT '检查时间',
  `item_id` varchar(20) NOT NULL COMMENT '设备ID',
  `location` varchar(255) DEFAULT NULL COMMENT '转出/转入地点',
  `org_id` varchar(255) DEFAULT NULL COMMENT '组织ID',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `type` int(11) NOT NULL COMMENT '0:转出/1:转入',
  `health` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='备机转移日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_user`
--

DROP TABLE IF EXISTS `think_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL DEFAULT '',
  `user_name` varchar(45) DEFAULT NULL,
  `gender` varchar(12) DEFAULT NULL,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_user2`
--

DROP TABLE IF EXISTS `think_user2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_user2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL DEFAULT '',
  `user_name` varchar(45) DEFAULT NULL,
  `gender` varchar(12) DEFAULT NULL,
  `create_time` int(10) unsigned DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_user_org`
--

DROP TABLE IF EXISTS `think_user_org`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_user_org` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `org_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expire_time` int(10) unsigned DEFAULT NULL COMMENT '过期时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `think_workorder`
--

DROP TABLE IF EXISTS `think_workorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `think_workorder` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `org_id` int(11) unsigned NOT NULL,
  `notification_id` int(11) unsigned NOT NULL COMMENT '报修单号',
  `item_id` int(11) unsigned NOT NULL COMMENT '设备id',
  `location` varchar(255) DEFAULT NULL COMMENT '设备位置',
  `settle_id` int(11) DEFAULT NULL COMMENT '结算类型：保内、保外',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1 启用 0 禁用',
  `receptor_id` tinyint(1) unsigned DEFAULT NULL COMMENT '接修者',
  `is_halt` tinyint(1) unsigned DEFAULT '1' COMMENT '是否停机',
  `halt_time` int(10) unsigned DEFAULT '0' COMMENT '停机时长（秒）',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `accept_time` datetime DEFAULT NULL,
  `complete_time` datetime DEFAULT NULL,
  `result` int(10) unsigned DEFAULT NULL,
  `fault_reason` int(10) unsigned DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `completed_by` int(10) DEFAULT NULL,
  `cost` decimal(11,2) DEFAULT '0.00',
  `service_cost` decimal(11,2) DEFAULT '0.00',
  `accessory_cost` decimal(11,2) DEFAULT '0.00',
  `report_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8 COMMENT='工单表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-09-22 11:29:18
