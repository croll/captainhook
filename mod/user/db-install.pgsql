CREATE TABLE "ch_user" (
                           "uid" serial NOT NULL,
                           "full_name" varchar(255) NOT NULL,
                           "login" varchar(32) NOT NULL,
                           "pass" varchar(32) DEFAULT NULL,
                           "email" varchar(255) DEFAULT NULL,
                           "hash" varchar(64) DEFAULT NULL,
                           "status" smallint NOT NULL DEFAULT 1,
                           "created" timestamp NULL DEFAULT NULL,
                           "updated" timestamp NULL DEFAULT NULL,
                           "last_connexion" timestamp NULL DEFAULT NULL,
                           PRIMARY KEY ("uid")
);
    
INSERT INTO "ch_user" ("uid", "full_name", "login", "pass", "status", "created") VALUES (1,'The Admin', 'admin', MD5('admin'), 1, now());
    
CREATE TABLE "ch_group" (
                           "gid" serial NOT NULL,
                           "name" varchar(255) NOT NULL,
                           "status" smallint NOT NULL DEFAULT 1,
                           PRIMARY KEY ("gid")
);
    
INSERT INTO "ch_group" VALUES (1,'Admin',1);
INSERT INTO "ch_group" VALUES (2,'Registered',1);
INSERT INTO "ch_group" VALUES (3,'Anonymous',1);
ALTER SEQUENCE ch_group_gid_seq RESTART WITH 4; 
    
CREATE TABLE "ch_user_group" (
                           "ugid" serial NOT NULL,
                           "uid" int NOT NULL,
                           "gid" int DEFAULT NULL,
                           PRIMARY KEY ("ugid")
);
CREATE INDEX "ch_user_group_uid_idx" ON "ch_user_group" ("uid");
CREATE INDEX "ch_user_group_gid_idx" ON "ch_user_group" ("gid");
INSERT INTO "ch_user_group" ("uid", "gid") VALUES (1, 1);
ALTER SEQUENCE ch_user_group_ugid_seq RESTART WITH 2; 

CREATE TABLE "ch_right" (
                           "rid" serial NOT NULL,
                           "name" varchar(50) NOT NULL,
                           "description" varchar(100) DEFAULT NULL,
                           PRIMARY KEY ("rid")
);
CREATE INDEX "ch_right_name_idx" ON "ch_right" ("name");

INSERT INTO "ch_right" ("rid", "name", "description") VALUES (1,'View rights','Allow user to see rights in admin panel.');
INSERT INTO "ch_right" ("rid", "name", "description") VALUES (2,'Manage rights','User can add/edit/delete rights.');
ALTER SEQUENCE ch_right_rid_seq RESTART WITH 3; 
    
CREATE TABLE "ch_group_right" (
                           "grid" serial NOT NULL,
                           "gid" int NOT NULL,
                           "rid" int DEFAULT NULL,
                           PRIMARY KEY ("grid")
);
CREATE INDEX "ch_group_right_gid_idx" ON "ch_group_right" ("gid");
CREATE INDEX "ch_group_right_rid_idx" ON "ch_group_right" ("rid");

    
INSERT INTO "ch_group_right" ("gid", "rid") VALUES (1, 1);
INSERT INTO "ch_group_right" ("gid", "rid") VALUES (1, 2);
ALTER SEQUENCE ch_group_right_grid_seq RESTART WITH 3; 
