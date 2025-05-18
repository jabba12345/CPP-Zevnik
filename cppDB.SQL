/*
Created: 3. 04. 2025
Modified: 9. 05. 2025
Model: MySQL 8.0
Database: MySQL 8.0
*/

-- Create tables section -------------------------------------------------

-- Table uporabniki

CREATE TABLE `uporabniki`
(
  `uporabniki_id` Int NOT NULL AUTO_INCREMENT,
  `ime` Varchar(100) NOT NULL,
  `geslo` Varchar(256) NOT NULL,
  `email` Varchar(100) NOT NULL,
  PRIMARY KEY (`uporabniki_id`)
)
;

-- Table kategorije

CREATE TABLE `kategorije`
(
  `kategorije_id` Int NOT NULL AUTO_INCREMENT,
  `ime` Varchar(20) NOT NULL,
  `opis` Text,
  PRIMARY KEY (`kategorije_id`)
)
;

-- Table vprasanja

CREATE TABLE `vprasanja`
(
  `vprasanja_id` Int NOT NULL AUTO_INCREMENT,
  `vprasanje` Varchar(500) NOT NULL,
  `tipi_vprasanja_id` Int,
  `tocke_vprasanja` Float NOT NULL,
  PRIMARY KEY (`vprasanja_id`)
)
;

CREATE INDEX `IX_Relationship13` ON `vprasanja` (`tipi_vprasanja_id`)
;

-- Table odgovori

CREATE TABLE `odgovori`
(
  `odgovori_id` Int NOT NULL AUTO_INCREMENT,
  `odgovor` Varchar(500) NOT NULL,
  `je_pravilen` Bool NOT NULL,
  `vprasanja_id` Int,
  `odgovori_tocke` Float NOT NULL,
  PRIMARY KEY (`odgovori_id`)
)
;

CREATE INDEX `IX_Relationship5` ON `odgovori` (`vprasanja_id`)
;

-- Table testi

CREATE TABLE `testi`
(
  `testi_id` Int NOT NULL AUTO_INCREMENT,
  `datum_cas` Timestamp NOT NULL,
  `uporabniki_id` Int,
  PRIMARY KEY (`testi_id`)
)
;

CREATE INDEX `IX_Relationship11` ON `testi` (`uporabniki_id`)
;

-- Table slike

CREATE TABLE `slike`
(
  `slike_id` Int NOT NULL AUTO_INCREMENT,
  `slika` Longblob NOT NULL,
  `vprasanja_id` Int,
  PRIMARY KEY (`slike_id`)
)
;

CREATE INDEX `IX_Relationship12` ON `slike` (`vprasanja_id`)
;

-- Table kategorije_vprasanja

CREATE TABLE `kategorije_vprasanja`
(
  `kategorije_vprasanja_id` Int NOT NULL AUTO_INCREMENT,
  `kategorije_id` Int,
  `vprasanja_id` Int,
  PRIMARY KEY (`kategorije_vprasanja_id`)
)
;

CREATE INDEX `IX_Relationship3` ON `kategorije_vprasanja` (`kategorije_id`)
;

CREATE INDEX `IX_Relationship4` ON `kategorije_vprasanja` (`vprasanja_id`)
;

-- Table tipi_vprasanja

CREATE TABLE `tipi_vprasanja`
(
  `tipi_vprasanja_id` Int NOT NULL AUTO_INCREMENT,
  `ime` Varchar(100) NOT NULL,
  PRIMARY KEY (`tipi_vprasanja_id`)
)
;

-- Table odgovori_uporabnikov

CREATE TABLE `odgovori_uporabnikov`
(
  `odgovori_uporabnikov_id` Int NOT NULL AUTO_INCREMENT,
  `testi_id` Int,
  `vprasanja_id` Int,
  `odgovori_id` Int,
  `uporabniki_id` Int,
  PRIMARY KEY (`odgovori_uporabnikov_id`)
)
;

CREATE INDEX `IX_Relationship14` ON `odgovori_uporabnikov` (`testi_id`)
;

CREATE INDEX `IX_Relationship15` ON `odgovori_uporabnikov` (`vprasanja_id`)
;

CREATE INDEX `IX_Relationship16` ON `odgovori_uporabnikov` (`odgovori_id`)
;

CREATE INDEX `IX_Relationship17` ON `odgovori_uporabnikov` (`uporabniki_id`)
;

-- Create foreign keys (relationships) section -------------------------------------------------

ALTER TABLE `kategorije_vprasanja` ADD CONSTRAINT `Relationship3` FOREIGN KEY (`kategorije_id`) REFERENCES `kategorije` (`kategorije_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `kategorije_vprasanja` ADD CONSTRAINT `Relationship4` FOREIGN KEY (`vprasanja_id`) REFERENCES `vprasanja` (`vprasanja_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `odgovori` ADD CONSTRAINT `Relationship5` FOREIGN KEY (`vprasanja_id`) REFERENCES `vprasanja` (`vprasanja_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `testi` ADD CONSTRAINT `Relationship11` FOREIGN KEY (`uporabniki_id`) REFERENCES `uporabniki` (`uporabniki_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `slike` ADD CONSTRAINT `Relationship12` FOREIGN KEY (`vprasanja_id`) REFERENCES `vprasanja` (`vprasanja_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `vprasanja` ADD CONSTRAINT `Relationship13` FOREIGN KEY (`tipi_vprasanja_id`) REFERENCES `tipi_vprasanja` (`tipi_vprasanja_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `odgovori_uporabnikov` ADD CONSTRAINT `Relationship14` FOREIGN KEY (`testi_id`) REFERENCES `testi` (`testi_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `odgovori_uporabnikov` ADD CONSTRAINT `Relationship15` FOREIGN KEY (`vprasanja_id`) REFERENCES `vprasanja` (`vprasanja_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `odgovori_uporabnikov` ADD CONSTRAINT `Relationship16` FOREIGN KEY (`odgovori_id`) REFERENCES `odgovori` (`odgovori_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

ALTER TABLE `odgovori_uporabnikov` ADD CONSTRAINT `Relationship17` FOREIGN KEY (`uporabniki_id`) REFERENCES `uporabniki` (`uporabniki_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
;

