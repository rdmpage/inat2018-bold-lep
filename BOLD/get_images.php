<?php

// Given a TSV file where rows are records for DNA barcodes for the same taxa in the
// iNat data we generate data for testing and training

require_once('shared.php');

//----------------------------------------------------------------------------------------
function get($url, $format = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	if ($format != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: " . $format));	
	}
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------

$filename = "bold-inat.tsv";

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
			$obj = new stdclass;

			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}		
			
			// destination folder
			$dir = dirname(__FILE__) . '/images';
			
			$dir .= '/' . $obj->cat_id;
			if (!file_exists($dir))
			{
				$oldumask = umask(0); 
				mkdir($dir, 0777);
				umask($oldumask);
			}
			
			$image_dest = $dir . '/' . $obj->md5 . '.jpg';		
			
			// do we already have the image?	
			if (file_exists($image_dest))
			{
				// do nothing
			}
			else
			{			
				// do we have this on our external disk?
				$image_source = '/Volumes/LaCie/BOLD/images' . hash_to_path($obj->md5) . '/' . $obj->md5 . '.jpg';			
			
				if (file_exists($image_source))
				{
					// yes, so copy it
					copy($image_source, $image_dest);			
				}
				else
				{
					// nope, go fetch
					echo "Fetch " . $obj->url . "\n";
					$image = get($obj->url);	
				
					if ($image != '')
					{
						file_put_contents($image_dest, $image);
					}
					else
					{
						echo "No image for " . $obj->url . "\n";
					}
				}
			}
		}
	}	
	$row_count++;		
}	


?>
