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
 *              insert_to_db(valid_email_users_array)
 *          ENDIF
 *          print_valid_email_users_array
 *          print_invalid_email_users_array
 *      ELSE
 *          print("No data in the file");
 *      ENDIF
 *  ELSE
 *      print("Please save the csv file in import folder and retry.")
 *  ENDIF
 * 
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
	    
if (!isset($options['file'])){
    if (isset($options['create_table']))
    {
        if ( isset($options['h']) && isset($options['u']) && isset($options['p']))
        {
            create_users_table($options['h'],$options['u'],$options['p']);
        } 
        else
        { 
            echo "Please retry with username,password and host for the database\n\n" ;
        }
    }
    if (isset($options['help']))
    {
        print_help_message();
    } 
}
else{
    $filename = "import/" . $options['file'];
    if (file_exists($filename))  {
       
        $users = get_csv_to_array($filename);        
              

    }    
    else
    {
        echo 'The CSV file doesn not exist in import folder.<br>' ;
    }
}

?>