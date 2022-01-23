<?php
/**
 * A PHP class to parse a CSV and upload the value
 * to MySQL database table.
 * 
 * @author     Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version    2.0
 */

require_once "classes/Uploader.php";


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

//Get the options. Print error message if no options.
$options = getopt('h:u:p:',array('file:','create_table','dry_run','help'));
if(sizeOf($options) == 0)  
    exit("Please retry with correct options.\n Execute \"php " . basename(__FILE__) . " --help\" for more information.\n") ;

$uploader = new Uploader($options); 
$uploader->processOptions();

//@todo -sanitizeOptions(),charset,PHPUnit tests
?>