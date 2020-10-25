# phpMyAdmin MySQL-Dump
# http://phpwizard.net/phpMyAdmin/
#
# Serveur: sql Base de données: rezophils
# --------------------------------------------------------

#
# Structure de la table 'xoops_webmail'
#

CREATE TABLE xoops_webmail (
    wbmid    INT(5)             NOT NULL AUTO_INCREMENT,
    uid      INT(5) DEFAULT '0' NOT NULL,
    login    VARCHAR(50)        NOT NULL,
    password VARCHAR(15)        NOT NULL,
    serveur  VARCHAR(100)       NOT NULL,
    type     VARCHAR(10)        NOT NULL,
    compte   VARCHAR(50)        NOT NULL,
    PRIMARY KEY (wbmid),
    KEY uid (uid)
);
