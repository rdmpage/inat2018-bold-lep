<?php

// Export a subset of data for iNat training, in this case just export Lepidoptera

$pdo = new PDO('sqlite:inat.db');

//----------------------------------------------------------------------------------------
function do_query($sql)
{
	global $pdo;
	
	$stmt = $pdo->query($sql);

	$data = array();

	while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

		$item = new stdclass;
		
		$keys = array_keys($row);
	
		foreach ($keys as $k)
		{
			if ($row[$k] != '')
			{
				$item->{$k} = $row[$k];
			}
		}
	
		$data[] = $item;
	
	
	}
	
	return $data;	
}

//----------------------------------------------------------------------------------------

$licenses_json = '[{"url": "http://creativecommons.org/licenses/by-nc-nd/4.0/", "id": 1, "name": "Attribution-NonCommercial-NoDerivatives License"}, {"url": "http://creativecommons.org/licenses/by-nc-sa/4.0/", "id": 2, "name": "Attribution-NonCommercial-ShareAlike License"}, {"url": "http://creativecommons.org/licenses/by-nc/4.0/", "id": 3, "name": "Attribution-NonCommercial License"}, {"url": "http://creativecommons.org/licenses/by-nd/4.0/", "id": 4, "name": "Attribution-NoDerivatives License"}, {"url": "http://creativecommons.org/licenses/by-sa/4.0/", "id": 5, "name": "Attribution-ShareAlike License"}, {"url": "http://creativecommons.org/licenses/by/4.0/", "id": 6, "name": "Attribution License"}, {"url": "http://creativecommons.org/publicdomain/zero/1.0/", "id": 7, "name": "Public Domain Dedication"}, {"url": "http://en.wikipedia.org/wiki/Copyright", "id": 8, "name": "No known copyright restrictions"}]';

//----------------------------------------------------------------------------------------

// {"info": {"description": "The 2018 FGVC^5 iNaturalist Competition dataset.", "url": "https://github.com/visipedia/inat_comp", "version": "1.0", "year": 2018, "contributor": "iNaturalist Competition group", "date_created": "2018-02-01 17:26:07.239765"}, "images"[]}

// If not using the original image dump from the iNat 2018 challenge dataset we may 
// be rewriting the image file names, such as to organise them by folders based on
// category id (species)

$rewrite_image_name = true;

// 0 = training
// 1 = validation
// 2 = test (same as validation but without identities)

for ($role = 0; $role <= 2; $role++)
{

	$obj = new stdclass;
	$obj->info = new stdclass;
	$obj->info->description = "iNat";
	$obj->images = array();
	$obj->licenses = json_decode($licenses_json);

	// training and validation datasets know the identify of the images
	if ($role != 2)
	{
		$obj->annotations = array();
		$obj->categories = array();
	}

	// images

	$filter = $role;
	if ($role == 2) { $filter = 1; }

	$sql = 'SELECT image.id, image.license, image.file_name, image.rights_holder, image.height, image.width,
	annotation.id as anno_id, annotation.image_id AS anno_image_id, annotation.category_id as anno_category_id, category.name AS cat_name, category.id as cat_id
	FROM image 
	INNER JOIN annotation ON image.id = annotation.image_id 
	INNER JOIN category ON annotation.category_id = category.id 
	WHERE image.role= ' . $filter . ' AND category.`order` = "Lepidoptera";';

	$categories = array();

	$data = do_query($sql);

	foreach ($data as $row)
	{
		$image = new stdclass;
		$annotation = new stdclass;
		$category = new stdclass;
		
		foreach ($row as $k => $v)
		{
			switch ($k)
			{
				case 'id':
				case 'license':
				case 'height':
				case 'width':
					$image->{$k} = (Integer)$v;
					break;

				case 'file_name':
					if ($rewrite_image_name)
					{
						$image->{$k} = 'images/' . $row->cat_id . '/' . $row->id . '.jpg';
					}
					else
					{
						$image->{$k} = $v;
					}
					break;
				
				case 'rights_holder':
				
					break;
				
				
				case 'anno_id':
				case 'anno_image_id':
				case 'anno_category_id':
					if ($role != 2)
					{
						$k = str_replace('anno_', '', $k);
						$annotation->{$k} = (Integer)$v;
					}
					break;
				
				case 'cat_id':
				case 'cat_name':
					if ($role != 2)
					{
						$k = str_replace('cat_', '', $k);
						$category->{$k} = $v;
					}
					break;
		
				default:
					break;
			}
		
		}
	
		$obj->images[] = $image;
	
		if ($role != 2)
		{
			$obj->annotations[] = $annotation;	
			$categories[$category->id] = $category;
		}

	}

	if ($role != 2)
	{
		$obj->categories = array_values($categories);
	}
	
	$filename = '';
	switch ($role)
	{
		case 0:
			$filename = 'train.json';
			break;

		case 1:
			$filename = 'val.json';
			break;

		case 2:
			$filename = 'test.json';
			break;
	}

	file_put_contents($filename, json_encode($obj));
}


?>
