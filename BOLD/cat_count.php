<?php

// How many images are there in each category?

error_reporting(E_ALL);

ini_set('memory_limit', '-1');

$filename = 'all.json';

$json = file_get_contents($filename);

$obj = json_decode($json);

$categories = array();

foreach ($obj->annotations as $annotation)
{
	if (!isset($categories[$annotation->category_id]))
	{
		$categories[$annotation->category_id] = 0;
	}
	$categories[$annotation->category_id]++;
}

print_r($categories);


?>
