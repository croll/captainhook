CREATE TABLE "ch_config" (
       "id_module" INT NOT NULL,
       "name" VARCHAR(255) NOT NULL,
       "value" TEXT NOT NULL,
       "id_user" INT NULL REFERENCES "ch_user" ON DELETE CASCADE,
				UNIQUE ("id_module", "name", "id_user")
);

CREATE INDEX "ch_config_id_module_idx" ON "ch_config" ("id_module");
CREATE INDEX "ch_config_name_idx" ON "ch_config" ("name");
CREATE INDEX "ch_config_id_user_idx" ON "ch_config" ("id_user");
