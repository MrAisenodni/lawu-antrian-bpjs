# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.30)
# Database: antrean
# Generation Time: 2021-12-28 14:04:33 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table antreans
# ------------------------------------------------------------

DROP TABLE IF EXISTS `antreans`;

CREATE TABLE `antreans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(255) NOT NULL,
  `kode_poli` varchar(255) NOT NULL,
  `kode_dokter` varchar(255) NOT NULL DEFAULT '',
  `nik` varchar(255) NOT NULL,
  `no_rm` varchar(255) DEFAULT NULL,
  `tanggal_periksa` date NOT NULL,
  `no_antrean` varchar(255) NOT NULL,
  `no_kartu` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `no_referensi` varchar(255) NOT NULL,
  `jenis_referensi` int(11) NOT NULL,
  `jenis_request` int(11) NOT NULL,
  `poli_eksekutif` int(11) NOT NULL,
  `estimasi_dilayani` date NOT NULL,
  `sudah_dilayani` int(11) NOT NULL DEFAULT '0',
  `keterangan` longtext,
  `waktu` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime(3) DEFAULT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  `jkn` varchar(255) DEFAULT 'JKN',
  `jam_praktek` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `antreans` WRITE;
/*!40000 ALTER TABLE `antreans` DISABLE KEYS */;

INSERT INTO `antreans` (`id`, `kode_booking`, `kode_poli`, `kode_dokter`, `nik`, `no_rm`, `tanggal_periksa`, `no_antrean`, `no_kartu`, `no_telp`, `no_referensi`, `jenis_referensi`, `jenis_request`, `poli_eksekutif`, `estimasi_dilayani`, `sudah_dilayani`, `keterangan`, `waktu`, `status`, `created_at`, `updated_at`, `jkn`, `jam_praktek`)
VALUES
	(1,'ANA-20211227154106','ANA','','3309162404919001',NULL,'2021-12-27','001','1234567890123','0283182391283','1',1,1,0,'2021-12-27',0,'Mendadak siuman','1640649600','check_in','2021-12-27 15:41:06.000','2021-12-28 05:02:34.000','JKN',NULL),
	(2,'ANA-20211228053418','ANA','12345','3309162404919001','12345','2021-12-27','002','1234567890123','0283182391283','1',1,1,0,'2021-12-27',0,NULL,NULL,NULL,'2021-12-28 05:34:18.000','2021-12-28 05:34:18.000','JKN','10.30 - 12.00');

/*!40000 ALTER TABLE `antreans` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table operasis
# ------------------------------------------------------------

DROP TABLE IF EXISTS `operasis`;

CREATE TABLE `operasis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(255) NOT NULL,
  `kode_poli` varchar(255) NOT NULL,
  `tanggal_operasi` date NOT NULL,
  `no_peserta` varchar(255) DEFAULT NULL,
  `nama_dokter` varchar(255) DEFAULT NULL,
  `jenis_tindakan` varchar(255) DEFAULT NULL,
  `sudah_dilaksanakan` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime(3) DEFAULT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table pasienbaru
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pasienbaru`;

CREATE TABLE `pasienbaru` (
  `nomorkartu` varchar(50) NOT NULL,
  `norm` varchar(50) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nomorkk` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `jeniskelamin` varchar(50) DEFAULT NULL,
  `tanggallahir` datetime(3) DEFAULT NULL,
  `nohp` varchar(50) DEFAULT NULL,
  `alamat` varchar(250) DEFAULT NULL,
  `kodeprop` char(10) DEFAULT NULL,
  `namaprop` varchar(50) DEFAULT NULL,
  `kodedati2` char(10) DEFAULT NULL,
  `namadati2` varchar(50) DEFAULT NULL,
  `kodekec` char(10) DEFAULT NULL,
  `namakec` varchar(50) DEFAULT NULL,
  `kodekel` char(10) DEFAULT NULL,
  `namakel` varchar(50) DEFAULT NULL,
  `rw` char(10) DEFAULT NULL,
  `rt` char(10) DEFAULT NULL,
  PRIMARY KEY (`nomorkartu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `pasienbaru` WRITE;
/*!40000 ALTER TABLE `pasienbaru` DISABLE KEYS */;

INSERT INTO `pasienbaru` (`nomorkartu`, `norm`, `nik`, `nomorkk`, `nama`, `jeniskelamin`, `tanggallahir`, `nohp`, `alamat`, `kodeprop`, `namaprop`, `kodedati2`, `namadati2`, `kodekec`, `namakec`, `kodekel`, `namakel`, `rw`, `rt`)
VALUES
	('00012345678','00001','32123456787654','32123456787654','sumarsono','L','1985-03-01 00:00:00.000','08563522888','alamat lengkap saya','11','Jawa Barat','0120','Kab. Bandung','1319','Soreang','D2105','Cingcin','001','002');

/*!40000 ALTER TABLE `pasienbaru` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table poli_tutup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poli_tutup`;

CREATE TABLE `poli_tutup` (
  `kode_poli` varchar(255) NOT NULL,
  `tanggal_off` date NOT NULL,
  PRIMARY KEY (`kode_poli`,`tanggal_off`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table polis
# ------------------------------------------------------------

DROP TABLE IF EXISTS `polis`;

CREATE TABLE `polis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_poli` varchar(255) NOT NULL,
  `nama_poli` varchar(255) NOT NULL,
  `nama_dokter` varchar(255) DEFAULT NULL,
  `created_at` datetime(3) DEFAULT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `polis` WRITE;
/*!40000 ALTER TABLE `polis` DISABLE KEYS */;

INSERT INTO `polis` (`id`, `kode_poli`, `nama_poli`, `nama_dokter`, `created_at`, `updated_at`)
VALUES
	(1,'001','Umum','Dr. Sangat Sehat',NULL,NULL),
	(2,'AKP','AKUPUNTUR MEDIK',NULL,NULL,NULL),
	(3,'ANA','ANAK',NULL,NULL,NULL),
	(4,'AND','ANDROLOGI',NULL,NULL,NULL),
	(5,'ANT','ANASTESI',NULL,NULL,NULL),
	(6,'BDA','BEDAH ANAK',NULL,NULL,NULL),
	(7,'BDM','GIGI BEDAH MULUT',NULL,NULL,NULL),
	(8,'BDP','BEDAH PLASTIK',NULL,NULL,NULL),
	(9,'BED','BEDAH',NULL,NULL,NULL),
	(10,'BKL','BEDAH KEPALA LEHER',NULL,NULL,NULL),
	(11,'BSY','BEDAH SARAF',NULL,NULL,NULL),
	(12,'BTK','BTKV (BEDAH THORAX KARDIOVASKULER)',NULL,NULL,NULL),
	(13,'FMK','FARMAKOLOGI KLINIK',NULL,NULL,NULL),
	(14,'FOR','FORENSIK',NULL,NULL,NULL),
	(15,'GIG','GIGI',NULL,NULL,NULL),
	(16,'GIZ','GIZI KLINIK',NULL,NULL,NULL),
	(17,'GND','GIGI ENDODONSI',NULL,NULL,NULL),
	(18,'GOR','GIGI ORTHODONTI',NULL,NULL,NULL),
	(19,'GPR','GIGI PERIODONTI',NULL,NULL,NULL),
	(20,'GRD','GIGI RADIOLOGI',NULL,NULL,NULL),
	(21,'HIV','HIV-ODHA',NULL,NULL,NULL),
	(22,'INT','PENYAKIT DALAM',NULL,NULL,NULL),
	(23,'IRM','REHABILITASI MEDIK',NULL,NULL,NULL),
	(24,'JAN','JANTUNG DAN PEMBULUH DARAH',NULL,NULL,NULL),
	(25,'JIW','JIWA',NULL,NULL,NULL),
	(26,'KDK','KEDOKTERAN KELAUTAN',NULL,NULL,NULL),
	(27,'KDN','KEDOKTERAN NUKLIR',NULL,NULL,NULL),
	(28,'KDO','KEDOKTERAN OKUPASI',NULL,NULL,NULL),
	(29,'KDP','KEDOKTERAN PENERBANGAN',NULL,NULL,NULL),
	(30,'KLT','KULIT KELAMIN',NULL,NULL,NULL),
	(31,'KON','GIGI PEDODONTIS',NULL,NULL,NULL),
	(32,'KOR','KEDOKTERAAN OLAHRAGA',NULL,NULL,NULL),
	(33,'MAT','MATA',NULL,NULL,NULL),
	(34,'MKB','MIKROBIOLOGI KLINIK',NULL,NULL,NULL),
	(35,'OBG','OBGYN',NULL,NULL,NULL),
	(36,'ORT','ORTHOPEDI',NULL,NULL,NULL),
	(37,'PAA','PATOLOGI ANATOMI',NULL,NULL,NULL),
	(38,'PAK','PATOLOGI KLINIK',NULL,NULL,NULL),
	(39,'PAR','PARU',NULL,NULL,NULL),
	(40,'PNM','GIGI PENYAKIT MULUT',NULL,NULL,NULL),
	(41,'PRM','PARASITOLOGI UMUM',NULL,NULL,NULL),
	(42,'PTD','GIGI PROSTHODONTI',NULL,NULL,NULL),
	(43,'RDN','RADIOLOGI ONKOLOGI',NULL,NULL,NULL),
	(44,'RDO','RADIOLOGI',NULL,NULL,NULL),
	(45,'RDT','RADIOTERAPI',NULL,NULL,NULL),
	(46,'SAR','SARAF',NULL,NULL,NULL),
	(47,'THT','THT-KL',NULL,NULL,NULL),
	(48,'UMU','UMUM',NULL,NULL,NULL),
	(49,'URO','UROLOGI',NULL,NULL,NULL);

/*!40000 ALTER TABLE `polis` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime(3) DEFAULT NULL,
  `updated_at` datetime(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `updated_at`)
VALUES
	(1,'admin','$2y$10$icB5UhSOuWV8F6zfwZWHzOHnXczi9tDl.4PaqTpeq4Slnso6z.Ewq',NULL,NULL),
	(2,'mobilejkn','$2y$10$5Kv09WFDrATivb3lOI8NUu7o0T48FE0RrKU60pR/VP2T3ZG5UH2gW',NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
