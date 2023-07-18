<?php

// Convert iNat 2018 JSON files into SQL for ease of querying

error_reporting(E_ALL);

ini_set('memory_limit', '-1');


$filenames = array('train2018.json', 'val2018.json');

foreach ($filenames as $filename)
{

	$role = 0; // training
	if (preg_match('/val/', $filename))
	{
		$role = 1; // validation
	}

	$json = file_get_contents($filename);

	$obj = json_decode($json);

	foreach ($obj->images as $image)
	{
		/*
		"images": [{
		"license": 3,
		"file_name": "train_val2018\/Plantae\/7477\/3b60c9486db1d2ee875f11a669fbde4a.jpg",
		"rights_holder": "Jonathan Carpenter",
		"height": 600,
		"width": 800,
		"id": 1
		*/

		$keys = array();
		$values = array();

		foreach ($image as $k => $v)
		{
			$keys[] = $k;
			$values[] = '"' . str_replace('"', '""', $v) . '"';
		}

		// flag as training data
		$keys[] = 'role';
		$values[] = $role;

		echo 'REPLACE INTO image(' . join(',', $keys) . ') VALUES (' . join(',', $values) . ');' . "\n";
	}

	// output annotations
	foreach ($obj->annotations as $annotation)
	{
		// {"image_id": 440920, "category_id": 1566, "id": 440920}	

		$keys = array();
		$values = array();

		foreach ($annotation as $k => $v)
		{
			$keys[] = $k;
			$values[] = '"' . $v . '"';
		}

		echo 'REPLACE INTO annotation(' . join(',', $keys) . ') VALUES (' . join(',', $values) . ');' . "\n";
	}

	// output (obfuscated) categories
	foreach ($obj->categories as $category)
	{
		/*
	"kingdom": "TBCSEQ", "name": "8133", "family": "WBFNIJ", "supercategory": "Plantae", "class": "YIZOWL", "id": 8133, "phylum": "UPQJHC", "genus": "JRIDJA", "order": "QHFAVB"	*/
	
		$keys = array();
		$values = array();
	
		foreach ($category as $k => $v)
		{
			$keys[] = "`" . $k . "`";
			$values[] = '"' . $v . '"';
		}
	
		echo 'REPLACE INTO category_obfuscated(' . join(',', $keys) . ') VALUES (' . join(',', $values) . ');' . "\n";
	
	}
}

// output revealed categories

$filename = 'categories.json';

$json = file_get_contents($filename);

$obj = json_decode($json);

// output categories
foreach ($obj as $category)
{
	/*
"kingdom": "TBCSEQ", "name": "8133", "family": "WBFNIJ", "supercategory": "Plantae", "class": "YIZOWL", "id": 8133, "phylum": "UPQJHC", "genus": "JRIDJA", "order": "QHFAVB"	*/

	$keys = array();
	$values = array();

	foreach ($category as $k => $v)
	{
		$keys[] = "`" . $k . "`";
		$values[] = '"' . $v . '"';
	}

	echo 'REPLACE INTO category(' . join(',', $keys) . ') VALUES (' . join(',', $values) . ');' . "\n";
}

?>
