<?php
/**
 * A static class to upload CSV data to database.
 * 
 * @param      $options
 * 
 * @author     Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version    2.0
 */
class Uploader  {
    public static $action ;

    /**
     * Determine the action
     */
    public static function get_action($options)
    {
        if (array_key_exists('help',$options))
            self::$action = 'help';
        elseif (array_key_exists('dry_run',$options) && array_key_exists('file',$options))
            self::$action = 'dry_run';
        elseif (array_key_exists('create_table',$options) && array_key_exists('h',$options)
                && array_key_exists('u',$options) && array_key_exists('p',$options))
            self::$action = 'create_table';
        elseif (array_key_exists('file',$options) && !array_key_exists('dry_run',$options)
                && array_key_exists('h',$options) && array_key_exists('u',$options) && array_key_exists('p',$options))
            self::$action = 'upload';
        else
            self::$action = NULL;
        return self::$action; 
    }

    /**
     * Display Help information.
     */
    public static function display_help_message(){
        echo "\n----------------------------------------\n";
        echo "Options/Directives for user_upload.php\n";
        echo "----------------------------------------\n";
        echo str_pad("-u <MySQL username>",30). "MySQL username \n";
        echo str_pad("-p <MySQL password>",30). "MySQL password \n";
        echo str_pad("-h <MySQL host>",30). "MySQL host\n\n";
        echo str_pad("--file <csv file name>",30). "CSV file containing user_data. Save the file in the same directory as user_upload.php". 
        str_pad("\n",31) ."Use with --dry_run or -h,-u and -p. When --file is used with -h,-u and -p, data is uploaded to the database.\n\n";
        str_pad("\n",31) ."Value is file name with .csv extension. Make sure the file exists in the import folder.\n\n";
        echo str_pad("--create_table",30). "Creates the user table to which user data is uploaded.".
        str_pad("\n",31)."Use with -h, -u and -p options.\n\n";
        echo str_pad("--dry_run" ,30). "Parse,format and validates data without uploading to the database. Use with --file option.\n\n";        
        echo str_pad("--help",30). "Lists options for user_upload.php\n\n"; 
    }

    /**
     * Extracts,formats,validate data 
     * without uploading to the database.
     */
    public static function dry_run($user_obj_list){
        foreach($user_obj_list as $user) {
            $tail = $user->is_email_valid() ? '' : "  : INVALID EMAIL" ;
                echo str_pad($user->get_fname(),8) . str_pad($user->get_lname(),8) . str_pad($user->get_email(),8) . $tail . "\n";
        }
    }

    /**
     * create user table. Drop it first if already exists.
     */
    public static function create_table($conn){  

        $sql_drop = "DROP TABLE IF EXISTS user"  ;
        try{
            $conn->query($sql_drop);
            if ($conn->error) 
                throw new Exception($conn->error); 
            echo "user table is dropped successfully IF it existed.\n" ; 
        }
        catch (Exception $e){
            exit($e->getMessage());
        } 

        $sql_create = "CREATE TABLE IF NOT EXISTS user (".
            "user_id INT NOT NULL AUTO_INCREMENT, ".
            "fname VARCHAR(30) NOT NULL,".
            "lname VARCHAR(30) NOT NULL,".
            "email VARCHAR(30) NOT NULL,".
            "PRIMARY KEY(user_id),".
            "UNIQUE KEY unique_email (email) )";
        try{
            $conn->query($sql_create);
            echo "user table created successfully\n\n";  
            if ($conn->error) 
                throw new Exception($conn->error); 
        }
        catch (Exception $e){
            exit($e->getMessage() . "\n");
        } 
    }

    /**
     * Upload the data to the database.
     */
    public static function upload($conn,$user_obj_list){
        self::create_table($conn);
        foreach($user_obj_list as $user) {  
            if ($user->is_email_valid()){                
                $sql_insert = $conn->prepare(" INSERT INTO user (fname,lname,email) VALUES (?, ?, ?)" );
                // $sql_insert->bind_param("sss",$user->get_fname(), $user->get_lname(), $user->get_email());
                $fname = $user->get_fname();
                $lname = $user->get_lname();
                $email = $user->get_email();
                $sql_insert->bind_param("sss",$fname, $lname, $email);
                try {
                    $sql_insert->execute();
                    $tail = " successfully uploaded to db.\n" ;
                    echo str_pad($user->get_fname(),8) . str_pad($user->get_lname(),8) . str_pad($user->get_email(),8) . $tail  ;           
                }
                catch(Exception $e) {
                    echo $e->getMessage(). "\n";
                }
            }
            else{
                $tail = " is not uploaded because invalid email.\n" ;
                echo "\n" . str_pad($user->get_fname(),8) . str_pad($user->get_lname(),8) . str_pad($user->get_email(),8) . $tail ;
            }
        }
    }
}
?>