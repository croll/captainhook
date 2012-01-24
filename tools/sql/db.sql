DROP TABLE IF EXISTS "ch_module";
CREATE TABLE "ch_module" (
  "mid" serial NOT NULL,
  "name" varchar(50) NOT NULL,
  "active" smallint DEFAULT 0,
  PRIMARY KEY ("mid")
);
CREATE INDEX ch_module_name_idx ON "ch_module" ("name");


DROP TABLE IF EXISTS "ch_hook";
CREATE TABLE "ch_hook" (
  "hid" serial NOT NULL,
  "name" varchar(255) NOT NULL,
  "mid" int DEFAULT NULL,
  "callback" varchar(255) NOT NULL,
  "userdata" varchar(255) NULL,
  "position" int NOT NULL,
  PRIMARY KEY ("hid")
);
CREATE INDEX ch_hook_mid_idx ON "ch_hook" ("mid");
