CREATE TABLE ch_page (
       pid serial PRIMARY KEY NOT NULL,
       sysname varchar(255) NOT NULL,
       name varchar(255) NOT NULL,
       authorid int NOT NULL default 0,
       published smallint NOT NULL default 0,
       content Text  NULL,
       lang varchar(5) NULL,
       id_lang_reference int NULL,
       created timestamp  NULL,
       updated timestamp  NULL
);
