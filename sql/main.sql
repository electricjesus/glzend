

-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 20, 2011 at 09:26 AM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `glcorporate`
--
DROP DATABASE `glcorporate`;
CREATE DATABASE `glcorporate` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `glcorporate`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Art Deco'),
(2, 'Art Nouveau'),
(3, 'Late Victorian');

-- --------------------------------------------------------

--
-- Table structure for table `featured`
--

DROP TABLE IF EXISTS `featured`;
CREATE TABLE IF NOT EXISTS `featured` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `location` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `featured`
--

INSERT INTO `featured` (`id`, `product_id`, `location`) VALUES
(1, 22, 'category'),
(2, 27, 'category'),
(3, 29, 'category'),
(4, 31, 'category'),
(5, 34, 'category'),
(6, 56, 'category'),
(7, 67, 'category'),
(8, 68, 'category'),
(9, 71, 'category'),
(10, 85, 'category'),
(11, 92, 'category'),
(12, 94, 'category'),
(13, 101, 'category'),
(14, 103, 'category'),
(15, 104, 'category'),
(16, 108, 'category'),
(17, 115, 'category'),
(18, 21, 'homepage'),
(19, 20, 'homepage'),
(20, 19, 'homepage');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(24) NOT NULL,
  `image` varchar(64) NOT NULL,
  `description` varchar(128) NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `subcategory_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`subcategory_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=125 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `image`, `description`, `category_id`, `subcategory_id`) VALUES
(15, '35128', 'GL-Art_Deco/AD_Art_Deco/35128.jpg', 'Brooch<P>Black cold Enamel<BR>Swarovski Crystals', 1, 22),
(16, '64700', 'GL-Art_Deco/AD_Art_Deco/64700.jpg', 'Necklace<P>Black cold Enamel<BR>Swarovski Crystals', 1, 22),
(17, '70000', 'GL-Art_Deco/AD_Art_Deco/70000.jpg', 'Stud Earring<P>Black cold Enamel<BR>Swarovski Crystals', 1, 22),
(18, '15555-1', 'GL-Art_Deco/AD_Boucheron/15555-1.jpg', 'Ring<P>Swarovski Crystals', 1, 2),
(19, '15555', 'GL-Art_Deco/AD_Boucheron/15555.jpg', 'Ring<P>Swarovski Crystals', 1, 2),
(20, '24951', 'GL-Art_Deco/AD_Boucheron/24951.jpg', 'Pendant<P>Swarovski Crystals', 1, 2),
(21, '54848', 'GL-Art_Deco/AD_Boucheron/54848.jpg', 'Bracelet<P>Swarovski Crystals', 1, 2),
(22, '70781', 'GL-Art_Deco/AD_Boucheron/70781.jpg', 'Stud Earring<P>Swarovski Crystals', 1, 2),
(23, '15566-1', 'GL-Art_Deco/AD_Cartier/15566-1.jpg', 'Ring<P>Swarovski Crystals', 1, 3),
(26, '24991', 'GL-Art_Deco/AD_Cartier/24991.jpg', 'Pendant<P>Swarovski Crystals', 1, 3),
(27, '65191', 'GL-Art_Deco/AD_Cartier/65191.jpg', 'Necklace<P>Swarovski Crystals', 1, 3),
(28, '65192', 'GL-Art_Deco/AD_Cartier/65192.jpg', 'Necklace<P>Swarovski Crystals', 1, 3),
(29, '70766', 'GL-Art_Deco/AD_Cartier/70766.jpg', 'Stud Earring<P>Swarovski Crystals', 1, 3),
(30, '70804', 'GL-Art_Deco/AD_Cartier/70804.jpg', 'Stud Earring<P>Swarovski Crystals', 1, 3),
(31, '35742', 'GL-Art_Deco/AD_Josef_Hoffman/35742.jpg', 'Brooch<P>Black cold Enamel<BR>Swarovski Crystals', 1, 4),
(32, '70759', 'GL-Art_Deco/AD_Josef_Hoffman/70759.jpg', 'Stud Earring<P>Black cold Enamel<BR>Swarovski Crystals', 1, 4),
(33, '24929', 'GL-Art_Deco/AD_Van_Cleef_Arpels/24929.jpg', 'Pendant<P>Swarovski Crystals', 1, 5),
(34, '54846', 'GL-Art_Deco/AD_Van_Cleef_Arpels/54846.jpg', 'Bracelet<P>Swarovski Crystals', 1, 5),
(35, '70738', 'GL-Art_Deco/AD_Van_Cleef_Arpels/70738.jpg', 'Stud Earring<P>Swarovski Crystals', 1, 5),
(36, '33461', 'GL-Art_Deco/AD_Van_Cleef_Arpels_1950/33461.jpg', 'Brooch<P>Swarovski Crystals', 1, 6),
(37, '33506', 'GL-Art_Deco/AD_Van_Cleef_Arpels_1950/33506.jpg', 'Brooch<P>Sandblasted face', 1, 0),
(38, '35515', 'GL-Art_Deco/AD_Van_Cleef_Arpels_1950/35515.jpg', 'Brooch<P>Sandblasted woman', 1, 6),
(39, '14965', 'GL-Art_Deco/AD_Wiener_Werkstatte/14965.jpg', 'Ring<P>Black cold Enamel<BR>Swarovski Crystals', 1, 7),
(40, '35124', 'GL-Art_Deco/AD_Wiener_Werkstatte/35124.jpg', 'Brooch<P>Black cold Enamel<BR>Swarovski Crystals', 1, 7),
(41, '54841', 'GL-Art_Deco/AD_Wiener_Werkstatte/54841.jpg', 'Bracelet<P>Black cold Enamel<BR>Swarovski Crystals', 1, 7),
(46, '12448', 'GL-Art_Nouveau/AN_Alfons_Mucha/12448.jpg', 'Ring<P>Sandblasted face', 2, 8),
(48, '12547', 'GL-Art_Nouveau/AN_Alfons_Mucha/12547.jpg', 'Ring<P>Sandblasted woman', 2, 8),
(49, '13411-1', 'GL-Art_Nouveau/AN_Alfons_Mucha/13411-1.jpg', 'Ring<P>Sandblasted face', 2, 8),
(51, '33117', 'GL-Art_Nouveau/AN_Alfons_Mucha/33117.jpg', 'Brooch / Pendant<P>Sandblasted face', 2, 8),
(52, '33504', 'GL-Art_Nouveau/AN_Alfons_Mucha/33504.jpg', 'Brooch<P>Sandblasted woman', 2, 8),
(53, '65206', 'GL-Art_Nouveau/AN_Alfons_Mucha/65206.jpg', 'Necklace<BR>Paris 1900<BR>Specially made for Sarah Bernard<P>Sandblasted face<BR>Pearl', 2, 8),
(54, '34041', 'GL-Art_Nouveau/AN_Archibald_Knox/34041.jpg', 'Brooch<P>Black cold Enamel<BR>Swarovski Crystal', 2, 9),
(56, '64502', 'GL-Art_Nouveau/AN_Archibald_Knox/64502.jpg', 'Necklace<P>Black cold Enamel<BR>Swarovski Crystal', 2, 9),
(57, '70760', 'GL-Art_Nouveau/AN_Archibald_Knox/70760.jpg', 'Drop Earring<P>Black cold Enamel', 2, 9),
(58, '35781', 'GL-Art_Nouveau/AN_C_Driguine/35781.jpg', 'Brooch<BR>Paris ca. 1904<P>Sandblasted woman<BR>Swarovski Crystals', 2, 10),
(59, '65242', 'GL-Art_Nouveau/AN_C_Driguine/65242.jpg', 'Necklace<BR>Paris ca. 1904<P>Sandblasted woman<BR>Swarovski Crystals', 2, 10),
(60, '34806', 'GL-Art_Nouveau/AN_Cartier_Siegel/34806.jpg', 'Brooch<BR>(two pieces)<P>Swarovski Crystals', 2, 11),
(61, '35125', 'GL-Art_Nouveau/AN_Cartier_Siegel/35125.jpg', 'Brooch<P>Sandblasted woman', 2, 11),
(62, '35430', 'GL-Art_Nouveau/AN_Cartier_Siegel/35430.jpg', 'Brooch<P>Sandblasted woman', 2, 11),
(63, '35776', 'GL-Art_Nouveau/AN_Cartier_Siegel/35776.jpg', 'Brooch<P>Sandblasted woman', 2, 11),
(66, '14710', 'GL-Art_Nouveau/AN_Florale_motieven/14710.jpg', 'Ring<P>Swarovski Crystals', 2, 12),
(67, '25015', 'GL-Art_Nouveau/AN_Florale_motieven/25015.jpg', 'Pendant<P>High Polish<BR>Pearls', 2, 12),
(68, '34952', 'GL-Art_Nouveau/AN_Florale_motieven/34952.jpg', 'Brooch<P>Swarovski Crystals<BR>Pearl', 2, 12),
(69, '35537', 'GL-Art_Nouveau/AN_Florale_motieven/35537.jpg', 'Brooch<P>High Polish<BR>Pearl', 2, 12),
(70, '70440', 'GL-Art_Nouveau/AN_Florale_motieven/70440.jpg', 'Hooked Earring<P>High Polish<BR>Pearls', 2, 12),
(71, 'EM35780-1', 'GL-Art_Nouveau/AN_Gaston_Lattitte/EM-35780-1.jpg', 'Brooch<BR>Paris (ca. 1904)<P>Plique a Jour Enamel<BR>Sandblasted woman<BR>Swarovski Crystals<BR>Pearl', 2, 13),
(72, '35731', 'GL-Art_Nouveau/AN_Georges_Brunet/35731.jpg', 'Brooch / Pendant (1895)<P>Sandblasted face<BR>Pearl', 2, 14),
(73, '34039', 'GL-Art_Nouveau/AN_Joseph_Maria_Olbrich/34039.jpg', 'Brooch / Pendant<P>High Polish<BR>Swarovski Crystal', 2, 15),
(74, '34058', 'GL-Art_Nouveau/AN_Joseph_Maria_Olbrich/34058.jpg', 'Brooch<P>High Polish<BR>Black cold Enamel', 2, 15),
(75, '34407', 'GL-Art_Nouveau/AN_Joseph_Maria_Olbrich/34407.jpg', 'Brooch / Pendant<P>High Polish', 2, 15),
(76, '35710', 'GL-Art_Nouveau/AN_Luis_Masriera/35710.jpg', 'Brooch / Pendant<BR>ca. 1905<P>Sandblasted woman<BR>Pearl', 2, 16),
(77, '35715', 'GL-Art_Nouveau/AN_Luis_Masriera/35715.jpg', 'Brooch / Pendant<BR>ca. 1905<P>Sandblasted woman<BR>Pearl', 2, 16),
(79, '15451', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/15451.jpg', 'Ring<P>Sandblasted Cherub', 2, 17),
(80, '24811 SB', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/24811 SB.jpg', 'Pendant<P>Sandblasted Cupid', 2, 17),
(81, '24821', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/24821.jpg', 'Pendant<P>Sandblasted Cherub', 2, 17),
(82, '24822', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/24822.jpg', 'Pendant<P>Sandblasted Cherub', 2, 17),
(83, '24847', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/24847.jpg', 'Pendant<P>Sandblasted Cherub', 2, 17),
(84, '35209', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/35209.jpg', 'Brooch / Pendant<P>Sandblasted Cupid', 2, 17),
(85, '35542', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/35542.jpg', 'Brooch<P>Sandblasted Cupid<BR>Swarovski Crystals', 2, 17),
(86, '35557', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/35557.jpg', 'Brooch<P>Sandblasted Cupid', 2, 17),
(87, '35597', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/35597.jpg', 'Brooch<P>Sandblasted Cupid<BR>Swarovski Crystals', 2, 17),
(89, '75048', 'GL-Art_Nouveau/AN_Putti_Cherubijntjes/75048.jpg', 'Drop Earring<P>Sandblasted Cherub', 2, 17),
(90, '34909', 'GL-Art_Nouveau/AN_Rene_Lalique/34909.jpg', 'Brooch<BR>Dragonfly<P>Swarovski Crystals', 2, 18),
(91, '35001', 'GL-Art_Nouveau/AN_Rene_Lalique/35001.jpg', 'Brooch<BR>Dragonfly<P>Swarovski Crystals', 2, 18),
(92, '53368', 'GL-Art_Nouveau/AN_Rene_Lalique/53368.jpg', 'Open Bangle<BR>Dragonfly', 2, 18),
(93, '65168', 'GL-Art_Nouveau/AN_Rene_Lalique/65168.jpg', 'Necklace<BR>Dragonfly woman', 2, 18),
(94, '65217', 'GL-Art_Nouveau/AN_Rene_Lalique/65217.jpg', 'Necklace<BR>Two Dragonflies<P>Swarovski Crystals', 2, 18),
(95, 'EM14710-1', 'GL-Art_Nouveau/AN_Rene_Lalique/EM 14710-1.jpg', 'Ring<P>Champleve Enamel<BR>Swarovski Crystals', 2, 18),
(96, 'EM35431-1', 'GL-Art_Nouveau/AN_Rene_Lalique/EM 35431-1.jpg', 'Brooch<BR>Butterfly<P>Plique a Jour Enamel<BR>Swarovski Crystals', 2, 18),
(98, 'EM24101-1', 'GL-Art_Nouveau/AN_Rene_Lalique/EM-24101-1.jpg', 'Pendant<P>Plique a Jour Enamel<BR>Swarovski Crystals', 2, 18),
(99, 'EM79835-1', 'GL-Art_Nouveau/AN_Rene_Lalique/EM-79835-1.jpg', 'Stud Earring<P>Plique a Jour Enamel<BR>Swarovski Crystals', 2, 18),
(100, '14219', 'Late_Victorian/14219.jpg', 'Ring<P/><P/>Swarovski Crystals', 3, 26),
(101, '25012', 'Late_Victorian/25012.jpg', 'Pendant<P>Swarovski Crystals', 3, 26),
(102, '25049', 'Late_Victorian/25049.jpg', 'Pendant<P>Swarovski Crystals<BR>Pearls', 3, 26),
(103, '34751', 'Late_Victorian/34751.jpg', 'Brooch<P>Swarovski Crystals<BR>Pearl', 3, 26),
(104, '34976', 'Late_Victorian/34976.jpg', 'Brooch<P>Swarovski Crystals<BR>Pearl', 3, 26),
(105, '35669', 'Late_Victorian/35669.jpg', 'Brooch<P>Swarovski Crystals<BR>Pearl', 3, 26),
(106, '54500', 'Late_Victorian/54500.jpg', 'Bracelet<P>Swarovski Crystals', 3, 26),
(108, '64601', 'Late_Victorian/64601.jpg', 'Necklace<P>Swarovski Crystals<BR>Pearl', 3, 26),
(109, '65233', 'Late_Victorian/65233.jpg', 'Necklace<P>Swarovski Crystals', 3, 26),
(111, '24943', 'Late_Victorian/LV_Elisabeth_Empress/24943.jpg', 'Pendant<BR>Specially made for the Sissi Museum in Vienna<P>Swarovski Crystals<BR>Pearls', 3, 19),
(114, '70573', 'Late_Victorian/LV_Elisabeth_Empress/70573.jpg', 'Drop Earring<BR>Specially made for the Sissi Museum in Vienna<P>Swarovski Crystals<BR>Pearl', 3, 19),
(115, '70681', 'Late_Victorian/LV_Elisabeth_Empress/70681.jpg', 'Stud Earring<BR>Specially made for the Sissi Museum in Vienna<P>Swarovski Crystals<BR>Pearl', 3, 19),
(116, '65155', 'GL-Art_Deco/AD_Wiener_Werkstatte/65155.jpg', 'Necklace<P>Black cold Enamel<BR>Swarovski Crystals', 0, 7),
(117, '65237', 'GL-Art_Deco/AD_Wiener_Werkstatte/65237.jpg', 'Necklace<P>Black cold Enamel<BR>Swarovski Crystals', 0, 0),
(118, '79142', 'GL-Art_Deco/AD_Wiener_Werkstatte/79142.jpg', 'Drop Earring<P>Black cold Enamel<BR>Swarovski Crystals', 0, 7),
(119, 'EM65157-3', '', 'Necklace<BR>Exposition Universelle paris 1900<P>Champleve Enamel<BR>Sandblasted face<BR>Pearl', 0, 0),
(120, '65238', '', 'Necklace<BR>Paris (ca. 1904)<P>Sandblasted woman<BR>Swarovski Crystals<BR>Pearl', 0, 0),
(121, '33135', '', 'Brooch / Pendant<P>Sandblasted face', 0, 0),
(122, '35506', '', 'Brooch<P>Swarovski Crystals<BR>Pearl', 0, 6),
(123, '35024', '', 'Brooch<P>High Polish', 0, 20),
(124, '65230', '', 'Necklace<P>Swarovski Crystals', 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `sort`
--

DROP TABLE IF EXISTS `sort`;
CREATE TABLE IF NOT EXISTS `sort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `item_type` varchar(16) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `location` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=172 ;

--
-- Dumping data for table `sort`
--

INSERT INTO `sort` (`id`, `item_id`, `item_type`, `sort_order`, `location`) VALUES
(171, 17, 'product', 25, 'category'),
(170, 16, 'product', 24, 'category'),
(169, 15, 'product', 23, 'category'),
(168, 41, 'product', 22, 'category'),
(167, 40, 'product', 21, 'category'),
(166, 39, 'product', 20, 'category'),
(165, 38, 'product', 19, 'category'),
(164, 36, 'product', 18, 'category'),
(163, 35, 'product', 17, 'category'),
(162, 34, 'product', 16, 'category'),
(161, 33, 'product', 15, 'category'),
(160, 32, 'product', 14, 'category'),
(159, 31, 'product', 13, 'category'),
(158, 30, 'product', 12, 'category'),
(157, 29, 'product', 11, 'category'),
(156, 28, 'product', 10, 'category'),
(155, 27, 'product', 9, 'category'),
(154, 26, 'product', 8, 'category'),
(153, 23, 'product', 7, 'category'),
(152, 22, 'product', 6, 'category'),
(151, 21, 'product', 5, 'category'),
(150, 20, 'product', 4, 'category'),
(149, 19, 'product', 3, 'category'),
(148, 18, 'product', 2, 'category'),
(147, 37, 'product', 1, 'category'),
(146, 81, 'product', 30, 'category'),
(145, 80, 'product', 29, 'category'),
(144, 79, 'product', 28, 'category'),
(143, 77, 'product', 27, 'category'),
(142, 76, 'product', 26, 'category'),
(141, 75, 'product', 25, 'category'),
(140, 74, 'product', 24, 'category'),
(139, 73, 'product', 23, 'category'),
(138, 72, 'product', 22, 'category'),
(137, 71, 'product', 21, 'category'),
(136, 70, 'product', 20, 'category'),
(135, 69, 'product', 19, 'category'),
(134, 68, 'product', 18, 'category'),
(133, 67, 'product', 17, 'category'),
(132, 66, 'product', 16, 'category'),
(131, 63, 'product', 15, 'category'),
(130, 62, 'product', 14, 'category'),
(129, 61, 'product', 13, 'category'),
(128, 60, 'product', 12, 'category'),
(127, 59, 'product', 11, 'category'),
(126, 58, 'product', 10, 'category'),
(125, 57, 'product', 9, 'category'),
(124, 56, 'product', 8, 'category'),
(123, 54, 'product', 7, 'category'),
(122, 53, 'product', 6, 'category'),
(121, 52, 'product', 5, 'category'),
(120, 51, 'product', 4, 'category'),
(119, 49, 'product', 3, 'category'),
(118, 48, 'product', 2, 'category'),
(117, 46, 'product', 1, 'category'),
(116, 109, 'product', 12, 'category'),
(115, 108, 'product', 11, 'category'),
(114, 106, 'product', 10, 'category'),
(113, 105, 'product', 9, 'category'),
(112, 104, 'product', 8, 'category'),
(111, 103, 'product', 7, 'category'),
(110, 102, 'product', 6, 'category'),
(109, 101, 'product', 5, 'category'),
(108, 100, 'product', 4, 'category'),
(107, 115, 'product', 3, 'category'),
(106, 114, 'product', 2, 'category'),
(105, 111, 'product', 1, 'category');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(128) NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `description`, `category_id`) VALUES
(2, 'Boucheron', '', 1),
(3, 'Cartier', '(ca. 1950)', 1),
(4, 'Josef Hoffman', 'Austria<BR>(1870 - 1956)<BR>Wiener werkstatte & Vienna Secession', 1),
(5, 'Van Cleef Arpels', '(ca. 1923)', 1),
(6, 'Van Cleef Arpels', '(ca. 1950)', 1),
(7, 'Wiener Werkstatte', '(1903 - 1932)', 1),
(8, 'Alphonse Mucha', '(1860 - 1939)<BR>Praque', 2),
(9, 'Archibald Knox', '(1864 - 1933)<BR>England<BR>"Manx Art & Craft"', 2),
(10, 'C Duguinne', '', 2),
(11, 'Siegel Mannequins', 'inspired by Cartier<BR>(ca. 1925)', 1),
(12, 'Floral Motivs', '', 2),
(13, 'Gaston Laffitte', '', 2),
(14, 'Georges Brunet', '(1847 - 1904)<BR>France', 2),
(15, 'Joseph Maria Olbrich', '(1867 - 1908)<BR>Oostenrijk<BR>"Wiener Secession"', 2),
(16, 'Luis Masriera', '(1872 - 1958)<BR>Barcelona', 2),
(17, 'Cherubs and Cupids', '', 2),
(18, 'Rene Lalique', '(1860 - 1945)<BR>Paris', 2),
(19, 'Sissy Stars', '(Elisabeth in Beieren)', 3),
(20, 'George Jensen', '(1866 - 1935)<BR>Denmark', 1),
(21, 'Cartier', '(ca. 1910)', 1),
(22, 'Dusausoy', '(ca. 1928)<BR>France', 1),
(23, 'Royal van Kempen & Begeer', '(ca. 1925)', 1),
(24, 'George Fouget', '(1862 - 1957)<BR>France', 1),
(25, 'W. Leither', '(ca. 1923)<BR>Germany', 1),
(26, 'Various Designers', '', 3);


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  password varchar(50) NOT NULL,
  salt varchar(50) NOT NULL,
  role varchar(50) NOT NULL,
  date_created datetime NOT NULL,
  PRIMARY KEY (id)
);

--
-- Dumping data for table `users`
--

INSERT INTO users (username, password, salt, role, date_created) VALUES 
('admin', SHA1('13bfb34'),'ce8d96d579d389e783f95b3772785783ea1a9854', 'administrator', NOW());
