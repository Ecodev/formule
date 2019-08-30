#
# Table structure for table 'tx_formule_domain_model_sentmessage'
#
CREATE TABLE tx_formule_domain_model_sentmessage (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	sender varchar(255) DEFAULT '' NOT NULL,
	recipient varchar(255) DEFAULT '' NOT NULL,
	recipient_cc varchar(255) DEFAULT '' NOT NULL,
	recipient_bcc varchar(255) DEFAULT '' NOT NULL,
	subject varchar(255) DEFAULT '' NOT NULL,
	body text,
	attachment varchar(255) DEFAULT '' NOT NULL,
	context varchar(255) DEFAULT '' NOT NULL,
	sent_time varchar(255) DEFAULT '' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,

	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);
