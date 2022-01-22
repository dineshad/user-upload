<?php
/**
 * A PHP class to parse a CSV and upload the value
 * to MySQL database table.
 * 
 * @author     Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version    1.0
 */

//require "user_upload_functions.php";
require "classes/Uploader.php";


// //-----------------
// // All of the command line classes are in the Garden\Cli namespace.
// use Garden\Cli\Cli;
// // Require composer's autoloader.
// require_once 'vendor/autoload.php';
// // Define the cli options.
// $cli = new Cli();

// $cli->description('Options for user_upload.php are mentioned below.')
//     ->opt('host:h', 'The database host.', true,'string')
//     ->opt('username:u', 'Username for the database', true, 'string')    
//     ->opt('password:p', 'Password to use when connecting to server.', true, 'string')
//     ->opt('create_table', 'Create user table') 
//     ->opt('dry_run', 'Execute the script without inserting to database') 
//     ->opt('file', 'The CSV file with user data.') ;
// // Parse and return cli args.
// $args = $cli->parse($argv, true);
//----

define("DATABASE",     "user_details");
define("TABLE",     "user");
define("IMPORT_FOLDER",     "import");

//Get the options. Print error message if no options.
$options = getopt('h:u:p:',array('file:','create_table','dry_run','help'));
//var_dump($options);
if(sizeOf($options) == 0)  
    exit("Please retry with correct options.\n Execute \"php " . basename(__FILE__) . " --help\" for more information.\n") ;

$uploader = new Uploader($options);    
//$uploader->sanitizeOptions();
$uploader->processOptions();



//$db_connection = new mysqli($options['h'],$options['u'],$options['p'],DATABASE);
// if (isset($valid_options['db_flag']))
//     $db_conn = new mysqli($valid_options['h'],$valid_options['u'],$valid_options['p'],DATABASE) ;



//The following loop will be executed only if the user has added options correctly.
// foreach ($options as $key => $value){
//     switch($key){
//         case "help":
//             $user_uploader->displayHelpMessage();
//         break;
//         case "create_table":
//             $user_uploader->createTable($db_connection,USER_TABLE);
//         break;
//         case "dry_run":
//         // break;
//         // case "":
//         // break;
//     }
// }




// if --file option is not set
// if (!isset($options['file'])){
//     if (isset($options['create_table'])){
//         if ( isset($options['h']) && isset($options['u']) && isset($options['p'])) {
//             create_users_table($options['h'],$options['u'],$options['p']);
//         } 
//         else { 
//             echo "Please retry with username,password and host for the database\n\n" ;
//         }
//     }
//     if (isset($options['help'])){
//         print_help_message();
//     } 
//     //If dry_run is added as option, but file is not specified.
//     if(array_key_exists('dry_run', $options) && !array_key_exists('file', $options)){
//         echo "Please retry with --file option\n";
//     }
// }
// // if --file option is  set
// else{
//     $filename = "import/" . $options['file'];
//     if (file_exists($filename)){       
//         $users_arr = get_csv_to_array($filename);
//         if (sizeof($users_arr) > 0){
//             //removing header values
//             array_shift($users_arr);
//             $users_valid_email_arr = [];
//             $users_invalid_email_arr = [];
//             foreach($users_arr as &$user){
//                 $user[0] = ucfirst(strtolower(($user[0]))) ;
//                 $user[1] = ucfirst(strtolower(($user[1]))) ;
//                 $user[2] = strtolower($user[2]) ;   
//                 $user[2] = filter_var($user[2], FILTER_SANITIZE_EMAIL) ;           
//                 if (filter_var(trim($user[2]), FILTER_VALIDATE_EMAIL)) {  
//                     array_push($users_valid_email_arr,$user); 
//                 }
//                 else{                   
//                     array_push($users_invalid_email_arr,$user);
//                 }
//             }
//             //If dry_run option is not specified(If data is inserted to db)
//             if (!array_key_exists('dry_run', $options)){
//                 if ( isset($options['h']) && isset($options['u']) && isset($options['p'])) {
//                     insert_to_db($options['h'],$options['u'],$options['p'],$users_valid_email_arr);
//                 }
//                 else { 
//                     echo "Please retry with username,password and host for the database\n\n" ;
//                 }
//                 if (sizeof($users_invalid_email_arr) > 0)
//                 {
//                     echo "\nFollowing users were not inserted to user table because their emails were invalid.\n";
//                     foreach($users_invalid_email_arr as $value) {                    
//                         echo implode(",",$value) . "\n\n";
//                     }
//                 }
//             } 
//             else{
//                 $msg_valid_email = "Users with valid emails:";
//                 $msg_invalid_email = "\nUsers with invalid emails:";
//                 print_array_with_message($msg_valid_email,$users_valid_email_arr);
//                 print_array_with_message($msg_invalid_email,$users_invalid_email_arr);
//             }
//         }
//         else {
//             echo "No user data in the file\n\n";
//         }
//     }    
//     else{
//         echo "The CSV file doesn not exist in import folder.\n\n" ;
//     }
// }
?>