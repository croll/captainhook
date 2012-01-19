CREATE TABLE "ch_regroute" (
       "id_module" INT NULL,
       "regexp" VARCHAR(255) NOT NULL,
       "hook" VARCHAR(255) NOT NULL,
       "flags" INT NOT NULL
);

CREATE INDEX "ch_regroute_id_module_idx" ON "ch_regroute" ("id_module");
