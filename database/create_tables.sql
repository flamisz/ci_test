CREATE TABLE users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  uid varchar(255) NOT NULL,
  nickname varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  token varchar(255) NOT NULL,
  token_secret varchar(255) NOT NULL,
  avatar varchar(255) NOT NULL,
  remember_digest varchar(255) NULL DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_nickname (nickname),
  UNIQUE KEY users_email (email),
  UNIQUE KEY users_uid (uid)
);
