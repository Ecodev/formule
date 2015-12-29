#
# Table structure for table 'tx_formule_domain_model_sentmessage'
#
CREATE TABLE tx_formule_domain_model_sentmessage (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	sender varchar(255) DEFAULT '' NOT NULL,
	recipient varchar(255) DEFAULT '' NOT NULL,
	subject varchar(255) DEFAULT '' NOT NULL,
	body varchar(255) DEFAULT '' NOT NULL,
	attachment varchar(255) DEFAULT '' NOT NULL,
	context varchar(255) DEFAULT '' NOT NULL,
	was_opened varchar(255) DEFAULT '' NOT NULL,
	sent_time varchar(255) DEFAULT '' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);
