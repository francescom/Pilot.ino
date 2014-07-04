-- --------------------------------------------------------

--
-- Table structure for table `arduino_commands`
--

CREATE TABLE IF NOT EXISTS `arduino_commands` (
  `timestamp` bigint(20) NOT NULL,
  `board_id` varchar(64) NOT NULL,
  `order` int(11) NOT NULL,
  `command_text` varchar(255) NOT NULL,
  `other_data_1` varchar(255) NOT NULL,
  `other_data_2` varchar(255) NOT NULL,
  KEY `timestamp` (`timestamp`),
  KEY `board_id` (`board_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Here pilot.ino reads commands to send to the arduino board, ';

-- --------------------------------------------------------

--
-- Table structure for table `arduino_sensors`
--

CREATE TABLE IF NOT EXISTS `arduino_sensors` (
  `timestamp` bigint(20) NOT NULL,
  `board_id` varchar(64) NOT NULL,
  `sensor_name` varchar(8) NOT NULL,
  `is_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'is_status to 1 means it is not a sensor but a key value pair set with a save command',
  `sensor_value` varchar(8) NOT NULL,
  `other_data_1` varchar(255) NOT NULL,
  `other_data_2` varchar(255) NOT NULL,
  KEY `timestamp` (`timestamp`),
  KEY `board_id` (`board_id`),
  KEY `is_status` (`is_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Here pilot.ino writes acquired sensor data';
