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
    
    //public $error_msg ;
    public static $action ;

    public static function get_action($options)
    {
        //var_dump($options);
        if (array_key_exists('help',$options))
            //echo 'in help' ;
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

    public static function dry_run($user_obj_list){
        foreach($user_obj_list as $user) {
            $tail = $user->is_email_valid() ? '' : "  : INVALID EMAIL" ;
                echo str_pad($user->get_fname(),8) . str_pad($user->get_lname(),8) . str_pad($user->get_email(),8) . $tail . "\n";
        }
    }

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
    
    

    /**
     * destructor(Automicatically called)
     * Close the db connection if it's opened.
     */
    // public function __destruct(){
    //     if (isset($this->conn))
    //         $this->conn->close();
    // }
     
    // if (array_key_exists('help',$options ))
    //         self::$action = 'help';
    //     elseif (array_key_exists('dry_run',$options))
    //         $user_obj_list != false ?  self::dry_run($user_obj_list) : exit("Retry with CSV file.\n" . self::display_help_message());
    //     elseif (array_key_exists('create_table',$options))
    //         $conn != false ? self::create_table() : exit("Retry with correct host,username and password of the database");
    //     elseif (array_key_exists('file',$options) && !array_key_exists('dry_run',$options))
    //         $conn != false && $user_obj_list != false ? self::upload_data_to_db() : exit("Retry with correct host,username and password of the database and CSV file.");
    //     else
    //         exit("Retry with correct options.\n Execute \"php " . basename(__FILE__) . " --help\" for more information.\n") ;


        
            //isset($this->conn) && isset($this->data) ? $this->uploadDataToDB() : exit("Please retry with correct host,username and password of the database and csv file.\n");
        //else
            //exit("Incorrect options.Execute \"php user_upload.php --help\" for more information or refer the user guide.\n");
    // self::$action = array_key_exists('help',$options )? 'help' : NULL ;
        // self::$action = array_key_exists('dry_run',$options ) && $user_obj_list != false ?
        //                 'dry_run' : NULL ;
        // self::$action = array_key_exists('create_table',$options ) && $conn != false ? 
        //                 'create_table' : NULL ;
        // self::$action = array_key_exists('file',$options ) && !array_key_exists('dry_run',$options ) ? 'upload' : NULL ;
    //     //$conn and  $data are set in connectToDB() and getDataFromFile() functions.
    //     //They do not have setters because they are not set publicly.
    //     //isset($this->options['h']) && isset($this->options['u']) && isset($this->options['p']) ? 
    //                    $this->connectToDB() : false ;        
    //     //isset($options['file']) ? $this->getDataFromFile() : false ;        
    // }

    
    // public function sanitizeOptions(){
    //     //validate options using a third party library.
    // }
    
    /**
     * Process the options and determin
     * which action need to be taken.
     */
    // public function get_main_action() {
        
    //     if (array_key_exists('help',$this->options ))
    //         //$this->displayHelpMessage();
    //         return 'help' ;
    //     elseif (array_key_exists('dry_run',$this->options ))
    //         //isset($this->data) ?  $this->dryRun() : exit("Please add the correct CSV file ");
    //         return 'dry_run' ;
    //     elseif (array_key_exists('create_table',$this->options ))
    //         isset($this->conn) ? $this->createTable() : exit("Please retry with correct host,username and password of the database\n");
    //     elseif (array_key_exists('file',$this->options ))
    //         isset($this->conn) && isset($this->data) ? $this->uploadDataToDB() : exit("Please retry with correct host,username and password of the database and csv file.\n");
    //     else
    //         exit("Incorrect options.Execute \"php user_upload.php --help\" for more information or refer the user guide.\n");
            
    //}
    /**
     * Try connecting to the database
     * and if success, assign the connection to
     * $this->conn
     */
    // private function connectToDB(){
         
    //     try{
    //         $conn = new mysqli($this->options['h'], $this->options['u'], $this->options['p'], self::DATABASE);
    //         if (!$conn->connect_error) {
    //             $this->conn = $conn;
    //         }
    //     }
    //     catch (Exception $e){
    //         exit($e->getMessage() . "\n");
    //     }        
    // }
    /**
     * Check whether the  file exists in the same directory as user_upload.php
     * and whether the extension of the file is .csv
     */
    // private function validateFile(){
    //     try {
    //         if (!file_exists($this->options['file']) )
    //             throw new Exception('File not found.');

    //         $extension = pathinfo($this->options['file'], PATHINFO_EXTENSION);
    //         if ( strtolower($extension) != 'csv')  {
    //             throw new Exception('Incorrect file type.');
    //         } 
    //     } 
    //     catch ( Exception $e ) {
    //         exit($e->getMessage() . "\n");
    //     } 
    //     return true ;
    // }

    /**
     * Extract the data in csv file to an array.
     */
    // private function readDatatoArray(){

    //     $data_array = [];
    //     $file = fopen($this->options['file'],"r");

    //     while($data = fgetcsv($file))
    //     {
    //         $data_array[] = $data; 
    //     }

    //     fclose($file);      
    //     return $data_array ;
    // }

    /**
     * Create the list of data objects.
     */
    // private function create_obj_list($data_array){
    //     //removing header values
    //     array_shift($data_array);
    //     if (!sizeof($data_array) > 0 )
    //         exit("No valid data in the file.\n");
    //     $obj_list = [];
    //     foreach($data_array as $value){
    //         $user = new User($value[0],$value[1],$value[2]);
    //         array_push($obj_list,$user);
    //     }
    //     return $obj_list;
    // }

    /**
     * Executes validateFile(),readDatatoArray,sanitizeData
     * in one go and assign to $this->data
     */
    // private function get_data_from_file(){
    //     $this->validateFile();
    //     $data_array = $this->readDatatoArray();
    //     $sanitised_data = $this->sanitizeData($data_array);
    //     $this->data = $sanitised_data;
    // }

    /**
     * Extract,format,validate data 
     * without uploading to the database.
     */
    
}
?>