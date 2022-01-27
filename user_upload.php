<?php
/**
 * A PHP script to parse a CSV and upload the value
 * to MySQL database table.
 * 
 * @author     Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version    3.0
 */

require_once "classes/Uploader.php";
require_once "classes/File.php";
require_once "classes/User.php";


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

define("DATABASE", "user_details");

//Try get the options.If no options, exit the script.
$options = getopt('h:u:p:',array('file:','create_table','dry_run','help'));
if (sizeOf($options) == 0)
  exit("Retry with  options.\n Execute \"php " . basename(__FILE__) . " --help\" for more information.\n") ;
  
//If -h,-u,-p are set, try connect to the database. If it fails,catch exception, exit the script.
$conn = false ;
if ( array_key_exists('h',$options) && array_key_exists('u',$options) && array_key_exists('p',$options)){
  try{
    $conn = new mysqli($options['h'],$options['u'],$options['p'],DATABASE) ;
    if ($conn->connect_error) 
        throw new Exception($conn->connect_error);
  }
  catch (Exception $e){
    exit($e->getMessage() . "\n");
  } 
} 

//If --file is set, try read the data into an array.If it fails,catch the exception, exit the script.
if ( array_key_exists('file',$options) ){
  $file = new File($options['file']);
  try{
    if ( $file->is_file_exist() && $file->is_right_type("csv"))
      $data_array = $file->get_data_into_array();
    else
      throw new Exception("File does not exist or incorrect type");
  }
  catch(Exception $e){
    exit($e->getMessage() . "\n");
  } 
  //Try  creating list of user objects from the data array populated above.
  $user_obj_list = [];
  try {
    array_shift($data_array); // exclude the header row.
    if ( sizeof($data_array) > 0 ) {
      foreach($data_array as $value){
        $user = new User($value[0],$value[1],$value[2]);
        array_push($user_obj_list,$user);
      }
    }
    else
      throw new Exception("No user data in the file.\n") ;
  }
  catch(Exception $e){
    exit($e->getMessage() . "\n");
  } 
}
//Determine the action  and try execute the action.
try {
  
  $action = Uploader::get_action($options);
  //echo 'action:' . $action ;
  if ($action != NULL )
  {
    switch($action){
      case 'help':
        Uploader::display_help_message();
      break;
      case 'dry_run':
        Uploader::dry_run($user_obj_list);
      break;
      case 'create_table':
        Uploader::create_table($conn);
      break;
      case 'upload':
        Uploader::upload($conn,$user_obj_list);
      break;
    }
  }
  else
    throw new Exception("Retry with correct options.\n Execute \"php " . basename(__FILE__) . " --help\" for more information.\n");
}
catch(Exception $e){
  exit($e->getMessage() . "\n");
} 

if ($conn)
    $conn->close();
    
//@todo -sanitizeOptions(),autoload,charset,error handler,PHPUnit tests

?>