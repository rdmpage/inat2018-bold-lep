<?php

// Given a TSV file where rows are records for DNA barcodes for the same taxa in the
// iNat data we dump all the data in the form for iNat training/validation

require_once('shared.php');

//----------------------------------------------------------------------------------------

$filename = "bold-inat.tsv";

$obj = new stdclass;
$obj->info = new stdclass;
$obj->info->description = "BOLD";
$obj->images = array();
$obj->annotations = array();
$obj->categories = array();

// image ids are integer counters to match iNat format
$image_id = 0;

$headings = array();

$row_count = 0;

$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$line = trim(fgets($file_handle));
		
	$row = explode("\t",$line);
	
	$go = is_array($row) && count($row) > 1;
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$data = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$data->{$headings[$k]} = $v;
				}
			}		
			//print_r($data);	
			
			$image = new stdclass;
			$annotation = new stdclass;
			$category = new stdclass;
			
			$image_id++;

			foreach ($data as $k => $v)
			{
				switch ($k)
				{
					case 'md5':
						$image->id = $image_id;		
						$image->file_name = 'images/' . $data->cat_id . '/' . $data->md5 . '.jpg';
						
					
						$info = getimagesize($image->file_name);
						
						// bale out if we have a problem with an image
						if (!$info)
						{
							print_r($data);
							echo $image->file_name . "\n";
							exit();
						}
						
						$image->width  = $info[0];
						$image->height = $info[1];	
					
						$annotation->id = $image_id;
						$annotation->image_id = $image_id;									
						break;
					
						// other info on image
					case 'processid':
					case 'license':
					case 'url':
					case 'name':
						$image->{$k} = $v;
						break;
					
					case 'cat_name':
						// taxon name
						$category->name = $v;
						break;
					
						// Catgory id must be integer
					case 'cat_id':
						$category->id = (Integer)$v;					
						$annotation->category_id = (Integer)$v;
						break;

					default:
						break;	
				}					
			}
			
			$obj->images[] = $image;
			$obj->annotations[] = $annotation;	
			$categories[$category->id] = $category;
			
		}
	}	
	$row_count++;		
}	


$obj->categories = array_values($categories);

echo json_encode($obj);
echo "\n";

?>
