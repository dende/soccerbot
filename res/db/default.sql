
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- teams
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `teams`;

CREATE TABLE `teams`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(5) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- matches
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `matches`;

CREATE TABLE `matches`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `home_team_id` INTEGER NOT NULL,
    `away_team_id` INTEGER NOT NULL,
    `home_team_goals` INTEGER NOT NULL,
    `away_team_goals` INTEGER NOT NULL,
    `status` VARCHAR(255) NOT NULL,
    `date` DATETIME NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `matches_u_d64a9a` (`home_team_id`, `away_team_id`, `date`),
    INDEX `matches_fi_d2f15d` (`away_team_id`),
    CONSTRAINT `matches_fk_44ba29`
        FOREIGN KEY (`home_team_id`)
        REFERENCES `teams` (`id`),
    CONSTRAINT `matches_fk_d2f15d`
        FOREIGN KEY (`away_team_id`)
        REFERENCES `teams` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- privatechats
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `privatechats`;

CREATE TABLE `privatechats`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `chat_id` INTEGER NOT NULL,
    `type` VARCHAR(20) NOT NULL,
    `state` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `privatechats_u_8d0865` (`chat_id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- groupchats
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `groupchats`;

CREATE TABLE `groupchats`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `chat_id` INTEGER NOT NULL,
    `type` VARCHAR(20) NOT NULL,
    `state` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `groupchats_u_8d0865` (`chat_id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
