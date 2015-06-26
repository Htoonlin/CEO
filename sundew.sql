-- MySQL dump 10.13  Distrib 5.6.21, for Win64 (x86_64)
--
-- Host: localhost    Database: sundew
-- ------------------------------------------------------
-- Server version	5.6.21

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
-- Table structure for table `tbl_account_closing`
--

DROP TABLE IF EXISTS `tbl_account_closing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_account_closing` (
  `closingId` int(11) NOT NULL AUTO_INCREMENT,
  `currencyId` int(11) NOT NULL,
  `receivableId` bigint(20) NOT NULL,
  `openingDate` datetime NOT NULL,
  `openingAmount` int(11) NOT NULL,
  `payableId` bigint(20) DEFAULT NULL,
  `closingDate` datetime DEFAULT NULL,
  `closingAmount` int(11) DEFAULT NULL,
  PRIMARY KEY (`closingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_account_currency`
--

DROP TABLE IF EXISTS `tbl_account_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_account_currency` (
  `currencyId` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_bin NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `rate` float NOT NULL,
  `status` char(1) CHARACTER SET latin1 NOT NULL,
  `entryDate` date NOT NULL,
  PRIMARY KEY (`currencyId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_account_payable`
--

DROP TABLE IF EXISTS `tbl_account_payable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_account_payable` (
  `payVoucherId` bigint(20) NOT NULL AUTO_INCREMENT,
  `voucherNo` varchar(50) NOT NULL,
  `voucherDate` date NOT NULL,
  `accountType` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `withdrawBy` int(11) NOT NULL,
  `approveBy` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `requestedDate` datetime NOT NULL,
  `group_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`payVoucherId`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_account_receivable`
--

DROP TABLE IF EXISTS `tbl_account_receivable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_account_receivable` (
  `receiveVoucherId` bigint(20) NOT NULL AUTO_INCREMENT,
  `voucherNo` varchar(50) NOT NULL,
  `voucherDate` date NOT NULL,
  `accountType` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `depositBy` int(11) NOT NULL,
  `approveBy` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `requestedDate` datetime NOT NULL,
  `group_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`receiveVoucherId`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_account_type`
--

DROP TABLE IF EXISTS `tbl_account_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_account_type` (
  `accountTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parentTypeId` int(11) DEFAULT NULL,
  `baseType` char(1) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`accountTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_calendar`
--

DROP TABLE IF EXISTS `tbl_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_calendar` (
  `calendarId` int(11) NOT NULL AUTO_INCREMENT,
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `linkId` int(11) DEFAULT NULL,
  PRIMARY KEY (`calendarId`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_constant`
--

DROP TABLE IF EXISTS `tbl_constant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_constant` (
  `constantId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(500) NOT NULL,
  `group_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`constantId`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_cr_company`
--

DROP TABLE IF EXISTS `tbl_cr_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cr_company` (
  `companyId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `phone` varchar(255) COLLATE utf8_bin NOT NULL,
  `address` varchar(500) COLLATE utf8_bin NOT NULL,
  `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`companyId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_cr_contact`
--

DROP TABLE IF EXISTS `tbl_cr_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cr_contact` (
  `contactId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `phone` varchar(500) COLLATE utf8_bin NOT NULL,
  `email` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(500) COLLATE utf8_bin NOT NULL,
  `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `companyId` int(11) DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`contactId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_cr_contract`
--

DROP TABLE IF EXISTS `tbl_cr_contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cr_contract` (
  `contractId` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `contactId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `code` varchar(50) COLLATE utf8_bin NOT NULL,
  `amount` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `contractFile` varchar(500) COLLATE utf8_bin NOT NULL,
  `contractBy` int(11) NOT NULL,
  `contractDate` date NOT NULL,
  `notes` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`contractId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_cr_payment`
--

DROP TABLE IF EXISTS `tbl_cr_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cr_payment` (
  `paymentId` int(11) NOT NULL AUTO_INCREMENT,
  `contractId` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `amount` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `paymentDate` date NOT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`paymentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_cr_proposal`
--

DROP TABLE IF EXISTS `tbl_cr_proposal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cr_proposal` (
  `proposalId` int(11) NOT NULL AUTO_INCREMENT,
  `companyId` int(11) NOT NULL,
  `contactId` int(11) NOT NULL,
  `code` varchar(50) COLLATE utf8_bin NOT NULL,
  `amount` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `proposalDate` date NOT NULL,
  `proposalFile` varchar(500) COLLATE utf8_bin NOT NULL,
  `notes` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `proposalBy` int(11) NOT NULL,
  `group_code` varchar(50) COLLATE utf8_bin NOT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`proposalId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_hr_attendance`
--

DROP TABLE IF EXISTS `tbl_hr_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_attendance` (
  `attendanceId` bigint(20) NOT NULL AUTO_INCREMENT,
  `staffId` int(11) NOT NULL,
  `attendanceDate` date NOT NULL,
  `inTime` time DEFAULT NULL,
  `outTime` time DEFAULT NULL,
  PRIMARY KEY (`attendanceId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_hr_department`
--

DROP TABLE IF EXISTS `tbl_hr_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_department` (
  `departmentId` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `team_code` varchar(50) NOT NULL,
  `parentId` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`departmentId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_hr_leave`
--

DROP TABLE IF EXISTS `tbl_hr_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_leave` (
  `leaveId` int(11) NOT NULL AUTO_INCREMENT,
  `status` char(1) NOT NULL,
  `staffId` int(11) NOT NULL,
  `leaveType` char(1) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`leaveId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_hr_payroll`
--

DROP TABLE IF EXISTS `tbl_hr_payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_payroll` (
  `payrollId` int(11) NOT NULL AUTO_INCREMENT,
  `staffId` int(11) NOT NULL,
  `fromDate` date NOT NULL,
  `toDate` date NOT NULL,
  `m_wd` tinyint(4) NOT NULL,
  `s_wd` tinyint(4) NOT NULL,
  `salary` bigint(20) NOT NULL,
  `annual_leave` float NOT NULL,
  `absent` tinyint(4) NOT NULL,
  `bonus` bigint(20) NOT NULL,
  `deduct` bigint(20) NOT NULL,
  PRIMARY KEY (`payrollId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_hr_position`
--

DROP TABLE IF EXISTS `tbl_hr_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_position` (
  `positionId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `min_salary` bigint(20) DEFAULT NULL,
  `max_salary` bigint(20) DEFAULT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`positionId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_hr_staff`
--

DROP TABLE IF EXISTS `tbl_hr_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_staff` (
  `staffId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `staffCode` varchar(50) NOT NULL,
  `staffName` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `positionId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `salary` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `annual_leave` float NOT NULL,
  `permanentDate` date NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`staffId`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_menu`
--

DROP TABLE IF EXISTS `tbl_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_menu` (
  `menuId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `url_type` char(1) NOT NULL,
  `parentId` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `hasDivider` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`menuId`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_menu_permission`
--

DROP TABLE IF EXISTS `tbl_menu_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_menu_permission` (
  `roleId` int(11) NOT NULL,
  `menuId` int(11) NOT NULL,
  PRIMARY KEY (`roleId`,`menuId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_pm_project`
--

DROP TABLE IF EXISTS `tbl_pm_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_pm_project` (
  `projectId` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL,
  `managerId` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `group_code` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `remark` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`projectId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_role`
--

DROP TABLE IF EXISTS `tbl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `parentId` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_route`
--

DROP TABLE IF EXISTS `tbl_route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_route` (
  `routeId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `controller` varchar(255) NOT NULL,
  `constraints` varchar(255) NOT NULL,
  PRIMARY KEY (`routeId`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_route_permission`
--

DROP TABLE IF EXISTS `tbl_route_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_route_permission` (
  `routeId` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `roleId` int(11) NOT NULL,
  PRIMARY KEY (`routeId`,`roleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(200) NOT NULL,
  `password` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `lastLogin` datetime NOT NULL,
  `userRole` int(11) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userName` (`userName`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `vw_account_closing`
--

DROP TABLE IF EXISTS `vw_account_closing`;
/*!50001 DROP VIEW IF EXISTS `vw_account_closing`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_account_closing` AS SELECT 
 1 AS `closingId`,
 1 AS `currencyId`,
 1 AS `currency`,
 1 AS `receivable_voucher`,
 1 AS `receivableId`,
 1 AS `openingDate`,
 1 AS `openingAmount`,
 1 AS `payableId`,
 1 AS `closingDate`,
 1 AS `closingAmount`,
 1 AS `payable_voucher`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_account_payable`
--

DROP TABLE IF EXISTS `vw_account_payable`;
/*!50001 DROP VIEW IF EXISTS `vw_account_payable`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_account_payable` AS SELECT 
 1 AS `payVoucherId`,
 1 AS `voucherNo`,
 1 AS `voucherDate`,
 1 AS `accountType`,
 1 AS `description`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `withdrawBy`,
 1 AS `approveBy`,
 1 AS `status`,
 1 AS `approvedDate`,
 1 AS `reason`,
 1 AS `requestedDate`,
 1 AS `group_code`,
 1 AS `type`,
 1 AS `currencyCode`,
 1 AS `requester`,
 1 AS `approver`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_account_receivable`
--

DROP TABLE IF EXISTS `vw_account_receivable`;
/*!50001 DROP VIEW IF EXISTS `vw_account_receivable`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_account_receivable` AS SELECT 
 1 AS `receiveVoucherId`,
 1 AS `voucherNo`,
 1 AS `voucherDate`,
 1 AS `accountType`,
 1 AS `description`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `depositBy`,
 1 AS `approveBy`,
 1 AS `status`,
 1 AS `approvedDate`,
 1 AS `reason`,
 1 AS `requestedDate`,
 1 AS `group_code`,
 1 AS `type`,
 1 AS `currencyCode`,
 1 AS `requester`,
 1 AS `approver`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_account_voucher`
--

DROP TABLE IF EXISTS `vw_account_voucher`;
/*!50001 DROP VIEW IF EXISTS `vw_account_voucher`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_account_voucher` AS SELECT 
 1 AS `type`,
 1 AS `voucherId`,
 1 AS `voucherNo`,
 1 AS `voucherDate`,
 1 AS `accountTypeId`,
 1 AS `accountType`,
 1 AS `description`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `currency`,
 1 AS `requestBy`,
 1 AS `requester`,
 1 AS `approveBy`,
 1 AS `approver`,
 1 AS `status`,
 1 AS `approvedDate`,
 1 AS `reason`,
 1 AS `requestedDate`,
 1 AS `group_code`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_cr_contact`
--

DROP TABLE IF EXISTS `vw_cr_contact`;
/*!50001 DROP VIEW IF EXISTS `vw_cr_contact`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_cr_contact` AS SELECT 
 1 AS `contactId`,
 1 AS `contactName`,
 1 AS `Phone`,
 1 AS `Email`,
 1 AS `Address`,
 1 AS `Website`,
 1 AS `Notes`,
 1 AS `Tag`,
 1 AS `Status`,
 1 AS `companyname`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_cr_contract`
--

DROP TABLE IF EXISTS `vw_cr_contract`;
/*!50001 DROP VIEW IF EXISTS `vw_cr_contract`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_cr_contract` AS SELECT 
 1 AS `contractId`,
 1 AS `companyId`,
 1 AS `companyName`,
 1 AS `contactId`,
 1 AS `contactName`,
 1 AS `code`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `currencyCode`,
 1 AS `contractDate`,
 1 AS `contractFile`,
 1 AS `notes`,
 1 AS `contractBy`,
 1 AS `status`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_cr_proposal`
--

DROP TABLE IF EXISTS `vw_cr_proposal`;
/*!50001 DROP VIEW IF EXISTS `vw_cr_proposal`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_cr_proposal` AS SELECT 
 1 AS `proposalId`,
 1 AS `companyId`,
 1 AS `contactId`,
 1 AS `code`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `currencyCode`,
 1 AS `proposalDate`,
 1 AS `proposalFile`,
 1 AS `notes`,
 1 AS `proposalBy`,
 1 AS `group_code`,
 1 AS `status`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_hr_attendance`
--

DROP TABLE IF EXISTS `vw_hr_attendance`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_attendance`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_attendance` AS SELECT 
 1 AS `staffCode`,
 1 AS `staffName`,
 1 AS `attendanceId`,
 1 AS `staffId`,
 1 AS `attendanceDate`,
 1 AS `inTime`,
 1 AS `outTime`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_hr_leave`
--

DROP TABLE IF EXISTS `vw_hr_leave`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_leave`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_leave` AS SELECT 
 1 AS `leaveId`,
 1 AS `status`,
 1 AS `staffId`,
 1 AS `leaveType`,
 1 AS `date`,
 1 AS `description`,
 1 AS `staffCode`,
 1 AS `staffName`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_hr_staff`
--

DROP TABLE IF EXISTS `vw_hr_staff`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_staff`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_staff` AS SELECT 
 1 AS `staffId`,
 1 AS `staffName`,
 1 AS `staffCode`,
 1 AS `Salary`,
 1 AS `Currency`,
 1 AS `Birthday`,
 1 AS `Leave`,
 1 AS `PermanentDate`,
 1 AS `Status`,
 1 AS `UserName`,
 1 AS `Position`,
 1 AS `Department`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_payment`
--

DROP TABLE IF EXISTS `vw_payment`;
/*!50001 DROP VIEW IF EXISTS `vw_payment`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_payment` AS SELECT 
 1 AS `paymentId`,
 1 AS `type`,
 1 AS `amount`,
 1 AS `paymentDate`,
 1 AS `status`,
 1 AS `contract`,
 1 AS `currency`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_pm_project`
--

DROP TABLE IF EXISTS `vw_pm_project`;
/*!50001 DROP VIEW IF EXISTS `vw_pm_project`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_pm_project` AS SELECT 
 1 AS `projectId`,
 1 AS `code`,
 1 AS `name`,
 1 AS `description`,
 1 AS `managerId`,
 1 AS `startDate`,
 1 AS `endDate`,
 1 AS `group_code`,
 1 AS `status`,
 1 AS `remark`,
 1 AS `userName`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_user`
--

DROP TABLE IF EXISTS `vw_user`;
/*!50001 DROP VIEW IF EXISTS `vw_user`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_user` AS SELECT 
 1 AS `userId`,
 1 AS `userName`,
 1 AS `password`,
 1 AS `description`,
 1 AS `image`,
 1 AS `status`,
 1 AS `lastLogin`,
 1 AS `userRole`,
 1 AS `rolename`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_account_closing`
--

/*!50001 DROP VIEW IF EXISTS `vw_account_closing`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_closing` AS select `close`.`closingId` AS `closingId`,`close`.`currencyId` AS `currencyId`,`cur`.`code` AS `currency`,`open`.`voucherNo` AS `receivable_voucher`,`close`.`receivableId` AS `receivableId`,`close`.`openingDate` AS `openingDate`,`close`.`openingAmount` AS `openingAmount`,`close`.`payableId` AS `payableId`,`close`.`closingDate` AS `closingDate`,`close`.`closingAmount` AS `closingAmount`,`pay`.`voucherNo` AS `payable_voucher` from (((`tbl_account_closing` `close` join `tbl_account_currency` `cur` on((`close`.`currencyId` = `cur`.`currencyId`))) left join `tbl_account_receivable` `open` on((`close`.`receivableId` = `open`.`receiveVoucherId`))) left join `tbl_account_payable` `pay` on((`close`.`payableId` = `pay`.`payVoucherId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_account_payable`
--

/*!50001 DROP VIEW IF EXISTS `vw_account_payable`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_payable` AS select `pv`.`payVoucherId` AS `payVoucherId`,`pv`.`voucherNo` AS `voucherNo`,`pv`.`voucherDate` AS `voucherDate`,`pv`.`accountType` AS `accountType`,`pv`.`description` AS `description`,`pv`.`amount` AS `amount`,`pv`.`currencyId` AS `currencyId`,`pv`.`withdrawBy` AS `withdrawBy`,`pv`.`approveBy` AS `approveBy`,`pv`.`status` AS `status`,`pv`.`approvedDate` AS `approvedDate`,`pv`.`reason` AS `reason`,`pv`.`requestedDate` AS `requestedDate`,`pv`.`group_code` AS `group_code`,`ty`.`name` AS `type`,`cur`.`code` AS `currencyCode`,concat(`req`.`staffName`,'(',`req`.`staffCode`,')') AS `requester`,concat(`app`.`staffName`,'(',`app`.`staffCode`,')') AS `approver` from ((((`tbl_account_payable` `pv` left join `tbl_account_type` `ty` on((`pv`.`accountType` = `ty`.`accountTypeId`))) left join `tbl_account_currency` `cur` on((`pv`.`currencyId` = `cur`.`currencyId`))) left join `tbl_hr_staff` `req` on((`pv`.`withdrawBy` = `req`.`staffId`))) left join `tbl_hr_staff` `app` on((`pv`.`approveBy` = `app`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_account_receivable`
--

/*!50001 DROP VIEW IF EXISTS `vw_account_receivable`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_receivable` AS select `rv`.`receiveVoucherId` AS `receiveVoucherId`,`rv`.`voucherNo` AS `voucherNo`,`rv`.`voucherDate` AS `voucherDate`,`rv`.`accountType` AS `accountType`,`rv`.`description` AS `description`,`rv`.`amount` AS `amount`,`rv`.`currencyId` AS `currencyId`,`rv`.`depositBy` AS `depositBy`,`rv`.`approveBy` AS `approveBy`,`rv`.`status` AS `status`,`rv`.`approvedDate` AS `approvedDate`,`rv`.`reason` AS `reason`,`rv`.`requestedDate` AS `requestedDate`,`rv`.`group_code` AS `group_code`,`ty`.`name` AS `type`,`cur`.`code` AS `currencyCode`,concat(`req`.`staffName`,'(',`req`.`staffCode`,')') AS `requester`,concat(`app`.`staffName`,'(',`app`.`staffCode`,')') AS `approver` from ((((`tbl_account_receivable` `rv` left join `tbl_account_type` `ty` on((`rv`.`accountType` = `ty`.`accountTypeId`))) left join `tbl_account_currency` `cur` on((`rv`.`currencyId` = `cur`.`currencyId`))) left join `tbl_hr_staff` `req` on((`rv`.`depositBy` = `req`.`staffId`))) left join `tbl_hr_staff` `app` on((`rv`.`approveBy` = `app`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_account_voucher`
--

/*!50001 DROP VIEW IF EXISTS `vw_account_voucher`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_voucher` AS select 'Receivable' AS `type`,`vw_account_receivable`.`receiveVoucherId` AS `voucherId`,`vw_account_receivable`.`voucherNo` AS `voucherNo`,`vw_account_receivable`.`voucherDate` AS `voucherDate`,`vw_account_receivable`.`accountType` AS `accountTypeId`,`vw_account_receivable`.`type` AS `accountType`,`vw_account_receivable`.`description` AS `description`,`vw_account_receivable`.`amount` AS `amount`,`vw_account_receivable`.`currencyId` AS `currencyId`,`vw_account_receivable`.`currencyCode` AS `currency`,`vw_account_receivable`.`depositBy` AS `requestBy`,`vw_account_receivable`.`requester` AS `requester`,`vw_account_receivable`.`approveBy` AS `approveBy`,`vw_account_receivable`.`approver` AS `approver`,`vw_account_receivable`.`status` AS `status`,`vw_account_receivable`.`approvedDate` AS `approvedDate`,`vw_account_receivable`.`reason` AS `reason`,`vw_account_receivable`.`requestedDate` AS `requestedDate`,`vw_account_receivable`.`group_code` AS `group_code` from `vw_account_receivable` union all select 'Payable' AS `type`,`vw_account_payable`.`payVoucherId` AS `voucherId`,`vw_account_payable`.`voucherNo` AS `voucherNo`,`vw_account_payable`.`voucherDate` AS `voucherDate`,`vw_account_payable`.`accountType` AS `accountTypeId`,`vw_account_payable`.`type` AS `accountType`,`vw_account_payable`.`description` AS `description`,`vw_account_payable`.`amount` AS `amount`,`vw_account_payable`.`currencyId` AS `currencyId`,`vw_account_payable`.`currencyCode` AS `currency`,`vw_account_payable`.`withdrawBy` AS `requestBy`,`vw_account_payable`.`requester` AS `requester`,`vw_account_payable`.`approveBy` AS `approveBy`,`vw_account_payable`.`approver` AS `approver`,`vw_account_payable`.`status` AS `status`,`vw_account_payable`.`approvedDate` AS `approvedDate`,`vw_account_payable`.`reason` AS `reason`,`vw_account_payable`.`requestedDate` AS `requestedDate`,`vw_account_payable`.`group_code` AS `group_code` from `vw_account_payable` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_cr_contact`
--

/*!50001 DROP VIEW IF EXISTS `vw_cr_contact`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_contact` AS select `c`.`contactId` AS `contactId`,`c`.`name` AS `contactName`,`c`.`phone` AS `Phone`,`c`.`email` AS `Email`,`c`.`address` AS `Address`,`c`.`website` AS `Website`,`c`.`notes` AS `Notes`,`c`.`tag` AS `Tag`,`c`.`status` AS `Status`,`p`.`name` AS `companyname` from (`tbl_cr_contact` `c` left join `tbl_cr_company` `p` on((`c`.`companyId` = `p`.`companyId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_cr_contract`
--

/*!50001 DROP VIEW IF EXISTS `vw_cr_contract`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_contract` AS select `c`.`contractId` AS `contractId`,`c`.`companyId` AS `companyId`,`com`.`name` AS `companyName`,`c`.`contactId` AS `contactId`,`co`.`name` AS `contactName`,`c`.`code` AS `code`,`c`.`amount` AS `amount`,`c`.`currencyId` AS `currencyId`,`cr`.`code` AS `currencyCode`,`c`.`contractDate` AS `contractDate`,`c`.`contractFile` AS `contractFile`,`c`.`notes` AS `notes`,`c`.`contractBy` AS `contractBy`,`c`.`status` AS `status` from (((`tbl_cr_contract` `c` join `tbl_account_currency` `cr` on((`c`.`currencyId` = `cr`.`currencyId`))) join `tbl_cr_company` `com` on((`c`.`companyId` = `com`.`companyId`))) join `tbl_cr_contact` `co` on((`c`.`contactId` = `co`.`contactId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_cr_proposal`
--

/*!50001 DROP VIEW IF EXISTS `vw_cr_proposal`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_proposal` AS select `tbl_cr_proposal`.`proposalId` AS `proposalId`,`tbl_cr_proposal`.`companyId` AS `companyId`,`tbl_cr_proposal`.`contactId` AS `contactId`,`tbl_cr_proposal`.`code` AS `code`,`tbl_cr_proposal`.`amount` AS `amount`,`tbl_account_currency`.`currencyId` AS `currencyId`,`tbl_account_currency`.`code` AS `currencyCode`,`tbl_cr_proposal`.`proposalDate` AS `proposalDate`,`tbl_cr_proposal`.`proposalFile` AS `proposalFile`,`tbl_cr_proposal`.`notes` AS `notes`,`tbl_cr_proposal`.`proposalBy` AS `proposalBy`,`tbl_cr_proposal`.`group_code` AS `group_code`,`tbl_cr_proposal`.`status` AS `status` from (`tbl_cr_proposal` join `tbl_account_currency` on((`tbl_cr_proposal`.`currencyId` = `tbl_account_currency`.`currencyId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_hr_attendance`
--

/*!50001 DROP VIEW IF EXISTS `vw_hr_attendance`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_attendance` AS select `s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName`,`a`.`attendanceId` AS `attendanceId`,`a`.`staffId` AS `staffId`,`a`.`attendanceDate` AS `attendanceDate`,`a`.`inTime` AS `inTime`,`a`.`outTime` AS `outTime` from (`tbl_hr_attendance` `a` join `tbl_hr_staff` `s` on((`a`.`staffId` = `s`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_hr_leave`
--

/*!50001 DROP VIEW IF EXISTS `vw_hr_leave`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_leave` AS select `l`.`leaveId` AS `leaveId`,(case `l`.`status` when 'R' then 'Requested' when 'A' then 'Approved' when 'C' then 'Rejected' else 'Invalid' end) AS `status`,`l`.`staffId` AS `staffId`,(case `l`.`leaveType` when 'H' then 'Half-day' when 'F' then 'Full-day' when 'M' then 'Medical' when 'A' then 'Absent' else 'Invalid' end) AS `leaveType`,`l`.`date` AS `date`,`l`.`description` AS `description`,`s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName` from (`tbl_hr_leave` `l` join `tbl_hr_staff` `s` on((`l`.`staffId` = `s`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_hr_staff`
--

/*!50001 DROP VIEW IF EXISTS `vw_hr_staff`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_staff` AS select `s`.`staffId` AS `staffId`,`s`.`staffName` AS `staffName`,`s`.`staffCode` AS `staffCode`,`s`.`salary` AS `Salary`,`c`.`code` AS `Currency`,`s`.`birthday` AS `Birthday`,`s`.`annual_leave` AS `Leave`,`s`.`permanentDate` AS `PermanentDate`,`s`.`status` AS `Status`,`u`.`userName` AS `UserName`,`p`.`name` AS `Position`,`d`.`name` AS `Department` from ((((`tbl_hr_staff` `s` left join `tbl_user` `u` on((`s`.`userId` = `u`.`userId`))) left join `tbl_hr_position` `p` on((`p`.`positionId` = `s`.`positionId`))) left join `tbl_hr_department` `d` on((`s`.`departmentId` = `d`.`departmentId`))) left join `tbl_account_currency` `c` on((`s`.`currencyId` = `c`.`currencyId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_payment`
--

/*!50001 DROP VIEW IF EXISTS `vw_payment`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_payment` AS select `p`.`paymentId` AS `paymentId`,`p`.`type` AS `type`,`p`.`amount` AS `amount`,`p`.`paymentDate` AS `paymentDate`,`p`.`status` AS `status`,`c`.`code` AS `contract`,`cu`.`code` AS `currency` from ((`tbl_cr_payment` `p` join `tbl_cr_contract` `c` on((`c`.`contractId` = `p`.`contractId`))) join `tbl_account_currency` `cu` on((`cu`.`currencyId` = `p`.`currencyId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_pm_project`
--

/*!50001 DROP VIEW IF EXISTS `vw_pm_project`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_pm_project` AS select `pm`.`projectId` AS `projectId`,`pm`.`code` AS `code`,`pm`.`name` AS `name`,`pm`.`description` AS `description`,`pm`.`managerId` AS `managerId`,`pm`.`startDate` AS `startDate`,`pm`.`endDate` AS `endDate`,`pm`.`group_code` AS `group_code`,`pm`.`status` AS `status`,`pm`.`remark` AS `remark`,`usr`.`userName` AS `userName` from (`tbl_pm_project` `pm` left join `tbl_user` `usr` on((`pm`.`managerId` = `usr`.`userId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_user`
--

/*!50001 DROP VIEW IF EXISTS `vw_user`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_user` AS select `u`.`userId` AS `userId`,`u`.`userName` AS `userName`,`u`.`password` AS `password`,`u`.`description` AS `description`,`u`.`image` AS `image`,`u`.`status` AS `status`,`u`.`lastLogin` AS `lastLogin`,`u`.`userRole` AS `userRole`,`r`.`name` AS `rolename` from (`tbl_user` `u` join `tbl_role` `r` on((`u`.`userRole` = `r`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-26 18:30:01
