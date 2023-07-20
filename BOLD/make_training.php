<?php

// subset the BOLD data into training and validation

error_reporting(E_ALL);

ini_set('memory_limit', '-1');

// load all the data
$filename = 'all.json';
$json = file_get_contents($filename);
$obj = json_decode($json);

// Get images by category
$categories = array();

foreach ($obj->annotations as $annotation)
{
	if (!isset($categories[$annotation->category_id]))
	{
		$categories[$annotation->category_id] = array();
	}
	$categories[$annotation->category_id][] = $annotation->image_id;
}

print_r($categories);

// training
$training = new $obj;
$training->info = $obj->info;
$training->images = array();
if (isset($obj->licenses))
{
	$training->licenses = $obj->licenses;
}
$training->annotations = array();

// validation
$validation = new $obj;
$validation->info = $obj->info;
$validation->images = array();
if (isset($obj->licenses))
{
	$validation->licenses = $obj->licenses;
}
$validation->annotations = array();

$training_set = array();
$validation_set = array();

foreach ($obj->categories as $category)
{
	if (count($categories[$category->id]) >= 9)
	{
		$validation_keys = array_rand($categories[$category->id], 3);
		
		foreach ($categories[$category->id] as $k => $image_id)
		{
			if (in_array($k, $validation_keys))
			{
				$validation_set[] = $image_id;
			}
			else
			{
				$training_set[] = $image_id;
			}
		}
		
		$training->categories[] = $category;	
		$validation->categories[] = $category;	
	}
}

//print_r($training_set);
//print_r($validation_set);

foreach ($obj->annotations as $annotation)
{
	if (in_array($annotation->image_id, $training_set))
	{
		$training->annotations[] = $annotation;
	}
	if (in_array($annotation->image_id, $validation_set))
	{
		$validation->annotations[] = $annotation;
	}
}

foreach ($obj->images as $image)
{
	if (in_array($image->id, $training_set))
	{
		$training->images[] = $image;
	}
	if (in_array($image->id, $validation_set))
	{
		$validation->images[] = $image;
	}
}

//print_r($training);
//print_r($validation);

file_put_contents("train.json", json_encode($training));
file_put_contents("val.json", json_encode($validation));


?>
