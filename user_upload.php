<?php
require "user_upload_functions.php";

$shortopts  = "";
$shortopts .= "u:";  
$shortopts .= "p:";
$shortopts .= "h:";


$longopts  = array(
    "file:",
    "create_table",
    "dry_run", 
    "help"        
);
$options = getopt($shortopts, $longopts);
//var_dump($options);
foreach(array_keys($options) as $key) 
{
    switch($key)
    {
        case "file":
            $file = $options['file'];
        break;
        case "create_table":
            $create_table = true;
        break;
        case "dry_run":
            $dry_run = true;
        break;
        case "u":
            $mysql_username = $options['u'];
        break;
        case "p":
            $mysql_password = $options['p'];
        break;
        case "h":
            $mysql_host = $options['h'];
        break; 
        case "help":
            print_help_message();
        break;
    }
}

?>