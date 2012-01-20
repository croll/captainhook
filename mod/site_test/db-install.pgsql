CREATE TYPE "ch_sitetest_person_gender_typ" AS ENUM('male','female','other');
CREATE TABLE "ch_sitetest_person" (
       "id" serial NOT NULL,
       "firstname" VARCHAR(255) NOT NULL,
       "lastname" VARCHAR(255) NOT NULL,
       "gender" ch_sitetest_person_gender_typ NOT NULL,
       PRIMARY KEY ("id")
);
