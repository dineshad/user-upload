<?php
class Uploader  {

    public $options;
    private $conn ;
    private $valid_file;
       
    public function __construct($options){
        
        $this->options = $options;
        //No setters are  provided for the below two functionalities, 
        //as they  are not intended to make public.
        $this->conn =  isset($this->options['h']) && isset($this->options['u']) && isset($this->options['p']) ? 
                       $this->tryConnectToDB() : false ;
        $this->valid_file = isset($options['file']) ? $this->validateFile() : false ;
    }
    
    private function tryConnectToDB(){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
        try{
            $conn = new mysqli($this->options['h'], $this->options['u'], $this->options['p'], DATABASE);
            if (!$conn->connect_error) 
                return $conn;
                print_r($conn);
        }
        catch (Exception $e){
            //print_r($e->getTrace());
            echo $e->getMessage() . "\n";
        }        
    }

    public function sanitizeOptions(){

    }

    public function processOptions() {
        //echo "Process" ;
        
        if ( isset($this->options['file']) && !isset($this->options['dry_run'])) 
            $this->options['upload'] = true ;
        //var_dump($this->options);

        foreach($this->options as $key => $val)
        {
            switch($key){
                case 'help':
                    $this->displayHelpMessage();
                break;
                case 'dry_run':
                    $this->valid_file ?  $this->dryRun() : exit("Please check the correct CSV file with user data exists in the " . IMPORT_FOLDER . " folder");
                break;
                case 'create_table':
                    is_obj($this->conn) ? $this->createTable() : exit("Please retry with correct host,username and password of the database\n");
                break;
                case ('upload'):
                    is_obj($this->conn) ? $this->uploadDataToDB() : exit("Please retry with correct host,username and password of the database\n");
                break;
                default:
                    exit("Please retry with correct options.\n Execute \"php " . basename(__FILE__) . " --help\" for more information.\n");
            }
        }
        //--help         
        if ( isset($this->options['help'])){
            $this->displayHelpMessage();
        }
                   
        //--dry_run
        if ( isset($this->options['dry_run']) ){
            if ($this->valid_file)
            {
                $this->dryRun();
            }
            else
                exit("Please check the correct CSV file with user data exists in the " . IMPORT_FOLDER . " folder");
        }   
        
        //--create_table
        if ( isset($this->options['create_table']) ){
            if (is_obj($this->conn))
            {
                $this->createTable();
            }
            else
                exit("Please retry with correct host,username and password of the database\n");
        }   
        
        //--file without --dry_run 
        if ( isset($this->options['file']) && !isset($this->options['dry_run'])) {
            if ($this->valid_file)
            {
                $this->sanitizeData();
                $this->uploadDataToDB();
            }
            else        
                exit("Please check the correct CSV file with user data exists in the " . IMPORT_FOLDER . " folder");
        }
    }

    

    private function validateFile(){

    }

    private function sanitizeData(){

    }
    private function uploadDataToDB(){
        //stack trace

    }
    private function dryRun(){

    }

    
    private function createTable(){   
        echo 'in createTable';
   
        // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);      
        // $dbname = "user_details";
        // $conn = new mysqli($host, $username, $password, $dbname);
        // try{
        //     if ($conn->connect_error) {
        //         throw new Exception("Connection failed:\n" . $conn->connect_error ."\n");
        //     }
        //     else{
        //         $sql_drop = "DROP TABLE IF EXISTS user ";
        //         if ($conn->query($sql_drop)) {
        //             echo "user table is dropped successfully IF it existed.\n" ;           
        //         }
        //         $sql_create = "CREATE TABLE IF NOT EXISTS user (".
        //             "user_id INT NOT NULL AUTO_INCREMENT, ".
        //             "fname VARCHAR(30) NOT NULL,".
        //             "lname VARCHAR(30) NOT NULL,".
        //             "email VARCHAR(30) NOT NULL,".
        //             "PRIMARY KEY(user_id),".
        //             "UNIQUE KEY unique_email (email) )";
        //             try{
        //             if ($conn->query($sql_create) === TRUE) {
        //                 echo "user table created successfully\n\n";                   
        //             } else {
        //                 throw new Exception("Error creating user table: " . $conn->error . "\n\n");
        //             }}
        //             catch(Exception $ne){
        //                 echo $ne->getMessage();
        //                 exit();
        //             }       
        //     }
        // }
        // catch(Exception $e){
        //     echo $e->getMessage();
        //     exit();
        // } 
    } 
    
    private function displayHelpMessage(){
        echo "\n----------------------------------------\n";
        echo "Options/Directives for user_upload.php\n";
        echo "----------------------------------------\n";
        echo str_pad("-u <MySQL username>",30). "MySQL username. Value Required.\n";
        echo str_pad("-p <MySQL password>",30). "MySQL password. Value Required.\n";
        echo str_pad("-h <MySQL host>",30). "MySQL host. Value Required.\n\n";
        echo str_pad("--file <csv file name>",30). "CSV file for parsing. Value Required.".
        str_pad("\n",31) ."Value is file name with .csv extension. Make sure the file exists in the import folder.\n\n";
        echo str_pad("--create_table",30). "Creates the MySQL users table. No value required".
        str_pad("\n",31)."It is required to use -u, -p and -h options together with create_table option.";
        echo str_pad("\n",31). "No further action is taken\n\n";
        echo str_pad("--dry_run" ,30). "Executes the script with the --file option.Data is not inserted to the database.No value is required.".
        str_pad("\n",31) ."All other functions will be executed, but the database will not be altered or updated.\n\n";
        echo str_pad("--help",30). "Explains options/directives of user_upload.php\n\n"; 
        exit();
    }

    //Automicatically called destructor
    public function __destruct(){
        //close db conn
    }
}
?>