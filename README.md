# iNaturalist 2018 challenge data tested using BOLD data for Lepidoptera

Using a model trained on a subset of the iNaturalist 2018 competition [dataset](https://github.com/visipedia/inat_comp).

## iNat 2018 model

For details on the model see https://github.com/rdmpage/inat_comp_2018. The model in that repository was trained on just the Lepidoptera images.

## iNat data

The training and validation datasets (`test2018.json` and `val2018.json`) contain information on the images and which taxa they represent. The species identifies where obfuscated, so we also need to process `categories.json` to get the actual names.

Run `to_sql.php` to convert these files to SQL. Create a SQLIte database using `schema.sql`, then add the SQL files to create a simple database of the iNat 2018 data.


## BOLD data
