<?php

// subset the BOLD data to include one or more categories

error_reporting(E_ALL);

ini_set('memory_limit', '-1');

// load all the data
$filename = 'all.json';
$json = file_get_contents($filename);
$obj = json_decode($json);

$target = array(1778);

$image_ids = array();


// subset
$subset = new $obj;
$subset->info = $obj->info;
$subset->images = array();
if (isset($obj->licenses))
{
	$subset->licenses = $obj->licenses;
}
$subset->annotations = array();

foreach ($obj->categories as $category)
{
	
	if (in_array($category->id, $target))
	{
		$subset->categories[] = $category;	
	}
}

foreach ($obj->annotations as $annotation)
{
	if (in_array($annotation->category_id, $target))
	{
		$subset->annotations[] = $annotation;	
		$image_ids[] = $annotation->image_id;
	}
}

foreach ($obj->images as $image)
{
	if (in_array($image->id, $image_ids))
	{
		$subset->images[] = $image;
	}
}


//print_r($subset);
echo json_encode($subset);

?>
