<?php
set_time_limit(0);
$base_path = "C:\\xampp\\htdocs\\NSE-Sync";
$server_path = "http://rupeemaxx.com/app1/data";
require_once("helper.php");

$str = file_get_contents("http://rupeemaxx.com/app1/home/get_sync_data");
//echo $str;
$data = json_decode($str);
$total_files_down = count($data);
$files_exists = 0;
$files_not_exists = 0;

foreach($data as $file) {
	$ffname = str_replace("/home/rupeemax/public_html/app1/data", "", $file->filename);
	$point = strrpos($ffname, "/") + 1 ;
	$fdir = substr($ffname, 0, $point);
	$fname = substr($ffname, $point);
	//echo $fname." -> ".$fdir." -> ".$ffname."<br>";
	// file sizes
	$sfsize = $file->size;
	
	if(file_exists($base_path.$ffname) && $sfsize == filesize($base_path.$ffname)) {
		$files_exists = $files_exists + 1;
		$lfsize = filesize($base_path.$ffname);
	} else {
		//echo $fname." -> ".$fdir." -> ".$ffname."<br>";
		$files_not_exists = $files_not_exists + 1;
		create_dir_structure($base_path, $fdir);
		$fileR = fopen($base_path."/".$ffname, "w");
		$arr1 = downloadFile($server_path.$ffname, $fileR);
		fclose($fileR);
	}
	//create_dir_structure($base_path, "/idx_opt/2018/06/");
}
echo "total_files_down: " . $total_files_down."<br>";
echo "files_exists: " . $files_exists."<br>";
echo "files_not_exists: " . $files_not_exists."<br>";
?>