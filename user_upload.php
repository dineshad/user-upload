<?php
/**
 * A PHP script to parse a CSV and upload the value
 * to MySQL database table.
 * 
 * Pseudocode:
 * IF  notset (--file ) THEN 
 *  IF  isset (--create_table ) THEN  
 *      IF  isset (-h AND -u AND -p ) THEN 
 *           create_users_table(h,u,p)
 *      ELSE
 *          print("Please retry with username,password and host for the database")
 *      ENDIF
 *  ENDIF
 *  IF isset (--help )
 *       print_help_message()
 *  ENDIF
 * ELSE
 *  IF exist(filename)
 *      set users_array = get_csv_to_array(filename)
 *      IF sizeof(users_array) > 0
 *          FOREACH(users_array)
 *              ucfirst(first_name)
 *              ucfirst(last_name)
 *              strtolower(email)
 *              IF (valid email)
 *                  add to (valid_email_users_array)
 *              ELSE
 *                  add to (invalid_email_users_array)
 *              ENDIF
 *          END FOREACH
 *          IF notset(--dry_run)
 *              insert_to_db(h,u,p,valid_email_users_array)
 *          ENDIF
 *          print_valid_email_users_array
 *          print_invalid_email_users_array
 *      ELSE
 *          print("No user data in the file");
 *      ENDIF
 *  ELSE
 *      print("Please save the csv file in import folder and retry.")
 *  ENDIF 
 * 
 * @author     Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version    1.0
 */
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
// if --file option is not set
if (!isset($options['file'])){
    if (isset($options['create_table'])){
        if ( isset($options['h']) && isset($options['u']) && isset($options['p'])) {
            create_users_table($options['h'],$options['u'],$options['p']);
        } 
        else { 
            echo "Please retry with username,password and host for the database\n\n" ;
        }
    }
    if (isset($options['help'])){
        print_help_message();
    } 
    //If dry_run is added as option, but file is not specified.
    if(array_key_exists('dry_run', $options) && !array_key_exists('file', $options)){
        echo "Please retry with --file option\n";
    }
}
// if --file option is  set
else{
    $filename = "import/" . $options['file'];
    if (file_exists($filename)){       
        $users_arr = get_csv_to_array($filename);
        if (sizeof($users_arr) > 0){
            //removing header values
            array_shift($users_arr);
            $users_valid_email_arr = [];
            $users_invalid_email_arr = [];
            foreach($users_arr as &$user){
                $user[0] = ucfirst(strtolower(($user[0]))) ;
                $user[1] = ucfirst(strtolower(($user[1]))) ;
                $user[2] = strtolower($user[2]) ;   
                $user[2] = filter_var($user[2], FILTER_SANITIZE_EMAIL) ;           
                if (filter_var(trim($user[2]), FILTER_VALIDATE_EMAIL)) {  
                    array_push($users_valid_email_arr,$user); 
                }
                else{                   
                    array_push($users_invalid_email_arr,$user);
                }
            }
            //If dry_run option is not specified(If data is inserted to db)
            if (!array_key_exists('dry_run', $options)){
                if ( isset($options['h']) && isset($options['u']) && isset($options['p'])) {
                    insert_to_db($options['h'],$options['u'],$options['p'],$users_valid_email_arr);
                }
                else { 
                    echo "Please retry with username,password and host for the database\n\n" ;
                }
                if (sizeof($users_invalid_email_arr) > 0)
                {
                    echo "\nFollowing users were not inserted to user table because their emails were invalid.\n";
                    foreach($users_invalid_email_arr as $value) {                    
                        echo implode(",",$value) . "\n\n";
                    }
                }
            } 
            else{
                $msg_valid_email = "Users with valid emails:";
                $msg_invalid_email = "\nUsers with invalid emails:";
                print_array_with_message($msg_valid_email,$users_valid_email_arr);
                print_array_with_message($msg_invalid_email,$users_invalid_email_arr);
            }
        }
        else {
            echo "No user data in the file\n\n";
        }
    }    
    else{
        echo 'The CSV file doesn not exist in import folder.<br>' ;
    }
}
?>