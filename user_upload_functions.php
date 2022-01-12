<?php
/**
 * TRY connect_db
 *  IF error(connect_db)
 *      THROW EXCEPTION("Error creating users table.")
 *  ELSE
 *      IF table_exist(user)   
 *          drop_table(user);
 *          print "user table is dropped successfully.\n"
 *      ELSE
 *      TRY create_table
 *          IF success(create_table)
 *              PRINT("user table created successfully.)
 *          ELSE
 *              THROW EXCEPTION("Error creating user table")
 *          ENDIF
 *      CATCH EXCEPTION             
 *      EXIT
 * CATCH EXCEPTION 
 * EXIT              
 */
function create_users_table($host,$username,$password){   
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);      
    $dbname = "user_details";
    $conn = new mysqli($host, $username, $password, $dbname);
    try{
        if ($conn->connect_error) {
            throw new Exception("Connection failed:\n" . $conn->connect_error ."\n");
        }
        else{
            $sql_drop = "DROP TABLE IF EXISTS user ";
            if ($conn->query($sql_drop)) {
                echo "user table is dropped successfully IF it existed.\n" ;           
            }
            $sql_create = "CREATE TABLE IF NOT EXISTS user (".
                "user_id INT NOT NULL AUTO_INCREMENT, ".
                "fname VARCHAR(30) NOT NULL,".
                "lname VARCHAR(30) NOT NULL,".
                "email VARCHAR(30) NOT NULL,".
                "PRIMARY KEY(user_id),".
                "UNIQUE KEY unique_email (email) )";
                try{
                if ($conn->query($sql_create) === TRUE) {
                    echo "user table created successfully\n\n";
                    $conn->close();
                } else {
                    throw new Exception("Error creating user table: " . $conn->error);
                    $conn->close();                    
                }}
                catch(Exception $ne){
                    echo $ne->getMessage();
                    exit();
                }       
        }
    }
    catch(Exception $e){
        echo $e->getMessage();
        exit();
    }  
}


    


function  print_help_message()
{
    echo "\n----------------------------------------\n";
    echo "Options/Directives for user_upload.php\n";
    echo "----------------------------------------\n";
    echo str_pad("-u <MySQL username>",30). "Provides MySQL username. Value Required.\n";
    echo str_pad("-p <MySQL password>",30). "Provides MySQL password. Value Required.\n";
    echo str_pad("-h <MySQL host>",30). "Provides MySQL host. Value Required.\n\n";
    echo str_pad("--file <csv file name>",30). "Provides the CSV file for parsing. Value Required.".
    str_pad("\n",31) ."Value is file name with .csv extension. Make sure the file exists in the import folder.\n\n";
    echo str_pad("--create_table",30). "Creates the MySQL users table. No value".
    str_pad("\n",31)."It is required to use -u, -p and -h options together with create_table option.";
    echo str_pad("\n",31). "No further action is taken\n\n";
    echo str_pad("--dry_run" ,30). "Executes the script with the --file directive without inserting data into the DB.No value".
    str_pad("\n",31) ."All other functions will be executed, but the database will not be altered or updated.\n\n";
    echo str_pad("--help",30). "Explains options/directives of user_upload.php\n\n"; 
}
// invalid will not be inserted to db

// shift
// function get_csv_to_array($filename)
// {
//     if (($open = fopen($filename, "r")) !== FALSE) 
//     {
//         while (($data = fgetcsv($open, 1000, ",")) !== FALSE) 
//         {        
//             $array[] = $data; 
//         }
//         fclose($open);
//     }  
//     return $array ;
?>

