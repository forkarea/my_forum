-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: my_php
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.12.04.2

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
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varbinary(10000) DEFAULT NULL,
  `thread_id` int(11) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `created_by_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `posts_thread_id_index` (`thread_id`),
  KEY `post_user_fk_c` (`created_by_user`),
  CONSTRAINT `posts_threads_fk` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`),
  CONSTRAINT `post_user_fk_c` FOREIGN KEY (`created_by_user`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (177,'Test post 1',74,'2014-02-24 20:45:48',62),(178,'Test post 2',74,'2014-02-24 20:45:54',62),(179,'Test post 3',75,'2014-02-24 20:46:23',62),(180,'😁  🐮  🐭  🐵 ',76,'2014-02-24 20:47:35',62),(181,'الاثنين 24/2/2014 م (آخر تحديث) الساعة 21:26 (مكة المكرمة\r\n',77,'2014-02-24 20:52:09',63);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varbinary(1000) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `created_by_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `threads_users_fk_c` (`created_by_user`),
  CONSTRAINT `threads_users_fk_c` FOREIGN KEY (`created_by_user`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threads`
--

LOCK TABLES `threads` WRITE;
/*!40000 ALTER TABLE `threads` DISABLE KEYS */;
INSERT INTO `threads` VALUES (74,'Test thread 1','2014-02-24 20:45:48',62),(75,'Test thread 2','2014-02-24 20:46:23',62),(76,'Test thread 4: 😁  🐮  🐭  🐵 ','2014-02-24 20:47:35',62),(77,'Test thread 5','2014-02-24 20:52:09',63);
/*!40000 ALTER TABLE `threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varbinary(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `signup_time` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (62,'Mat2','$2y$10$W4RA3CQt2KeWkaXJIijvWO0t0JxOn1XcltcbC259aPWkq.6vrinAC','2014-02-24 20:45:22'),(63,'المكرمة','$2y$10$3zEQ9R9XHfM2Lg221UMYa.0XN8fGPzfmO/j7ioBNdC00p3iJV/vvu','2014-02-24 20:48:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-02-24 20:55:19
