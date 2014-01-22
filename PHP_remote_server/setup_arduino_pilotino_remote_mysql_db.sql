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

--
-- Dumping data for table `arduino_commands`
--


-- --------------------------------------------------------

--
-- Table structure for table `arduino_sensors`
--

CREATE TABLE IF NOT EXISTS `arduino_sensors` (
  `timestamp` bigint(20) NOT NULL,
  `board_id` varchar(64) NOT NULL,
  `sensor_name` varchar(8) NOT NULL,
  `sensor_value` varchar(8) NOT NULL,
  `other_data_1` varchar(255) NOT NULL,
  `other_data_2` varchar(255) NOT NULL,
  KEY `timestamp` (`timestamp`),
  KEY `board_id` (`board_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Here pilot.ino writes acquired sensor data';

--
-- Dumping data for table `arduino_sensors`
--