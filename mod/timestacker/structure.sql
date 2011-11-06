--
-- Table structure for table `ch_stack`
--

DROP TABLE IF EXISTS `ch_stack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ch_stack` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` int(6) NOT NULL,
  `date` date DEFAULT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ch_stack`
--

LOCK TABLES `ch_stack` WRITE;
/*!40000 ALTER TABLE `ch_stack` DISABLE KEYS */;
/*!40000 ALTER TABLE `ch_stack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ch_stack_assignation`
--

DROP TABLE IF EXISTS `ch_stack_assignation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ch_stack_assignation` (
  `said` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `owner` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`said`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ch_stack_assignation`
--

LOCK TABLES `ch_stack_assignation` WRITE;
/*!40000 ALTER TABLE `ch_stack_assignation` DISABLE KEYS */;
/*!40000 ALTER TABLE `ch_stack_assignation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ch_task`
--

DROP TABLE IF EXISTS `ch_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ch_task` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  `color` int(6) NOT NULL,
  `priority` int(6) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ch_task`
--

LOCK TABLES `ch_task` WRITE;
/*!40000 ALTER TABLE `ch_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `ch_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ch_task_stack`
--

DROP TABLE IF EXISTS `ch_task_stack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ch_task_stack` (
  `tsid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  PRIMARY KEY (`tsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ch_task_stack`
--

LOCK TABLES `ch_task_stack` WRITE;
/*!40000 ALTER TABLE `ch_task_stack` DISABLE KEYS */;
/*!40000 ALTER TABLE `ch_task_stack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ch_task_wip`
--

DROP TABLE IF EXISTS `ch_task_wip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ch_task_wip` (
  `twid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stop` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`twid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ch_task_wip`
--

LOCK TABLES `ch_task_wip` WRITE;
/*!40000 ALTER TABLE `ch_task_wip` DISABLE KEYS */;
/*!40000 ALTER TABLE `ch_task_wip` ENABLE KEYS */;
UNLOCK TABLES;
