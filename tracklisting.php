<?php

class TrackListing
{
	public function __construct()
	{
		$this->generateListing();
	}

	public function generateListing()
	{
		$path = __DIR__.'/tracks';
		$iterator = new GlobIterator($path.'/*.mp3');
		$iterator->setFlags(FilesystemIterator::KEY_AS_FILENAME);
		foreach($iterator as $key => $fileinfo)
		{
			$tracks[] = 'tracks/'.$key;
		}
		return array_values(array_map("strval",$tracks));
	}
}

$listing = new TrackListing();
echo json_encode($listing->generateListing(), JSON_UNESCAPED_SLASHES);