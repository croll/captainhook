CREATE TABLE ch_page (
       pid serial PRIMARY KEY NOT NULL,
       sysname varchar(255) NOT NULL,
       name varchar(255) NOT NULL,
       authorid int NOT NULL,
       published smallint NOT NULL default 0,
       content Text  NULL,
       created timestamp  NULL,
       updated timestamp  NULL
);
