CREATE TABLE IF NOT EXISTS `sotbit_b2bcabinet_draft` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `DATE_CREATE` datetime DEFAULT NULL,
  `USER_ID` int(11) NOT NULL,
  `SITE_ID` char(2) NOT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `sotbit_b2bcabinet_draft_product` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DRAFT_ID` int(11) NOT NULL,
  `PRODUCT_ID` int(11) NOT NULL,
  `QUANTITY` float NOT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `sotbit_b2bcabinet_order_template` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `DATE_CREATE` datetime DEFAULT NULL,
  `USER_ID` int(11) NOT NULL,
  `SITE_ID` char(2) NOT NULL,
  `SAVED` varchar(1) NOT NULL default 'N',
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `sotbit_b2bcabinet_order_template_product` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ORDER_TEMPLATE_ID` int(11) NOT NULL,
  `PRODUCT_ID` int(11) NOT NULL,
  `QUANTITY` float NOT NULL,
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `sotbit_b2bcabinet_order_template_company` (
  `ORDER_TEMPLATE_ID` int(11) NOT NULL,
  `COMPANY_ID` int(11) NOT NULL,
  PRIMARY KEY (`ORDER_TEMPLATE_ID`, `COMPANY_ID`)
);
CREATE TABLE IF NOT EXISTS `sotbit_b2bcabinet_calendar_events` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `CODE` varchar(255) NOT NULL,
    `VALUES` varchar(255) NOT NULL,
    `DATE` datetime DEFAULT NULL,
    `USER_ID` int(11) NOT NULL,
    PRIMARY KEY (`ID`)
);