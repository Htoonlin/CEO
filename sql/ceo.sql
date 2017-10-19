-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: sundew_dgk
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

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
-- Table structure for table `tbl_account_asset`
--

DROP TABLE IF EXISTS `tbl_account_asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_account_asset` (
  `assetId` int(11) NOT NULL,
  `assetType` char(1) COLLATE utf8_bin NOT NULL,
  `category` varchar(50) COLLATE utf8_bin NOT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL,
  `amount` float NOT NULL,
  `currencyId` int(11) NOT NULL,
  `linkCode` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `isLiquid` tinyint(1) NOT NULL,
  `isTangible` tinyint(1) NOT NULL,
  `remark` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_account_asset`
--

LOCK TABLES `tbl_account_asset` WRITE;
/*!40000 ALTER TABLE `tbl_account_asset` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_account_asset` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`closingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_account_closing`
--

LOCK TABLES `tbl_account_closing` WRITE;
/*!40000 ALTER TABLE `tbl_account_closing` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_account_closing` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`currencyId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_account_currency`
--

LOCK TABLES `tbl_account_currency` WRITE;
/*!40000 ALTER TABLE `tbl_account_currency` DISABLE KEYS */;
INSERT INTO `tbl_account_currency` VALUES (1,'MMK','Myanmar Kyats',1,'A','2017-05-26',NULL,NULL,'2017-05-26 18:18:58',1,NULL,NULL);
/*!40000 ALTER TABLE `tbl_account_currency` ENABLE KEYS */;
UNLOCK TABLES;

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
  `paymentType` char(1) NOT NULL DEFAULT 'C',
  `attachmentFile` varchar(500) DEFAULT NULL,
  `currencyId` int(11) NOT NULL,
  `withdrawBy` int(11) NOT NULL,
  `approveBy` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `requestedDate` datetime NOT NULL,
  `group_code` varchar(50) DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`payVoucherId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_account_payable`
--

LOCK TABLES `tbl_account_payable` WRITE;
/*!40000 ALTER TABLE `tbl_account_payable` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_account_payable` ENABLE KEYS */;
UNLOCK TABLES;

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
  `paymentType` char(1) NOT NULL DEFAULT 'C',
  `attachmentFile` varchar(500) DEFAULT NULL,
  `currencyId` int(11) NOT NULL,
  `depositBy` int(11) NOT NULL,
  `approveBy` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `requestedDate` datetime NOT NULL,
  `group_code` varchar(50) DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`receiveVoucherId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_account_receivable`
--

LOCK TABLES `tbl_account_receivable` WRITE;
/*!40000 ALTER TABLE `tbl_account_receivable` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_account_receivable` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`accountTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_account_type`
--

LOCK TABLES `tbl_account_type` WRITE;
/*!40000 ALTER TABLE `tbl_account_type` DISABLE KEYS */;
INSERT INTO `tbl_account_type` VALUES (1,'A000','Default Account',NULL,'B','A',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(2,'I001','Default Income',1,'I','A',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(3,'E001','Default Expense',1,'E','A',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(4,'A999','Closing Process',NULL,'B','A',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL);
/*!40000 ALTER TABLE `tbl_account_type` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`calendarId`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_calendar`
--

LOCK TABLES `tbl_calendar` WRITE;
/*!40000 ALTER TABLE `tbl_calendar` DISABLE KEYS */;
INSERT INTO `tbl_calendar` VALUES (1,25,12,NULL,'holiday_y','X\'mas',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(2,1,5,2015,'holiday_y','Labour day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(3,13,4,2015,'holiday','Water Festival',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(4,14,4,2015,'holiday','Water Festival',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(5,15,4,2015,'holiday','Water Festival',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(6,16,4,2015,'holiday','Water Festival',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(7,17,4,2015,'holiday','Myanmar New Year',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(8,4,1,2015,'holiday_y','Independence Day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(9,12,2,2015,'holiday_y','Union Day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(10,4,3,2015,'holiday','Ta Paung Festival',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(11,27,3,2015,'holiday_y','Armed Forces Day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(12,2,3,2015,'holiday','Peasants Day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(13,2,5,2015,'holiday','Full Moon of Kason',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(14,19,7,2015,'holiday_y','Arr Zar Ni Day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(15,31,7,2015,'holiday','Full Moon of Warso',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(16,28,10,2015,'holiday','Full Moon of Thitin Kyut',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(17,26,11,2015,'holiday','Full Moon Of Ta Saung Taine',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(18,6,12,2015,'holiday','National Day',NULL,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(21,1,8,2015,'holiday_w','Weekend Holiday',NULL,NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-15 17:28:35',1),(22,3,9,2015,'holiday_y','Test Sep Holiday',NULL,'2015-09-15 17:28:37',1,'2015-09-15 17:28:02',1,'2015-09-15 17:28:37',1),(23,23,3,2016,'holiday','Full Moon Day',NULL,NULL,NULL,'2016-03-13 11:22:12',1,NULL,NULL);
/*!40000 ALTER TABLE `tbl_calendar` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`constantId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_constant`
--

LOCK TABLES `tbl_constant` WRITE;
/*!40000 ALTER TABLE `tbl_constant` DISABLE KEYS */;
INSERT INTO `tbl_constant` VALUES (1,'default_status','{\r\n    \"A\": \"Active\",\r\n    \"D\": \"Inactive\",\r\n        \"P\":\"Permance\"\r\n    \r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,'2015-10-17 10:46:42',1),(2,'routing_url_type','{\r\n    \"N\":\"Normal\",\r\n    \"R\":\"Route\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(3,'account_base_type','{\r\n    \"I\":\"၀င္ေငြ\",\r\n    \"E\":\"ထြက္ေငြ\",\r\n    \"B\":\"Both\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(4,'leave_status','{\r\n    \"R\":\"Requested\",\r\n    \"A\":\"Approved\",\r\n    \"C\":\"Rejected\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(5,'leave_type','[\r\n    {\"id\": \"F\", \"title\": \"Full-day (Annual)\",  \"value\":1},\r\n    {\"id\": \"H\", \"title\": \"Half-day (Annual)\", \"value\":0.5},\r\n    {\"id\": \"M\", \"title\": \"Medical\", \"value\":1}\r\n]','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(6,'holiday_type','{\r\n    \"holiday\":\"Holiday\",\r\n    \"holiday_y\":\"Yearly Holiday\",\r\n    \"holiday_m\":\"Monthly Holiday\",\r\n    \"holiday_w\":\"Weekly Holiday\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(8,'late_condition','[\r\n    {\"code\":\"L20\", \"minute\":20, \"title\": \"20 mins\"},\r\n    {\"code\":\"L45\", \"minute\":45, \"title\": \"45 mins\"}\r\n]','payroll',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(9,'payroll_formula','{\r\n    \"(P*(W + L))-(((L45/3)*P)+((L20/3)*(P/2)))\" : \"Default Formula\"\r\n}','payroll',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(10,'company_types','{\r\n    \"A\": \"Type-A\",\r\n    \"B\": \"Type-B\",\r\n    \"C\": \"Type-C\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(11,'closing_type_id','{\r\n    \"open\":4,\r\n    \"close\":4\r\n}','system_use',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(12,'account_status','{\r\n    \"R\":\"Requested\",\r\n    \"A\":\"Approved\",\r\n    \"C\":\"Canceled\",\r\n    \"F\":\"Finished\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(13,'generate_types','{\r\n    \"E\": \"Entity\",\r\n    \"H\": \"Helper\",\r\n    \"D\": \"DataAccess\",\r\n    \"C\": \"Controller\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(14,'task_status','{\r\n    \"A\": \"Assigned\",\r\n    \"P\": \"Processing\",\r\n    \"F\": \"Finished\",\r\n    \"C\": \"Completed\",\r\n    \"L\": \"Failed\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(17,'payment_type','{\r\n    \"B\": \"Bank\",\r\n    \"C\": \"Cash\"\r\n}','combo_data',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(18,'payroll_formula 2','{\r\n    \"(P*(W + L))-(((L45/3)*P)+((L20/3)*(P/2)))\" : \"Default Formula\"\r\n}','payroll','2015-09-15 17:12:58',1,'2015-09-15 17:12:29',1,'2015-09-15 17:12:58',1);
/*!40000 ALTER TABLE `tbl_constant` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`companyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cr_company`
--

LOCK TABLES `tbl_cr_company` WRITE;
/*!40000 ALTER TABLE `tbl_cr_company` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_cr_company` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`contactId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cr_contact`
--

LOCK TABLES `tbl_cr_contact` WRITE;
/*!40000 ALTER TABLE `tbl_cr_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_cr_contact` ENABLE KEYS */;
UNLOCK TABLES;

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
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `amount` float NOT NULL,
  `currencyId` int(11) NOT NULL,
  `contractFile` varchar(500) COLLATE utf8_bin NOT NULL,
  `contractBy` int(11) NOT NULL,
  `contractDate` date NOT NULL,
  `notes` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`contractId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cr_contract`
--

LOCK TABLES `tbl_cr_contract` WRITE;
/*!40000 ALTER TABLE `tbl_cr_contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_cr_contract` ENABLE KEYS */;
UNLOCK TABLES;

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
  `staffId` int(11) NOT NULL,
  `contactId` int(11) NOT NULL,
  `remark` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`paymentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cr_payment`
--

LOCK TABLES `tbl_cr_payment` WRITE;
/*!40000 ALTER TABLE `tbl_cr_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_cr_payment` ENABLE KEYS */;
UNLOCK TABLES;

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
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `amount` float NOT NULL,
  `currencyId` int(11) NOT NULL,
  `proposalDate` date NOT NULL,
  `proposalFile` varchar(500) COLLATE utf8_bin NOT NULL,
  `notes` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `proposalBy` int(11) NOT NULL,
  `group_code` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`proposalId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cr_proposal`
--

LOCK TABLES `tbl_cr_proposal` WRITE;
/*!40000 ALTER TABLE `tbl_cr_proposal` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_cr_proposal` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`attendanceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hr_attendance`
--

LOCK TABLES `tbl_hr_attendance` WRITE;
/*!40000 ALTER TABLE `tbl_hr_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hr_attendance` ENABLE KEYS */;
UNLOCK TABLES;

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
  `description` varchar(500) DEFAULT NULL,
  `group_code` varchar(50) NOT NULL,
  `parentId` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `status` char(1) NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`departmentId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hr_department`
--

LOCK TABLES `tbl_hr_department` WRITE;
/*!40000 ALTER TABLE `tbl_hr_department` DISABLE KEYS */;
INSERT INTO `tbl_hr_department` VALUES (1,'Sundew','Head Office',NULL,'',NULL,0,'A',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(2,'MeShop','Point Of Sale','adfasf','afa',1,0,'A',NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-15 17:16:40',1),(3,'','Point Of Sale','asfdasfas','asfsafas',1,0,'A','2015-09-15 17:16:53',1,'2015-09-15 17:16:34',1,'2015-09-15 17:16:53',1);
/*!40000 ALTER TABLE `tbl_hr_department` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`leaveId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hr_leave`
--

LOCK TABLES `tbl_hr_leave` WRITE;
/*!40000 ALTER TABLE `tbl_hr_leave` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hr_leave` ENABLE KEYS */;
UNLOCK TABLES;

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
  `s_wd` float NOT NULL,
  `salary` float NOT NULL,
  `currencyId` int(11) NOT NULL,
  `bankCode` varchar(50) DEFAULT NULL,
  `leave` float NOT NULL,
  `absent` float NOT NULL,
  `formula` varchar(500) NOT NULL,
  `Late` varchar(500) DEFAULT NULL,
  `netSalary` float NOT NULL,
  `managerId` int(11) NOT NULL,
  `status` char(1) NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`payrollId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hr_payroll`
--

LOCK TABLES `tbl_hr_payroll` WRITE;
/*!40000 ALTER TABLE `tbl_hr_payroll` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hr_payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_hr_position`
--

DROP TABLE IF EXISTS `tbl_hr_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hr_position` (
  `positionId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `min_salary` bigint(20) DEFAULT NULL,
  `max_salary` bigint(20) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`positionId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hr_position`
--

LOCK TABLES `tbl_hr_position` WRITE;
/*!40000 ALTER TABLE `tbl_hr_position` DISABLE KEYS */;
INSERT INTO `tbl_hr_position` VALUES (1,'System Admin',1,1,99999999,'A',NULL,NULL,'2017-05-26 18:19:21',1,NULL,NULL),(2,'Digital Marketing Manager ',1,600000,600000,'A',NULL,NULL,'2017-05-28 22:27:44',1,NULL,NULL);
/*!40000 ALTER TABLE `tbl_hr_position` ENABLE KEYS */;
UNLOCK TABLES;

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
  `workHours` varchar(250) NOT NULL,
  `salary` bigint(20) NOT NULL,
  `currencyId` int(11) NOT NULL,
  `annual_leave` float NOT NULL,
  `permanentDate` date NOT NULL,
  `bankCode` varchar(50) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`staffId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hr_staff`
--

LOCK TABLES `tbl_hr_staff` WRITE;
/*!40000 ALTER TABLE `tbl_hr_staff` DISABLE KEYS */;
INSERT INTO `tbl_hr_staff` VALUES (1,1,'SA0000','SUNDEW','2017-05-26',1,1,'{\"1\":{\"from\":\"09:00\",\"to\":\"18:00\"},\"2\":{\"from\":\"09:00\",\"to\":\"18:00\"},\"3\":{\"from\":\"09:00\",\"to\":\"18:00\"},\"4\":{\"from\":\"09:00\",\"to\":\"18:00\"},\"5\":{\"from\":\"09:00\",\"to\":\"18:00\"}}',1,1,14,'2017-05-26',NULL,'A',NULL,NULL,'2017-05-26 18:20:02',1,NULL,NULL);
/*!40000 ALTER TABLE `tbl_hr_staff` ENABLE KEYS */;
UNLOCK TABLES;

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
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`menuId`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_menu`
--

LOCK TABLES `tbl_menu` WRITE;
/*!40000 ALTER TABLE `tbl_menu` DISABLE KEYS */;
INSERT INTO `tbl_menu` VALUES (1,'Dashboard','User dashboard','glyphicon glyphicon-home','dashboard','R',NULL,0,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(2,'System','System module','glyphicon glyphicon-flash','#','N',NULL,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(3,'User','User management','glyphicon glyphicon-user','user','R',10,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(4,'Role','Role management','fa fa-users','role','R',10,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(6,'Management','System Management','glyphicon glyphicon-cog','#','N',2,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(8,'Constant','Constant values and formula fields','fa fa-key','constant','R',6,3,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(9,'Menu','Menu management','glyphicon glyphicon-list-alt','menu','R',6,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(10,'Admin','Administration','glyphicon glyphicon-briefcase','#','N',2,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(11,'HR','Human Resource Module','glyphicon glyphicon-heart','#','N',NULL,3,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(12,'Position','Position Menu','glyphicon glyphicon-align-left','hr_position','R',25,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(13,'Account','Accounting module','glyphicon glyphicon-usd','#','N',NULL,4,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(14,'Type','Account type menu','glyphicon glyphicon-th-list','account_type','R',30,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(16,'Routing','Route manager','glyphicon glyphicon-road','route','R',6,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(17,'Management','Manage requested vouchers','glyphicon glyphicon-book','#','N',13,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(20,'Approval','Request Voucher Approval','fa fa-list-ul','account_voucher','R',17,3,1,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(21,'Department','Department management','glyphicon glyphicon-th-large','hr_department','R',25,2,1,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(22,'Staff Register','Staff Management','glyphicon glyphicon-user','hr_staff','R',25,3,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(23,'Attendance board','Attendance management','glyphicon glyphicon-pencil','hr_attendance','R',11,4,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(24,'Payroll','Payroll management','fa fa-smile-o','hr_payroll','R',41,3,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(25,'HR Setup','HR setup ','glyphicon glyphicon-edit','#','N',11,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(27,'Currency','Currency Management','fa fa-money','account_currency','R',30,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(29,'CRM','Customer relation management System','glyphicon glyphicon-star','#','N',NULL,7,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(30,'Account Setup','Account Setup Module','glyphicon glyphicon-edit','#','N',13,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(31,'Companies','Company registration','glyphicon glyphicon-certificate','cr_company','R',29,2,1,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(32,'Management','CRM contract and proposal','glyphicon glyphicon-briefcase','#','N',29,3,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(33,'Contact','Contact management','glyphicon glyphicon-user','cr_contact','R',29,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(34,'Proposal','Proposal files','glyphicon glyphicon-file','cr_proposal','R',32,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(35,'Contract','Contracted by customer files','glyphicon glyphicon-pencil','cr_contract','R',32,2,0,NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-28 10:48:00',1),(36,'Payment','Payment management for contracts','glyphicon glyphicon-thumbs-up','#','N',32,4,0,NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-28 10:48:08',1),(37,'Balance Sheet','Account balance sheet. It will show all transaction after closing date.','glyphicon glyphicon-list-alt','account_balance','R',17,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(38,'Project','Project Management Module','glyphicon glyphicon-paperclip','#','N',NULL,5,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(39,'Project Setup','Project Setup Forms','glyphicon glyphicon-edit','pm_project','R',38,0,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(40,'Holidays',NULL,'glyphicon glyphicon-tree-conifer','hr_holiday','R',41,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(41,'Management','Human Resource Manager','fa fa-child','#','N',11,2,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(42,'Leave','Leave management','fa fa-user-md','hr_leave','R',41,2,1,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(43,'Development','Development Tools','fa fa-github-square','#','N',NULL,7,0,NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-28 13:54:04',1),(44,'Generate','Code generation tools','glyphicon glyphicon-export','development_generate','R',43,1,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(45,'Withdraw','Withdraw Money from company','glyphicon glyphicon-open','account_payable','R',47,4,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(46,'Deposit','Deposit money to company','glyphicon glyphicon-save','account_receivable','R',47,5,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(47,'My Account','Account management for staff','glyphicon glyphicon-user','#','N',13,0,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(48,'Report','Account reporting','fa fa-bar-chart','account_report','R',17,4,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(49,'Task Manager','Task management','glyphicon glyphicon-tasks','pm_task','R',38,0,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(50,'Preferences','System Preferences','fa fa-cogs','preferences','R',2,4,0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(51,'Dashboard 3','User dashboard','glyphicon glyphicon-home','dashboard','R',NULL,0,0,'2015-09-15 17:12:01',1,'2015-09-15 17:11:51',1,'2015-09-15 17:12:01',1),(52,'a',NULL,'glyphicon glyphicon-font','#','N',NULL,0,0,'2016-03-17 16:56:07',1,'2016-03-17 16:54:46',1,'2016-03-17 16:56:07',1);
/*!40000 ALTER TABLE `tbl_menu` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tbl_menu_permission`
--

LOCK TABLES `tbl_menu_permission` WRITE;
/*!40000 ALTER TABLE `tbl_menu_permission` DISABLE KEYS */;
INSERT INTO `tbl_menu_permission` VALUES (1,1),(1,2),(1,3),(1,4),(1,6),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,16),(1,17),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,27),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(1,35),(1,37),(1,38),(1,39),(1,40),(1,41),(1,42),(1,43),(1,44),(1,45),(1,46),(1,47),(1,48),(1,49),(1,50),(2,1),(2,13),(2,45),(2,46),(2,47),(3,13),(3,14),(3,17),(3,20),(3,27),(3,30),(3,37),(3,48),(4,2),(4,3),(4,10),(4,11),(4,12),(4,21),(4,22),(4,23),(4,24),(4,25),(4,40),(4,41),(4,42);
/*!40000 ALTER TABLE `tbl_menu_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_pm_comment`
--

DROP TABLE IF EXISTS `tbl_pm_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_pm_comment` (
  `commentId` bigint(20) NOT NULL AUTO_INCREMENT,
  `taskId` int(11) NOT NULL,
  `commentText` text COLLATE utf8_bin NOT NULL,
  `commentBy` int(11) NOT NULL,
  `commentDate` int(11) NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`commentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_pm_comment`
--

LOCK TABLES `tbl_pm_comment` WRITE;
/*!40000 ALTER TABLE `tbl_pm_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_pm_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_pm_flow`
--

DROP TABLE IF EXISTS `tbl_pm_flow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_pm_flow` (
  `flowId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `flowType` char(1) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL,
  `topId` int(11) DEFAULT NULL,
  `bottomId` int(11) DEFAULT NULL,
  `leftId` int(11) DEFAULT NULL,
  `rightId` int(11) DEFAULT NULL,
  `topText` int(11) NOT NULL,
  `bottomText` int(11) NOT NULL,
  `leftText` int(11) NOT NULL,
  `rightText` int(11) NOT NULL,
  `eventLink` varchar(500) COLLATE utf8_bin NOT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`flowId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_pm_flow`
--

LOCK TABLES `tbl_pm_flow` WRITE;
/*!40000 ALTER TABLE `tbl_pm_flow` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_pm_flow` ENABLE KEYS */;
UNLOCK TABLES;

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
  `repository` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `managerId` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `group_code` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `remark` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`projectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_pm_project`
--

LOCK TABLES `tbl_pm_project` WRITE;
/*!40000 ALTER TABLE `tbl_pm_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_pm_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_pm_task`
--

DROP TABLE IF EXISTS `tbl_pm_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_pm_task` (
  `taskId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_bin NOT NULL,
  `current` float NOT NULL,
  `staffId` int(11) NOT NULL,
  `fromTime` date NOT NULL,
  `toTime` date NOT NULL,
  `projectId` int(11) DEFAULT NULL,
  `predecessorId` int(11) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `maxBudget` float DEFAULT NULL,
  `currencyId` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `description` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  `tag` varchar(50) COLLATE utf8_bin NOT NULL,
  `managerId` int(11) NOT NULL,
  `finished` datetime DEFAULT NULL,
  `status` char(1) COLLATE utf8_bin NOT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`taskId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_pm_task`
--

LOCK TABLES `tbl_pm_task` WRITE;
/*!40000 ALTER TABLE `tbl_pm_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_pm_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_role`
--

DROP TABLE IF EXISTS `tbl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_role` (
  `roleId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `parentId` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`roleId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_role`
--

LOCK TABLES `tbl_role` WRITE;
/*!40000 ALTER TABLE `tbl_role` DISABLE KEYS */;
INSERT INTO `tbl_role` VALUES (1,'Root',NULL,NULL,'A','glyphicon glyphicon-hdd',0,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(2,'Staff','Staff Role',1,'A','glyphicon glyphicon-user',1,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(3,'Account','Account Management role',1,'A','glyphicon glyphicon-usd',1,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(4,'Human Resource','Human Resource management Role',1,'A','glyphicon glyphicon-heart',3,NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(5,'Test','Test',3,'A','glyphicon glyphicon-inbox',2,NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-15 17:09:21',1),(6,'Human Resource Update','Human Resource management Role',1,'A','glyphicon glyphicon-heart',3,'2015-09-15 16:16:10',1,'2015-09-15 16:15:37',1,'2015-09-15 16:16:10',1),(7,'Test 2','Test',3,'A','glyphicon glyphicon-inbox',2,'2015-09-15 17:09:32',1,'2015-09-15 17:09:27',1,'2015-09-15 17:09:32',1),(8,'ojt','des',2,'A','glyphicon glyphicon-user',1,'2015-11-30 15:20:21',1,'2015-11-30 15:19:55',1,'2015-11-30 15:20:21',1);
/*!40000 ALTER TABLE `tbl_role` ENABLE KEYS */;
UNLOCK TABLES;

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
  `isApi` bit(1) NOT NULL DEFAULT b'0',
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`routeId`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_route`
--

LOCK TABLES `tbl_route` WRITE;
/*!40000 ALTER TABLE `tbl_route` DISABLE KEYS */;
INSERT INTO `tbl_route` VALUES (1,'role','/role[/:action[/:id]]','Application','Application\\Controller\\Role','{\r\n    \'id\':\'[0-9]+\',\r\n    \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(2,'user','/user[/:action[/:id]]','Application','Application\\Controller\\User','{\r\n    \'id\' : \'[0-9]+\',\r\n     \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-22 14:55:46',1),(3,'constant','/constant[/:action[/:id]]','Application','Application\\Controller\\Constant','{\r\n    \'id\' : \'[0-9]+\',\r\n	\'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(4,'menu','/menu[/:action[/:id]]','Application','Application\\Controller\\Menu','{\r\n    \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(5,'route','/route[/:action[/:id]]','Application','Application\\Controller\\Route','{\r\n	\'id\' : \'[0-9]+\',\r\n	\'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(6,'account_type','/account/type[/:action[/:id]]','Account','Account\\Controller\\AccountType','{\r\n    \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(7,'account_receivable','/account/receivable[/:action[/:id]]','Account','Account\\Controller\\Receivable','{\r\n    \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(8,'account_payable','/account/payable[/:action[/:id]]','Account','Account\\Controller\\Payable','{\r\n    \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(9,'hr_position','/hr/position[/:action[/:id]]','Human Resource','HumanResource\\Controller\\Position','{\r\n    \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(10,'hr_department','/hr/department[/:action[/:id]]','Human Resource','HumanResource\\Controller\\Department','{\r\n    \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(11,'hr_staff','/hr/staff[/:action[/:id]]','HumanResource','HumanResource\\Controller\\Staff','{\r\n    \'id\':\'[0-9]+\',\r\n    \'action\':\'[a-zA-Z][a-zA-Z0-9]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(12,'account_voucher','/account/voucher[/:action[/:voucher]]','Account','Account\\Controller\\Voucher','{\r\n    \'voucher\' : \'(PV|RV)[0-9]{12}\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(13,'account_currency','/account/currency[/:action[/:id]]','Account','Account\\Controller\\Currency','{\r\n	\'id\':\'[0-9]+\',\r\n     \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(14,'cr_proposal','/cr/proposal[/:action[/:id]]','Customer Relation','Customer Relation\\Controller\\Proposal','{\r\n   \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\' \r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(15,'cr_contact','/cr/contact[/:action[/:id]]','Customer Relation','Customer Relation\\Controller\\Contact','{\r\n	\'id\':\'[0-9]+\',\r\n     \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(16,'cr_company','/cr/company[/:action[/:id]]','Customer Relation','Customer Relation\\Controller\\Company','{\r\n	\'id\':\'[0-9]+\',\r\n     \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(17,'cr_contract','/cr/contract[/:action[/:id]]','Customer Relation','Customer Relation\\Controller\\Contract','{\r\n	\'id\':\'[0-9]+\',\r\n     \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(18,'hr_attendance','/hr/attendance[/:action[/:id]]','Human Resource','HumanResource\\Controller\\Attendance','{\r\n	\'id\':\'[0-9]+\',\r\n     \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(19,'account_balance','/account/balance[/:action[/:id]]','Account','Account\\Controller\\Balance','{\r\n    \'action\':\'[A-Za-z][A-Za-z0-9]*\',\r\n    \'id\':\'[0-9]+\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(20,'pm_project','/pm/project[/:action[/:id]]','Project Management','ProjectManagement\\Controller\\Project','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \'id\' : \'[0-9]+\',\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(22,'dashboard','/dashboard[/:action[/:id]]','Application','Application\\Controller\\Dashboard','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \"id\":\"[0-9]+\"\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(23,'hr_holiday','/hr/holiday[/:action[/:id]]','Human Resource','HumanResource\\Controller\\Holiday','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \'id\' : \'[0-9]+\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(24,'hr_payroll','/hr/payroll[/:action[/:id]]','Human Resource','HumanResource\\Controller\\Payroll','{\r\n    \'action\':\'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \'id\':\'[0-9]\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(25,'hr_leave','/hr/leave[/:action[/:id]]','Human Resource','HumanResource\\Controller\\Leave','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \'id\':\'[0-9]+\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(26,'account_report','/account/report[/:action][/:year][/:month]','Account','Account\\Controller\\Report','{\r\n    \"year\" : \'[0-9]{4}\',\r\n    \"month\" : \'[0-9]{2}\',\r\n    \"action\" : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(27,'development_generate','/development/generate[/:action]','Development','Development\\Controller\\Generate','{\r\n	\"action\":\"[a-zA-Z][a-zA-Z0-9_]*\"\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,'2015-09-28 13:53:53',1),(28,'pm_task','/pm/task[/:action[/:id]]','Project Management','ProjectManagement\\Controller\\Task','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \'id\' : \'[0-9]+\',\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(29,'pm_report','/pm/report[/:action[/:id]]','Project Management','ProjectManagement\\Controller\\Report','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n    \'id\' : \'[0-9]+\',\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(30,'preferences','/preferences[/:action]','Application','Application\\Controller\\Preferences','{\r\n	\'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n}','\0',NULL,NULL,'0000-00-00 00:00:00',0,NULL,NULL),(31,'aaaaaa','afasf','afas','Application\\Controller\\Index','fasdfasf','\0','2015-09-15 17:11:08',1,'2015-09-15 17:10:47',1,'2015-09-15 17:11:08',1),(32,'aaaaaa 2','afasf','afas','Application\\Controller\\Index','fasdfasf','\0','2015-09-15 17:11:05',1,'2015-09-15 17:11:01',1,'2015-09-15 17:11:05',1),(33,'cr_payment','/cr/payment[/:action[/:id]]','Customer Relation','CustomerRelation\\Controller\\Payment','{\r\n   \'id\' : \'[0-9]+\',\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\' \r\n}','\0',NULL,NULL,'2015-09-16 13:22:54',1,NULL,NULL),(34,'user_api','/api/user[/:action[/:id]]','Application','Application\\Api\\User','{\r\n    \'id\' : \'[0-9]+\',\r\n     \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\',\r\n}','','2015-10-07 10:35:51',1,'2015-09-22 14:57:22',1,'2015-10-07 10:35:51',1),(35,'auth_api','/api/auth[/:action]','Application','Application\\Api\\Auth','{\r\n    \'action\' : \'[a-zA-Z][a-zA-Z0-9_]*\'\r\n}','','2015-10-07 10:35:51',1,'2015-09-29 11:48:25',1,'2015-10-07 10:35:51',1);
/*!40000 ALTER TABLE `tbl_route` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `tbl_route_permission`
--

LOCK TABLES `tbl_route_permission` WRITE;
/*!40000 ALTER TABLE `tbl_route_permission` DISABLE KEYS */;
INSERT INTO `tbl_route_permission` VALUES (1,NULL,1),(2,NULL,1),(2,NULL,4),(3,NULL,1),(4,NULL,1),(5,NULL,1),(6,NULL,1),(6,NULL,3),(7,NULL,1),(7,NULL,2),(7,NULL,3),(8,NULL,1),(8,NULL,2),(8,NULL,3),(9,NULL,1),(9,NULL,4),(10,NULL,1),(10,NULL,4),(11,NULL,1),(11,NULL,2),(11,NULL,4),(12,NULL,1),(12,NULL,3),(13,NULL,1),(13,NULL,3),(14,NULL,1),(15,NULL,1),(16,NULL,1),(17,NULL,1),(18,NULL,1),(18,NULL,2),(18,NULL,4),(19,NULL,1),(19,NULL,3),(20,NULL,1),(22,NULL,1),(22,NULL,2),(23,NULL,1),(23,NULL,2),(23,NULL,4),(24,NULL,1),(24,NULL,4),(25,NULL,1),(25,NULL,2),(25,NULL,4),(26,NULL,1),(26,NULL,3),(27,NULL,1),(28,NULL,1),(29,NULL,1),(30,NULL,1),(33,NULL,1);
/*!40000 ALTER TABLE `tbl_route_permission` ENABLE KEYS */;
UNLOCK TABLES;

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
  `lastLogin` datetime DEFAULT NULL,
  `tokenKey` char(32) DEFAULT NULL,
  `deletedDate` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `modifiedDate` datetime DEFAULT NULL,
  `modifiedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user`
--

LOCK TABLES `tbl_user` WRITE;
/*!40000 ALTER TABLE `tbl_user` DISABLE KEYS */;
INSERT INTO `tbl_user` VALUES (1,'sundew','d22a48c17050c4e810dc8110766057fc','It is a default admin.','./data/uploads/avatar/sundew.png','A','2017-10-19 22:16:53',NULL,NULL,NULL,'0000-00-00 00:00:00',0,'2017-10-19 22:16:53',1);
/*!40000 ALTER TABLE `tbl_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_user_role`
--

DROP TABLE IF EXISTS `tbl_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user_role` (
  `userId` int(11) NOT NULL,
  `roleId` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`roleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user_role`
--

LOCK TABLES `tbl_user_role` WRITE;
/*!40000 ALTER TABLE `tbl_user_role` DISABLE KEYS */;
INSERT INTO `tbl_user_role` VALUES (1,1),(1,2),(2,2),(3,2),(4,2),(4,3),(4,4),(6,2),(22,2),(23,2),(24,2),(25,2);
/*!40000 ALTER TABLE `tbl_user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `vw_account_closing`
--

DROP TABLE IF EXISTS `vw_account_closing`;
/*!50001 DROP VIEW IF EXISTS `vw_account_closing`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_account_closing` AS SELECT 
 1 AS `closingId`,
 1 AS `currencyId`,
 1 AS `receivableId`,
 1 AS `openingDate`,
 1 AS `openingAmount`,
 1 AS `payableId`,
 1 AS `closingDate`,
 1 AS `closingAmount`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `currency`,
 1 AS `receivable_voucher`,
 1 AS `payable_voucher`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_account_payable`
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
 1 AS `paymentType`,
 1 AS `attachmentFile`,
 1 AS `currencyId`,
 1 AS `withdrawBy`,
 1 AS `approveBy`,
 1 AS `status`,
 1 AS `approvedDate`,
 1 AS `reason`,
 1 AS `requestedDate`,
 1 AS `group_code`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `type`,
 1 AS `currencyCode`,
 1 AS `currencyRate`,
 1 AS `bankCode`,
 1 AS `requester`,
 1 AS `approver`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_account_receivable`
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
 1 AS `paymentType`,
 1 AS `attachmentFile`,
 1 AS `currencyId`,
 1 AS `depositBy`,
 1 AS `approveBy`,
 1 AS `status`,
 1 AS `approvedDate`,
 1 AS `reason`,
 1 AS `requestedDate`,
 1 AS `group_code`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `type`,
 1 AS `currencyCode`,
 1 AS `currencyRate`,
 1 AS `bankCode`,
 1 AS `requester`,
 1 AS `approver`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_account_voucher`
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
 1 AS `rate`,
 1 AS `paymentType`,
 1 AS `bankCode`,
 1 AS `attachmentFile`,
 1 AS `requestBy`,
 1 AS `requester`,
 1 AS `approveBy`,
 1 AS `approver`,
 1 AS `status`,
 1 AS `approvedDate`,
 1 AS `reason`,
 1 AS `requestedDate`,
 1 AS `group_code`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_cr_contact`
--

DROP TABLE IF EXISTS `vw_cr_contact`;
/*!50001 DROP VIEW IF EXISTS `vw_cr_contact`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_cr_contact` AS SELECT 
 1 AS `contactId`,
 1 AS `name`,
 1 AS `phone`,
 1 AS `email`,
 1 AS `address`,
 1 AS `website`,
 1 AS `companyId`,
 1 AS `notes`,
 1 AS `tag`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `companyname`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_cr_contract`
--

DROP TABLE IF EXISTS `vw_cr_contract`;
/*!50001 DROP VIEW IF EXISTS `vw_cr_contract`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_cr_contract` AS SELECT 
 1 AS `contractId`,
 1 AS `companyId`,
 1 AS `contactId`,
 1 AS `projectId`,
 1 AS `code`,
 1 AS `name`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `contractFile`,
 1 AS `contractBy`,
 1 AS `contractDate`,
 1 AS `notes`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `companyName`,
 1 AS `contactName`,
 1 AS `ProjectName`,
 1 AS `currencyCode`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_cr_payment`
--

DROP TABLE IF EXISTS `vw_cr_payment`;
/*!50001 DROP VIEW IF EXISTS `vw_cr_payment`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_cr_payment` AS SELECT 
 1 AS `paymentId`,
 1 AS `contractId`,
 1 AS `type`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `paymentDate`,
 1 AS `status`,
 1 AS `staffId`,
 1 AS `contactId`,
 1 AS `remark`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `contractName`,
 1 AS `currencyCode`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_cr_proposal`
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
 1 AS `name`,
 1 AS `amount`,
 1 AS `currencyId`,
 1 AS `proposalDate`,
 1 AS `proposalFile`,
 1 AS `notes`,
 1 AS `proposalBy`,
 1 AS `group_code`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `currencyCode`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_hr_attendance`
--

DROP TABLE IF EXISTS `vw_hr_attendance`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_attendance`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_attendance` AS SELECT 
 1 AS `attendanceId`,
 1 AS `staffId`,
 1 AS `attendanceDate`,
 1 AS `inTime`,
 1 AS `outTime`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `staffCode`,
 1 AS `staffName`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_hr_leave`
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
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `staffCode`,
 1 AS `staffName`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_hr_payroll`
--

DROP TABLE IF EXISTS `vw_hr_payroll`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_payroll`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_payroll` AS SELECT 
 1 AS `payrollId`,
 1 AS `staffId`,
 1 AS `fromDate`,
 1 AS `toDate`,
 1 AS `m_wd`,
 1 AS `s_wd`,
 1 AS `salary`,
 1 AS `currencyId`,
 1 AS `bankCode`,
 1 AS `leave`,
 1 AS `absent`,
 1 AS `formula`,
 1 AS `Late`,
 1 AS `netSalary`,
 1 AS `managerId`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `staffCode`,
 1 AS `staffName`,
 1 AS `Currency`,
 1 AS `Position`,
 1 AS `Department`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_hr_position`
--

DROP TABLE IF EXISTS `vw_hr_position`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_position`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_position` AS SELECT 
 1 AS `positionId`,
 1 AS `name`,
 1 AS `currencyId`,
 1 AS `min_salary`,
 1 AS `max_salary`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `currency`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_hr_staff`
--

DROP TABLE IF EXISTS `vw_hr_staff`;
/*!50001 DROP VIEW IF EXISTS `vw_hr_staff`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_hr_staff` AS SELECT 
 1 AS `staffId`,
 1 AS `userId`,
 1 AS `staffCode`,
 1 AS `staffName`,
 1 AS `birthday`,
 1 AS `positionId`,
 1 AS `departmentId`,
 1 AS `workHours`,
 1 AS `salary`,
 1 AS `currencyId`,
 1 AS `annual_leave`,
 1 AS `permanentDate`,
 1 AS `bankCode`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `Currency`,
 1 AS `UserName`,
 1 AS `Position`,
 1 AS `Department`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_payment`
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
-- Temporary table structure for view `vw_pm_project`
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
 1 AS `repository`,
 1 AS `managerId`,
 1 AS `startDate`,
 1 AS `endDate`,
 1 AS `group_code`,
 1 AS `status`,
 1 AS `remark`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `userName`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_pm_task`
--

DROP TABLE IF EXISTS `vw_pm_task`;
/*!50001 DROP VIEW IF EXISTS `vw_pm_task`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_pm_task` AS SELECT 
 1 AS `taskId`,
 1 AS `name`,
 1 AS `current`,
 1 AS `staffId`,
 1 AS `fromTime`,
 1 AS `toTime`,
 1 AS `projectId`,
 1 AS `predecessorId`,
 1 AS `level`,
 1 AS `maxBudget`,
 1 AS `currencyId`,
 1 AS `priority`,
 1 AS `description`,
 1 AS `tag`,
 1 AS `managerId`,
 1 AS `finished`,
 1 AS `status`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `staffCode`,
 1 AS `staffName`,
 1 AS `managerCode`,
 1 AS `managerName`,
 1 AS `projectCode`,
 1 AS `projectName`,
 1 AS `currencyCode`,
 1 AS `currency`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_user`
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
 1 AS `tokenKey`,
 1 AS `deletedDate`,
 1 AS `deletedBy`,
 1 AS `createdDate`,
 1 AS `createdBy`,
 1 AS `modifiedDate`,
 1 AS `modifiedBy`,
 1 AS `rolename`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_account_closing`
--

/*!50001 DROP VIEW IF EXISTS `vw_account_closing`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_closing` AS select `close`.`closingId` AS `closingId`,`close`.`currencyId` AS `currencyId`,`close`.`receivableId` AS `receivableId`,`close`.`openingDate` AS `openingDate`,`close`.`openingAmount` AS `openingAmount`,`close`.`payableId` AS `payableId`,`close`.`closingDate` AS `closingDate`,`close`.`closingAmount` AS `closingAmount`,`close`.`deletedDate` AS `deletedDate`,`close`.`deletedBy` AS `deletedBy`,`close`.`createdDate` AS `createdDate`,`close`.`createdBy` AS `createdBy`,`close`.`modifiedDate` AS `modifiedDate`,`close`.`modifiedBy` AS `modifiedBy`,`cur`.`code` AS `currency`,`open`.`voucherNo` AS `receivable_voucher`,`pay`.`voucherNo` AS `payable_voucher` from (((`tbl_account_closing` `close` join `tbl_account_currency` `cur` on((`close`.`currencyId` = `cur`.`currencyId`))) left join `tbl_account_receivable` `open` on((`close`.`receivableId` = `open`.`receiveVoucherId`))) left join `tbl_account_payable` `pay` on((`close`.`payableId` = `pay`.`payVoucherId`))) */;
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
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_payable` AS (select `pv`.`payVoucherId` AS `payVoucherId`,`pv`.`voucherNo` AS `voucherNo`,`pv`.`voucherDate` AS `voucherDate`,`pv`.`accountType` AS `accountType`,`pv`.`description` AS `description`,`pv`.`amount` AS `amount`,`pv`.`paymentType` AS `paymentType`,`pv`.`attachmentFile` AS `attachmentFile`,`pv`.`currencyId` AS `currencyId`,`pv`.`withdrawBy` AS `withdrawBy`,`pv`.`approveBy` AS `approveBy`,`pv`.`status` AS `status`,`pv`.`approvedDate` AS `approvedDate`,`pv`.`reason` AS `reason`,`pv`.`requestedDate` AS `requestedDate`,`pv`.`group_code` AS `group_code`,`pv`.`deletedDate` AS `deletedDate`,`pv`.`deletedBy` AS `deletedBy`,`pv`.`createdDate` AS `createdDate`,`pv`.`createdBy` AS `createdBy`,`pv`.`modifiedDate` AS `modifiedDate`,`pv`.`modifiedBy` AS `modifiedBy`,`ty`.`name` AS `type`,`cur`.`code` AS `currencyCode`,`cur`.`rate` AS `currencyRate`,`req`.`bankCode` AS `bankCode`,concat(`req`.`staffName`,'(',`req`.`staffCode`,')') AS `requester`,concat(`app`.`staffName`,'(',`app`.`staffCode`,')') AS `approver` from ((((`tbl_account_payable` `pv` left join `tbl_account_type` `ty` on((`pv`.`accountType` = `ty`.`accountTypeId`))) left join `tbl_account_currency` `cur` on((`pv`.`currencyId` = `cur`.`currencyId`))) left join `tbl_hr_staff` `req` on((`pv`.`withdrawBy` = `req`.`staffId`))) left join `tbl_hr_staff` `app` on((`pv`.`approveBy` = `app`.`staffId`)))) */;
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
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_receivable` AS (select `rv`.`receiveVoucherId` AS `receiveVoucherId`,`rv`.`voucherNo` AS `voucherNo`,`rv`.`voucherDate` AS `voucherDate`,`rv`.`accountType` AS `accountType`,`rv`.`description` AS `description`,`rv`.`amount` AS `amount`,`rv`.`paymentType` AS `paymentType`,`rv`.`attachmentFile` AS `attachmentFile`,`rv`.`currencyId` AS `currencyId`,`rv`.`depositBy` AS `depositBy`,`rv`.`approveBy` AS `approveBy`,`rv`.`status` AS `status`,`rv`.`approvedDate` AS `approvedDate`,`rv`.`reason` AS `reason`,`rv`.`requestedDate` AS `requestedDate`,`rv`.`group_code` AS `group_code`,`rv`.`deletedDate` AS `deletedDate`,`rv`.`deletedBy` AS `deletedBy`,`rv`.`createdDate` AS `createdDate`,`rv`.`createdBy` AS `createdBy`,`rv`.`modifiedDate` AS `modifiedDate`,`rv`.`modifiedBy` AS `modifiedBy`,`ty`.`name` AS `type`,`cur`.`code` AS `currencyCode`,`cur`.`rate` AS `currencyRate`,`req`.`bankCode` AS `bankCode`,concat(`req`.`staffName`,'(',`req`.`staffCode`,')') AS `requester`,concat(`app`.`staffName`,'(',`app`.`staffCode`,')') AS `approver` from ((((`tbl_account_receivable` `rv` left join `tbl_account_type` `ty` on((`rv`.`accountType` = `ty`.`accountTypeId`))) left join `tbl_account_currency` `cur` on((`rv`.`currencyId` = `cur`.`currencyId`))) left join `tbl_hr_staff` `req` on((`rv`.`depositBy` = `req`.`staffId`))) left join `tbl_hr_staff` `app` on((`rv`.`approveBy` = `app`.`staffId`)))) */;
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
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_account_voucher` AS select 'Receivable' AS `type`,`vw_account_receivable`.`receiveVoucherId` AS `voucherId`,`vw_account_receivable`.`voucherNo` AS `voucherNo`,`vw_account_receivable`.`voucherDate` AS `voucherDate`,`vw_account_receivable`.`accountType` AS `accountTypeId`,`vw_account_receivable`.`type` AS `accountType`,`vw_account_receivable`.`description` AS `description`,`vw_account_receivable`.`amount` AS `amount`,`vw_account_receivable`.`currencyId` AS `currencyId`,`vw_account_receivable`.`currencyCode` AS `currency`,`vw_account_receivable`.`currencyRate` AS `rate`,`vw_account_receivable`.`paymentType` AS `paymentType`,`vw_account_receivable`.`bankCode` AS `bankCode`,`vw_account_receivable`.`attachmentFile` AS `attachmentFile`,`vw_account_receivable`.`depositBy` AS `requestBy`,`vw_account_receivable`.`requester` AS `requester`,`vw_account_receivable`.`approveBy` AS `approveBy`,`vw_account_receivable`.`approver` AS `approver`,`vw_account_receivable`.`status` AS `status`,`vw_account_receivable`.`approvedDate` AS `approvedDate`,`vw_account_receivable`.`reason` AS `reason`,`vw_account_receivable`.`requestedDate` AS `requestedDate`,`vw_account_receivable`.`group_code` AS `group_code`,`vw_account_receivable`.`deletedDate` AS `deletedDate`,`vw_account_receivable`.`deletedBy` AS `deletedBy`,`vw_account_receivable`.`createdDate` AS `createdDate`,`vw_account_receivable`.`createdBy` AS `createdBy`,`vw_account_receivable`.`modifiedDate` AS `modifiedDate`,`vw_account_receivable`.`modifiedBy` AS `modifiedBy` from `vw_account_receivable` union all select 'Payable' AS `type`,`vw_account_payable`.`payVoucherId` AS `voucherId`,`vw_account_payable`.`voucherNo` AS `voucherNo`,`vw_account_payable`.`voucherDate` AS `voucherDate`,`vw_account_payable`.`accountType` AS `accountTypeId`,`vw_account_payable`.`type` AS `accountType`,`vw_account_payable`.`description` AS `description`,`vw_account_payable`.`amount` AS `amount`,`vw_account_payable`.`currencyId` AS `currencyId`,`vw_account_payable`.`currencyCode` AS `currency`,`vw_account_payable`.`currencyRate` AS `rate`,`vw_account_payable`.`paymentType` AS `paymentType`,`vw_account_payable`.`bankCode` AS `bankCode`,`vw_account_payable`.`attachmentFile` AS `attachmentFile`,`vw_account_payable`.`withdrawBy` AS `requestBy`,`vw_account_payable`.`requester` AS `requester`,`vw_account_payable`.`approveBy` AS `approveBy`,`vw_account_payable`.`approver` AS `approver`,`vw_account_payable`.`status` AS `status`,`vw_account_payable`.`approvedDate` AS `approvedDate`,`vw_account_payable`.`reason` AS `reason`,`vw_account_payable`.`requestedDate` AS `requestedDate`,`vw_account_payable`.`group_code` AS `group_code`,`vw_account_payable`.`deletedDate` AS `deletedDate`,`vw_account_payable`.`deletedBy` AS `deletedBy`,`vw_account_payable`.`createdDate` AS `createdDate`,`vw_account_payable`.`createdBy` AS `createdBy`,`vw_account_payable`.`modifiedDate` AS `modifiedDate`,`vw_account_payable`.`modifiedBy` AS `modifiedBy` from `vw_account_payable` */;
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
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_contact` AS select `c`.`contactId` AS `contactId`,`c`.`name` AS `name`,`c`.`phone` AS `phone`,`c`.`email` AS `email`,`c`.`address` AS `address`,`c`.`website` AS `website`,`c`.`companyId` AS `companyId`,`c`.`notes` AS `notes`,`c`.`tag` AS `tag`,`c`.`status` AS `status`,`c`.`deletedDate` AS `deletedDate`,`c`.`deletedBy` AS `deletedBy`,`c`.`createdDate` AS `createdDate`,`c`.`createdBy` AS `createdBy`,`c`.`modifiedDate` AS `modifiedDate`,`c`.`modifiedBy` AS `modifiedBy`,`com`.`name` AS `companyname` from (`tbl_cr_contact` `c` left join `tbl_cr_company` `com` on((`c`.`companyId` = `com`.`companyId`))) */;
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
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_contract` AS (select `c`.`contractId` AS `contractId`,`c`.`companyId` AS `companyId`,`c`.`contactId` AS `contactId`,`c`.`projectId` AS `projectId`,`c`.`code` AS `code`,`c`.`name` AS `name`,`c`.`amount` AS `amount`,`c`.`currencyId` AS `currencyId`,`c`.`contractFile` AS `contractFile`,`c`.`contractBy` AS `contractBy`,`c`.`contractDate` AS `contractDate`,`c`.`notes` AS `notes`,`c`.`status` AS `status`,`c`.`deletedDate` AS `deletedDate`,`c`.`deletedBy` AS `deletedBy`,`c`.`createdDate` AS `createdDate`,`c`.`createdBy` AS `createdBy`,`c`.`modifiedDate` AS `modifiedDate`,`c`.`modifiedBy` AS `modifiedBy`,`com`.`name` AS `companyName`,`co`.`name` AS `contactName`,`p`.`name` AS `ProjectName`,`cr`.`code` AS `currencyCode` from ((((`tbl_cr_contract` `c` join `tbl_account_currency` `cr` on((`c`.`currencyId` = `cr`.`currencyId`))) join `tbl_cr_company` `com` on((`c`.`companyId` = `com`.`companyId`))) join `tbl_cr_contact` `co` on((`c`.`contactId` = `co`.`contactId`))) join `tbl_pm_project` `p` on((`c`.`projectId` = `p`.`projectId`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_cr_payment`
--

/*!50001 DROP VIEW IF EXISTS `vw_cr_payment`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_payment` AS (select `p`.`paymentId` AS `paymentId`,`p`.`contractId` AS `contractId`,`p`.`type` AS `type`,`p`.`amount` AS `amount`,`p`.`currencyId` AS `currencyId`,`p`.`paymentDate` AS `paymentDate`,`p`.`status` AS `status`,`p`.`staffId` AS `staffId`,`p`.`contactId` AS `contactId`,`p`.`remark` AS `remark`,`p`.`deletedDate` AS `deletedDate`,`p`.`deletedBy` AS `deletedBy`,`p`.`createdDate` AS `createdDate`,`p`.`createdBy` AS `createdBy`,`p`.`modifiedDate` AS `modifiedDate`,`p`.`modifiedBy` AS `modifiedBy`,`cr`.`code` AS `contractName`,`ac`.`code` AS `currencyCode` from (((`tbl_cr_payment` `p` join `tbl_cr_contact` `c` on((`c`.`contactId` = `p`.`contactId`))) join `tbl_cr_contract` `cr` on((`cr`.`contractId` = `p`.`contractId`))) join `tbl_account_currency` `ac` on((`ac`.`currencyId` = `p`.`currencyId`)))) */;
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
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_cr_proposal` AS select `tbl_cr_proposal`.`proposalId` AS `proposalId`,`tbl_cr_proposal`.`companyId` AS `companyId`,`tbl_cr_proposal`.`contactId` AS `contactId`,`tbl_cr_proposal`.`code` AS `code`,`tbl_cr_proposal`.`name` AS `name`,`tbl_cr_proposal`.`amount` AS `amount`,`tbl_cr_proposal`.`currencyId` AS `currencyId`,`tbl_cr_proposal`.`proposalDate` AS `proposalDate`,`tbl_cr_proposal`.`proposalFile` AS `proposalFile`,`tbl_cr_proposal`.`notes` AS `notes`,`tbl_cr_proposal`.`proposalBy` AS `proposalBy`,`tbl_cr_proposal`.`group_code` AS `group_code`,`tbl_cr_proposal`.`status` AS `status`,`tbl_cr_proposal`.`deletedDate` AS `deletedDate`,`tbl_cr_proposal`.`deletedBy` AS `deletedBy`,`tbl_cr_proposal`.`createdDate` AS `createdDate`,`tbl_cr_proposal`.`createdBy` AS `createdBy`,`tbl_cr_proposal`.`modifiedDate` AS `modifiedDate`,`tbl_cr_proposal`.`modifiedBy` AS `modifiedBy`,`tbl_account_currency`.`code` AS `currencyCode` from (`tbl_cr_proposal` join `tbl_account_currency` on((`tbl_cr_proposal`.`currencyId` = `tbl_account_currency`.`currencyId`))) */;
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
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_attendance` AS select `a`.`attendanceId` AS `attendanceId`,`a`.`staffId` AS `staffId`,`a`.`attendanceDate` AS `attendanceDate`,`a`.`inTime` AS `inTime`,`a`.`outTime` AS `outTime`,`a`.`deletedDate` AS `deletedDate`,`a`.`deletedBy` AS `deletedBy`,`a`.`createdDate` AS `createdDate`,`a`.`createdBy` AS `createdBy`,`a`.`modifiedDate` AS `modifiedDate`,`a`.`modifiedBy` AS `modifiedBy`,`s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName` from (`tbl_hr_attendance` `a` join `tbl_hr_staff` `s` on((`a`.`staffId` = `s`.`staffId`))) */;
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
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_leave` AS select `l`.`leaveId` AS `leaveId`,(case `l`.`status` when 'R' then 'Requested' when 'A' then 'Approved' when 'C' then 'Rejected' else 'Invalid' end) AS `status`,`l`.`staffId` AS `staffId`,(case `l`.`leaveType` when 'H' then 'Half-day' when 'F' then 'Full-day' when 'M' then 'Medical' when 'A' then 'Absent' else 'Invalid' end) AS `leaveType`,`l`.`date` AS `date`,`l`.`description` AS `description`,`l`.`deletedDate` AS `deletedDate`,`l`.`deletedBy` AS `deletedBy`,`l`.`createdDate` AS `createdDate`,`l`.`createdBy` AS `createdBy`,`l`.`modifiedDate` AS `modifiedDate`,`l`.`modifiedBy` AS `modifiedBy`,`s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName` from (`tbl_hr_leave` `l` join `tbl_hr_staff` `s` on((`l`.`staffId` = `s`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_hr_payroll`
--

/*!50001 DROP VIEW IF EXISTS `vw_hr_payroll`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_payroll` AS select `p`.`payrollId` AS `payrollId`,`p`.`staffId` AS `staffId`,`p`.`fromDate` AS `fromDate`,`p`.`toDate` AS `toDate`,`p`.`m_wd` AS `m_wd`,`p`.`s_wd` AS `s_wd`,`p`.`salary` AS `salary`,`p`.`currencyId` AS `currencyId`,`p`.`bankCode` AS `bankCode`,`p`.`leave` AS `leave`,`p`.`absent` AS `absent`,`p`.`formula` AS `formula`,`p`.`Late` AS `Late`,`p`.`netSalary` AS `netSalary`,`p`.`managerId` AS `managerId`,`p`.`status` AS `status`,`p`.`deletedDate` AS `deletedDate`,`p`.`deletedBy` AS `deletedBy`,`p`.`createdDate` AS `createdDate`,`p`.`createdBy` AS `createdBy`,`p`.`modifiedDate` AS `modifiedDate`,`p`.`modifiedBy` AS `modifiedBy`,`s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName`,`s`.`Currency` AS `Currency`,`s`.`Position` AS `Position`,`s`.`Department` AS `Department` from (`tbl_hr_payroll` `p` join `vw_hr_staff` `s` on((`p`.`staffId` = `s`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_hr_position`
--

/*!50001 DROP VIEW IF EXISTS `vw_hr_position`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_position` AS select `pos`.`positionId` AS `positionId`,`pos`.`name` AS `name`,`pos`.`currencyId` AS `currencyId`,`pos`.`min_salary` AS `min_salary`,`pos`.`max_salary` AS `max_salary`,`pos`.`status` AS `status`,`pos`.`deletedDate` AS `deletedDate`,`pos`.`deletedBy` AS `deletedBy`,`pos`.`createdDate` AS `createdDate`,`pos`.`createdBy` AS `createdBy`,`pos`.`modifiedDate` AS `modifiedDate`,`pos`.`modifiedBy` AS `modifiedBy`,`cur`.`code` AS `currency` from (`tbl_hr_position` `pos` join `tbl_account_currency` `cur` on((`pos`.`currencyId` = `cur`.`currencyId`))) */;
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
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_hr_staff` AS select `s`.`staffId` AS `staffId`,`s`.`userId` AS `userId`,`s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName`,`s`.`birthday` AS `birthday`,`s`.`positionId` AS `positionId`,`s`.`departmentId` AS `departmentId`,`s`.`workHours` AS `workHours`,`s`.`salary` AS `salary`,`s`.`currencyId` AS `currencyId`,`s`.`annual_leave` AS `annual_leave`,`s`.`permanentDate` AS `permanentDate`,`s`.`bankCode` AS `bankCode`,`s`.`status` AS `status`,`s`.`deletedDate` AS `deletedDate`,`s`.`deletedBy` AS `deletedBy`,`s`.`createdDate` AS `createdDate`,`s`.`createdBy` AS `createdBy`,`s`.`modifiedDate` AS `modifiedDate`,`s`.`modifiedBy` AS `modifiedBy`,`c`.`code` AS `Currency`,`u`.`userName` AS `UserName`,`p`.`name` AS `Position`,`d`.`name` AS `Department` from ((((`tbl_hr_staff` `s` left join `tbl_user` `u` on((`s`.`userId` = `u`.`userId`))) left join `tbl_hr_position` `p` on((`p`.`positionId` = `s`.`positionId`))) left join `tbl_hr_department` `d` on((`s`.`departmentId` = `d`.`departmentId`))) left join `tbl_account_currency` `c` on((`s`.`currencyId` = `c`.`currencyId`))) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
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
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_pm_project` AS select `pm`.`projectId` AS `projectId`,`pm`.`code` AS `code`,`pm`.`name` AS `name`,`pm`.`description` AS `description`,`pm`.`repository` AS `repository`,`pm`.`managerId` AS `managerId`,`pm`.`startDate` AS `startDate`,`pm`.`endDate` AS `endDate`,`pm`.`group_code` AS `group_code`,`pm`.`status` AS `status`,`pm`.`remark` AS `remark`,`pm`.`deletedDate` AS `deletedDate`,`pm`.`deletedBy` AS `deletedBy`,`pm`.`createdDate` AS `createdDate`,`pm`.`createdBy` AS `createdBy`,`pm`.`modifiedDate` AS `modifiedDate`,`pm`.`modifiedBy` AS `modifiedBy`,concat(`staff`.`staffName`,' (',`staff`.`staffCode`,')') AS `userName` from (`tbl_pm_project` `pm` left join `tbl_hr_staff` `staff` on((`pm`.`managerId` = `staff`.`staffId`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_pm_task`
--

/*!50001 DROP VIEW IF EXISTS `vw_pm_task`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_pm_task` AS select `t`.`taskId` AS `taskId`,`t`.`name` AS `name`,`t`.`current` AS `current`,`t`.`staffId` AS `staffId`,`t`.`fromTime` AS `fromTime`,`t`.`toTime` AS `toTime`,`t`.`projectId` AS `projectId`,`t`.`predecessorId` AS `predecessorId`,(`t`.`level` + 1) AS `level`,`t`.`maxBudget` AS `maxBudget`,`t`.`currencyId` AS `currencyId`,`t`.`priority` AS `priority`,`t`.`description` AS `description`,`t`.`tag` AS `tag`,`t`.`managerId` AS `managerId`,`t`.`finished` AS `finished`,`t`.`status` AS `status`,`t`.`deletedDate` AS `deletedDate`,`t`.`deletedBy` AS `deletedBy`,`t`.`createdDate` AS `createdDate`,`t`.`createdBy` AS `createdBy`,`t`.`modifiedDate` AS `modifiedDate`,`t`.`modifiedBy` AS `modifiedBy`,`s`.`staffCode` AS `staffCode`,`s`.`staffName` AS `staffName`,`m`.`staffCode` AS `managerCode`,`m`.`staffName` AS `managerName`,`p`.`code` AS `projectCode`,`p`.`name` AS `projectName`,`c`.`code` AS `currencyCode`,`c`.`name` AS `currency` from ((((`tbl_pm_task` `t` join `tbl_hr_staff` `s` on((`t`.`staffId` = `s`.`staffId`))) join `tbl_hr_staff` `m` on((`t`.`managerId` = `m`.`staffId`))) left join `tbl_pm_project` `p` on((`t`.`projectId` = `p`.`projectId`))) left join `tbl_account_currency` `c` on((`t`.`currencyId` = `c`.`currencyId`))) */;
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
/*!50013 DEFINER=`sundew_dgk`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_user` AS select `u`.`userId` AS `userId`,`u`.`userName` AS `userName`,`u`.`password` AS `password`,`u`.`description` AS `description`,`u`.`image` AS `image`,`u`.`status` AS `status`,`u`.`lastLogin` AS `lastLogin`,`u`.`tokenKey` AS `tokenKey`,`u`.`deletedDate` AS `deletedDate`,`u`.`deletedBy` AS `deletedBy`,`u`.`createdDate` AS `createdDate`,`u`.`createdBy` AS `createdBy`,`u`.`modifiedDate` AS `modifiedDate`,`u`.`modifiedBy` AS `modifiedBy`,group_concat(`r`.`name` separator ',') AS `rolename` from ((`tbl_user` `u` left join `tbl_user_role` `ur` on((`u`.`userId` = `ur`.`userId`))) left join `tbl_role` `r` on((`ur`.`roleId` = `r`.`roleId`))) group by `u`.`userId`,`u`.`userName`,`u`.`password`,`u`.`description`,`u`.`image`,`u`.`status`,`u`.`lastLogin` */;
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

-- Dump completed on 2017-10-19 16:01:13
