-- --------------------------------------------------------
-- Hôte :                        vlpi05.poli.network
-- Version du serveur:           10.3.20-MariaDB-0ubuntu0.19.04.1 - Ubuntu 19.04
-- SE du serveur:                debian-linux-gnu
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Listage de la structure de la base pour Factorio-Stats
CREATE DATABASE IF NOT EXISTS `Factorio-Stats` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `Factorio-Stats`;

-- Listage de la structure de la table Factorio-Stats. Ban
CREATE TABLE IF NOT EXISTS `Ban` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKPlayerId` int(255) NOT NULL,
  `FKBanRuleId` int(255) DEFAULT NULL,
  `Reason` varchar(600) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Player` (`FKPlayerId`),
  KEY `FK_Ban_Sync` (`FKBanRuleId`),
  CONSTRAINT `FK_BanRule_Ban` FOREIGN KEY (`FKBanRuleId`) REFERENCES `BanRules` (`Id`),
  CONSTRAINT `FK_Player_Ban` FOREIGN KEY (`FKPlayerId`) REFERENCES `Players` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la procédure Factorio-Stats. BanPlayer
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `BanPlayer`(
	IN `PlayerId` INT,
	IN `Reason` Varchar(255)
)
BEGIN

	 IF(SELECT EXISTS(SELECT * FROM Ban WHERE FKPlayerId = PlayerId)) THEN 
		 set @message_text = CONCAT('PlayerId ', PlayerId , ' is already banned');
		 SIGNAL SQLSTATE '45000'
		 SET MESSAGE_TEXT =  @message_text;
	 END IF;

	INSERT INTO Ban
	(`FKPlayerId`,
	`Reason`)
	VALUES
	( PlayerId,
	  Reason);
END//
DELIMITER ;

-- Listage de la structure de la table Factorio-Stats. BanRules
CREATE TABLE IF NOT EXISTS `BanRules` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `RuleName` varchar(200) NOT NULL,
  `Description` varchar(200) DEFAULT NULL,
  `Source` varchar(200) DEFAULT NULL,
  `Notes` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='This table is for the Factorio Anti-Griefer Coordination';

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. ConnectedServer
CREATE TABLE IF NOT EXISTS `ConnectedServer` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKPlayerId` int(255) DEFAULT NULL,
  `FKServerId` int(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Player` (`FKPlayerId`),
  KEY `FK_Server` (`FKServerId`),
  CONSTRAINT `FK_Conn_Player` FOREIGN KEY (`FKPlayerId`) REFERENCES `Players` (`Id`),
  CONSTRAINT `FK_Conn_Server` FOREIGN KEY (`FKServerId`) REFERENCES `Server` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la procédure Factorio-Stats. DeleteServer
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteServer`(
	IN `ServerId` INT,
	IN `HardDelete` Bit

)
BEGIN
	-- If Server not Exists
	IF not exists ( SELECT * FROM Server WHERE Id = ServerId ) THEN 
		Begin			
			set @msg  = CONCAT('serverId ', ServerId , ' does not exist');
			SIGNAL SQLSTATE '45000'
			set MESSAGE_TEXT = @msg;
		End;
	ELSE
		-- Deletes from all FK tables
		 Delete From ConnectedServer
		 Where FKServerId = ServerId;
		 
		Delete From Log
		Where FKServerId = ServerId;

		Delete From PlayersOnline
		Where FKServerId = ServerId;

		Delete From Production
		Where FKServerId = ServerId;

		Delete From ServerConfig
		Where FKServerId = ServerId;

		Delete From Ticks
		Where FKServerId = ServerId;
        
        -- IF HardDelete,  removes record itself
        IF HardDelete = true THEN
				DELETE from Server
                Where Id = ServerId;
        End IF;        
	END IF;
END//
DELIMITER ;

-- Listage de la structure de la table Factorio-Stats. Log
CREATE TABLE IF NOT EXISTS `Log` (
  `Id` bigint(255) NOT NULL AUTO_INCREMENT,
  `FKServerId` int(255) DEFAULT NULL,
  `FKTicksId` int(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp(),
  `TotalPlayersOnline` int(255) DEFAULT NULL,
  `TotalPlayers` int(255) DEFAULT NULL,
  `RocketCount` int(11) DEFAULT NULL,
  `AlienEvolution` double DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Server` (`FKServerId`),
  KEY `FK_Ticks` (`FKTicksId`),
  CONSTRAINT `FK_Log_Server` FOREIGN KEY (`FKServerId`) REFERENCES `Server` (`Id`),
  CONSTRAINT `FK_Log_Ticks` FOREIGN KEY (`FKTicksId`) REFERENCES `Ticks` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. Material
CREATE TABLE IF NOT EXISTS `Material` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `MaterialName` varchar(50) NOT NULL,
  `InGameName` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. PlayerPlaytime
CREATE TABLE IF NOT EXISTS `PlayerPlaytime` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKPlayerId` int(255) NOT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Ticks` int(255) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Player` (`FKPlayerId`),
  CONSTRAINT `FK_Playtime_Player` FOREIGN KEY (`FKPlayerId`) REFERENCES `Players` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. Players
CREATE TABLE IF NOT EXISTS `Players` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `PlayerName` varchar(50) DEFAULT NULL,
  `IsAdmin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la procédure Factorio-Stats. PlayersOnline
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `PlayersOnline`()
BEGIN
    SELECT S.ServerName,
		   Count(P.PlayerName),
		   S.IP,
		   S.Status,
		   S.Version,
		   S.IsReseting
	FROM   PlayersOnline
		   INNER JOIN Players AS P
				   ON PlayersOnline.FKPlayerId = P.Id
		   RIGHT JOIN Server AS S
				   ON PlayersOnline.FKServerId = S.Id
	WHERE S.Status = "On"
	GROUP  BY S.ServerName
	ORDER  BY S.ServerName
	;
END//
DELIMITER ;

-- Listage de la structure de la table Factorio-Stats. PlayersOnline
CREATE TABLE IF NOT EXISTS `PlayersOnline` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKPlayerId` int(255) DEFAULT NULL,
  `FKServerId` int(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Player` (`FKPlayerId`),
  KEY `FK_Server` (`FKServerId`),
  CONSTRAINT `FK_Online_Players` FOREIGN KEY (`FKPlayerId`) REFERENCES `Players` (`Id`),
  CONSTRAINT `FK_Online_Server` FOREIGN KEY (`FKServerId`) REFERENCES `Server` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la procédure Factorio-Stats. PlayersOnlineOld
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `PlayersOnlineOld`()
BEGIN
	SELECT 
		Se.ServerName,
		TotalPlayersOnline,
		TotalPlayers,
		CreatedDate
	FROM
		(SELECT 
				FKServerId,
				CreatedDate,
				TotalPlayers,
				TotalPlayersOnline,
				@rn:=IF(@prev = FKServerId, @rn + 1, 1) AS rn,
				@prev:=FKServerId
		FROM
			Log
		JOIN (SELECT @prev:=NULL, @rn:=0) AS vars
		ORDER BY FKServerId , CreatedDate DESC) AS T1
		INNER JOIN Server AS Se on Se.Id = T1.FKServerId 
	WHERE
		rn <= 1
	ORDER BY Se.ServerName;    
END//
DELIMITER ;

-- Listage de la structure de la table Factorio-Stats. Production
CREATE TABLE IF NOT EXISTS `Production` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKMaterialId` int(255) NOT NULL,
  `FKServerId` int(255) NOT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp(),
  `NumberProduced` bigint(20) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Material` (`FKMaterialId`),
  KEY `FK_Server` (`FKServerId`),
  CONSTRAINT `FK_Production_Material` FOREIGN KEY (`FKMaterialId`) REFERENCES `Material` (`Id`),
  CONSTRAINT `FK_Production_Server` FOREIGN KEY (`FKServerId`) REFERENCES `Server` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. Server
CREATE TABLE IF NOT EXISTS `Server` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `ServerName` varchar(50) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Version` varchar(50) DEFAULT NULL,
  `IP` varchar(50) DEFAULT NULL,
  `IsReseting` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. ServerConfig
CREATE TABLE IF NOT EXISTS `ServerConfig` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKServerId` int(255) DEFAULT NULL,
  `RconPort` int(5) DEFAULT NULL,
  `RconPassword` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FKServerId` (`FKServerId`),
  CONSTRAINT `FK_ServerConfig_Server` FOREIGN KEY (`FKServerId`) REFERENCES `Server` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table Factorio-Stats. Ticks
CREATE TABLE IF NOT EXISTS `Ticks` (
  `Id` int(255) NOT NULL AUTO_INCREMENT,
  `FKServerId` int(255) NOT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `CurrentTicks` int(255) DEFAULT NULL,
  `OldTicks` int(255) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Server` (`FKServerId`),
  CONSTRAINT `FK_Ticks_Server` FOREIGN KEY (`FKServerId`) REFERENCES `Server` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la procédure Factorio-Stats. UnbanPlayer
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `UnbanPlayer`(
	IN `PlayerId` INT
	
)
BEGIN
	 IF(SELECT NOT EXISTS(SELECT * FROM Ban WHERE PlayerId)) THEN 
			 set @message_text = CONCAT('PlayerId ', PlayerId , ' is already unbanned');
			 SIGNAL SQLSTATE '45000'
			 SET MESSAGE_TEXT =  @message_text;
		 END IF;

		DELETE FROM `Factorio-Stats`.`Ban`
		WHERE FKPlayerId = PlayerId;
END//
DELIMITER ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
