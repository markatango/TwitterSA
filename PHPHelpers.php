<?php
// PHP Helpers

function test_print($item, $key){
    echo "$key holds $item" . "<br/>\n";
}

function clean($string) {
   //$string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}

function process_MySQL_script($link, $filename){
//From stackoverflow.com user 'genesis' Oct 20,2011
//Modified for mysqli
    if ($file = file_get_contents($filename)){
        foreach(explode(";", $file) as $query){
            $query = trim($query);
            if (!empty($query) && $query != ";") {
                mysqli_query($link,$query);
            }
        }
    }
}

function safe_query($link, $query) {
	if (mysqli_query($link, $query) === FALSE) {
		die("Cannot '"  . $query . "'" . "because: " . mysqli_error($link) . "<br/>\n");
	}
}