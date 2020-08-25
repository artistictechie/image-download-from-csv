<?php

$csvFile = "imageurls.csv";
$downloadFolder = getcwd() . "/images/";
$csvDelimiter = ",";
$fileNameColumn = "Name";
$urlColumn = "Url";
$columnArr = [];
$fileHandle = fopen($csvFile, 'r');

function  sanitizeFileName($name){

    // Remove anything which isn't a word, whitespace, number
    // or any of the following caracters -_~,;[]().
    // If you don't need to handle multi-byte characters
    // you can use preg_replace rather than mb_ereg_replace
    $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $name);
    // Remove any runs of periods
    $name = mb_ereg_replace("([\.]{2,})", '', $name);
    // Replace all whitespace to hyphen
    $name = mb_ereg_replace(" ", '-', $name);
    
    return $name;
}

function downloadFile($file){
    global $downloadFolder;
    

    $url = $file['url'];

    if (filter_var($url, FILTER_VALIDATE_URL)){

        $filename = $file['name'] . "." . pathinfo($url, PATHINFO_EXTENSION);
        
        if (!file_exists($filename)){
            
            $imageContent = file_get_contents($url);
            
            if(!$imageContent){
                echo "Error: Image not able to download\n";
            }
            else{
                $fileWithPath = $downloadFolder . $filename; 
                $size = file_put_contents($fileWithPath, $imageContent);
                if($size=== FALSE){
                    echo "Error: Permission issue, Not able to write file on disk\n";
                }
                else{
                    echo "Downloaded : " . $filename . "  (" . round(($size/1024)) . "kb) \n";
                    $d = $d + 1;
                }  
            }
            
        }
    }
}


if ($fileHandle !== FALSE) {
    // Get Headers
    if(!($columnArr = fgetcsv($fileHandle, 0, $csvDelimiter))){
        echo "Error :  Blank CSV";
        die();
    }
    while ($row = fgetcsv($fileHandle, 0, $csvDelimiter)) {
        $data = []; //reset array

        foreach($columnArr as $key => $column){
            if($fileNameColumn == $column){
                $data["name"] = sanitizeFileName($row[$key]);
            } elseif($urlColumn == $column) {
                $data["url"] = $row[$key];
            }
        }

        downloadFile($data);
    }
	fclose($fileHandle);
}
else
{
    echo "CSV File not Found";
    die();
}