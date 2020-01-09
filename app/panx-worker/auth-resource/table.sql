CREATE TABLE `users` (
	`ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`USERNAME` VARCHAR(32) NOT NULL COLLATE 'utf8mb4_bin',
	`EMAIL` VARCHAR(128) NOT NULL COLLATE 'utf8mb4_bin',
	`PASSWORD` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8mb4_bin',
	`VERIFIED` TINYINT(4) NULL DEFAULT '0',
	`ROLE` TINYINT(3) UNSIGNED NULL DEFAULT '50',
	`PERMISSIONS` TEXT NULL COLLATE 'utf8mb4_bin',
	`VERIFY_KEY` VARCHAR(16) NULL DEFAULT NULL COLLATE 'utf8mb4_bin',
	`FORGOT_TOKEN` VARCHAR(63) NULL DEFAULT NULL COLLATE 'utf8mb4_bin',
	`CREATED_AT` TIMESTAMP NULL DEFAULT NULL,
	`EDITED_AT` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`),
	UNIQUE INDEX `EMAIL` (`EMAIL`),
	UNIQUE INDEX `USERNAME` (`USERNAME`)
)
COLLATE='utf8mb4_bin'
ENGINE=InnoDB
AUTO_INCREMENT=23
;
