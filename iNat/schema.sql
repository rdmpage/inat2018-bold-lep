
CREATE TABLE "annotation" (
    id INTEGER
  , image_id INTEGER
  , category_id INTEGER
);

CREATE INDEX anno_id ON annotation(id ASC);

CREATE INDEX annot_cat_id ON annotation(category_id ASC);

CREATE INDEX anno_image_id ON annotation(image_id ASC);

CREATE TABLE category (
    id INTEGER PRIMARY KEY
  , name TEXT
  , supercategory TEXT
  , kingdom TEXT
  , phylum TEXT
  , class TEXT
  , "order" TEXT
  , family TEXT
  , genus TEXT
);

CREATE INDEX cat_order ON category("order" ASC);

CREATE INDEX cat_id ON category(id ASC);

CREATE TABLE "category_obfuscated" (
    id INTEGER PRIMARY KEY
  , name INTEGER
  , supercategory TEXT
  , kingdom TEXT
  , phylum TEXT
  , class TEXT
  , "order" TEXT
  , family TEXT
  , genus TEXT
);

CREATE INDEX id ON "category_obfuscated"(id ASC);

CREATE INDEX "" ON "category_obfuscated"("order" ASC);

CREATE TABLE "image" (
    id INTEGER
  , license INTEGER
  , file_name TEXT
  , rights_holder TEXT
  , height INTEGER
  , width INTEGER
  , role INTEGER
);

CREATE INDEX "role" ON image(role ASC);

CREATE INDEX image_id ON image(id ASC);
