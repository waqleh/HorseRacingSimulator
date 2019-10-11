-- @author: Walid Aqleh <waleedakleh23@hotmail.com>


-- DO NOT UPLOAD THIS FILE TO THE SERVER WITHOUT PROTECTING ACCESS TO THE FOLDER AT LEAST

--
-- Database: `horse_racing_simulator`
--

CREATE TABLE `race` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `progress_time` double NOT NULL DEFAULT '0',
  `finish_time` double DEFAULT NULL,
  `finished` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=inprogress, 1=finished',
  PRIMARY KEY (`id`),
  KEY `finished` (`finished`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `horse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `race_id` int(11) NOT NULL,
  `speed` double(3,1) NOT NULL,
  `strength` double(3,1) NOT NULL,
  `endurance` double(3,1) NOT NULL,
  `finished_in_seconds` double NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`race_id`) REFERENCES `race`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;