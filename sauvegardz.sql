-- MySQL dump 10.13  Distrib 5.5.57, for debian-linux-gnu (armv7l)
--
-- Host: localhost    Database: pihome
-- ------------------------------------------------------
-- Server version	5.5.57-0+deb8u1

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
-- Table structure for table `interface`
--

DROP TABLE IF EXISTS `interface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interface` (
  `id` text COLLATE utf8_bin,
  `descr` text COLLATE utf8_bin,
  `type` text COLLATE utf8_bin,
  `value` text COLLATE utf8_bin,
  `depuis` int(11) DEFAULT NULL,
  `isDisplay` int(11) DEFAULT NULL,
  `descVal1` text COLLATE utf8_bin,
  `value1` text COLLATE utf8_bin,
  `descVal2` text COLLATE utf8_bin,
  `value2` text COLLATE utf8_bin,
  `commandOn` text COLLATE utf8_bin,
  `commandOff` text COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interface`
--

LOCK TABLES `interface` WRITE;
/*!40000 ALTER TABLE `interface` DISABLE KEYS */;
INSERT INTO `interface` VALUES ('toutOn','ON','action','push',0,1,'','','','','ordinateur=on,salon=on,chambre=on,led=on',''),('ToutOff','OFF','action','push',0,1,'','','','','ordinateur=off,salon=off,chambre=off,led=off',''),('ordinateur','Ordinateur','action','off',1503737765,1,'','','','','/var/www/html/radioEmission 22 12325261 3 on','/var/www/html/radioEmission 22 12325261 3 off'),('salon','Salon','action','off',1503737647,1,'','','','','/var/www/html/433Utils/RPi_utils/codesend 1381717','/var/www/html/433Utils/RPi_utils/codesend 1381716'),('chambre','Chambre','action','off',1503737644,1,'','','','','/var/www/html/433Utils/RPi_utils/codesend 1394005','/var/www/html/433Utils/RPi_utils/codesend 1394004'),('porte','Porte','action','push',0,1,'','','','','/var/www/html/433Utils/RPi_utils/codesend 1230 ',' /var/www/html/433Utils/RPi_utils/codesend 1230 '),('led','LED','action','off',1503737649,1,'','','','','/var/www/html/radioEmission 22 12325261 2 on',' /var/www/html/radioEmission 22 12325261 2 off'),('cinema','Mode Cinema','action','push',1500827520,1,'','','','','ordniateur=on,salon=off,chambre=off,led=off',''),('nuit','Mode nuit','action','push',1500583318,1,'','','','','chambre=on,ordinateur=off,led=off,salon=off',''),('wifikey','IP masters','reglage','Belette-192.168.1.11;Pawitra-192.168.1.12',1502529513,1,'','','','','',''),('wifi','Wifi','info','0',1503742530,1,'','','','','',''),('move','Mouvement','info','1',1503742540,1,'','','','','',''),('tempIn','Temp In','info','26.34',0,1,'','','','','',''),('humidity','Humidite','info','63.83',0,1,'','','','','',''),('tempOut','Temp Out','info','29',0,1,'','','','','',''),('tempPi','Temp CPU','info','34',0,1,'','','','','',''),('abs','Absence lvl','reglage','0',1503737754,1,'Temps abs1','600','Temps abs2','0','',''),('alarmeAuto','Alarme Automatique','reglage','1',0,1,'','','','','',''),('alarme','Alarme','info','0',1502553881,1,'Notification alarme','1','Nbr alarme','0','',''),('autoChauffage','Chauffage automatique','reglage','0',0,1,'Température réglage','21','','','',''),('chauffage','Chauffage','info','0',1502573854,1,'','','','','sudo /var/www/html/codeSend 1588201',''),('memory','Memoire','info','0,54',0,1,'','','','','',''),('ram','RAM','info','22.2%',0,1,'','','','','',''),('reveil','Reveil','reglage','1',0,1,'Heure','7','Minutes','11','',''),('radioReveil','Radio reveil','reglage','0',1496483221,1,'','','','','',''),('radio','Radio reveil','reglage','1',1503465068,0,'Time out radio','2900','','','',''),('ordinateurAutoOn','Allumage auto ordinateur','reglage','0',0,1,'','','','','',''),('ordinateurAutoOff','Extinction auto ordinateur','reglage','1',0,1,'','','','','',''),('chambreAutoOn','Extinction auto Chambre','reglage','1',0,1,'','','','','',''),('mute','Mode silence','reglage','0',0,1,'','','','','',''),('exec','Executer','reglage','sudo rm /var/www/html/capture/*.jpg',0,1,'','','','','',''),('clear','Clear cache','reglage','',0,1,'','','','','',''),('cron','CronTab','reglage','',1503742616,1,'','','','','',''),('parle','Parle','reglage','',0,1,'','','','','',''),('speakIt','Speak','reglage','',0,1,'','','','','',''),('flash','Flash info','reglage','0',1500713556,1,'Date info','Sun 23 Apr 2017 18:50:59 +0200','','','',''),('couverture','Couverture chauffante','action','0',0,0,'timer','3600',NULL,NULL,'-','-');
/*!40000 ALTER TABLE `interface` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-08-26 12:17:02
