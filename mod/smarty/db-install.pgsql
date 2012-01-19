CREATE TYPE "ch_smarty_plugintype" AS ENUM ('function','block','compiler','modifier','preFilter','postFilter','outputFilter');

CREATE TABLE "ch_smarty_plugins" (
       "id_module" int NULL,
       "name" VARCHAR(255) NOT NULL,
       "type" ch_smarty_plugintype NOT NULL,
       "method" VARCHAR(255) NOT NULL
);
CREATE INDEX ch_smarty_plugins_id_module_idx ON "ch_smarty_plugins" ("id_module");
CREATE INDEX ch_smarty_plugins_name_idx ON "ch_smarty_plugins" ("name");

CREATE TABLE "ch_smarty_override" (
       "id_module" INT NULL,
       "orig" VARCHAR(255) NOT NULL,
       "replace" VARCHAR(255) NOT NULL
);
CREATE INDEX ch_smarty_override_id_module_idx ON "ch_smarty_override" ("id_module");
CREATE INDEX ch_smarty_override_orig_idx ON "ch_smarty_override" ("orig");
