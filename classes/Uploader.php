<?php
class Uploader  {

    public  $options;
    private $conn;
    private $data;

    const DATABASE = 'user_details';
       
    public function __construct($options){ 

        $this->options = $options;

        //$conn and  $data are set in connectToDB() and getDataFromFile() functions.
        //They do not have setters because they are not set publicly.
        isset($this->options['h']) && isset($this->options['u']) && isset($this->options['p']) ? 
                       $this->connectToDB() : false ;        
        isset($options['file']) ? $this->getDataFromFile() : false ;        
    }

    /*
    public function sanitizeOptions(){
        //validate options using a third party library.
    }
    */

    public function processOptions() {
        
        if (array_key_exists('help',$this->options ))
            $this->displayHelpMessage();
        elseif (array_key_exists('dry_run',$this->options ))
            isset($this->data) ?  $this->dryRun() : exit("Please add the correct CSV file ");
        elseif (array_key_exists('create_table',$this->options ))
            isset($this->conn) ? $this->createTable() : exit("Please retry with correct host,username and password of the database\n");
        elseif (array_key_exists('file',$this->options ))
            isset($this->conn) && isset($this->data) ? $this->uploadDataToDB() : exit("Please retry with correct host,username and password of the database and csv file.\n");
        else
            exit("Incorrect options.Execute \"php user_upload.php --help\" for more information or refer the user guide.\n");
            
    }

    private function connectToDB(){
         
        try{
            $conn = new mysqli($this->options['h'], $this->options['u'], $this->options['p'], self::DATABASE);
            if (!$conn->connect_error) {
                $this->conn = $conn;
            }
        }
        catch (Exception $e){
            exit($e->getMessage() . "\n");
        }        
    }

    private function validateFile(){
        try {
            if (!file_exists($this->options['file']) )
                throw new Exception('File not found.');

            $extension = pathinfo($this->options['file'], PATHINFO_EXTENSION);
            if ( strtolower($extension) != 'csv')  {
                throw new Exception('Incorrect file type.');
            } 
        } 
        catch ( Exception $e ) {
            exit($e->getMessage() . "\n");
        } 
        return true ;
    }

    private function readDatatoArray(){

        $data_array = [];
        $file = fopen($this->options['file'],"r");

        while($data = fgetcsv($file))
        {
            $data_array[] = $data; 
        }

        fclose($file);      
        return $data_array ;
    }

    private function sanitizeData($data_array){

        //removing header values
        array_shift($data_array);
        if (!sizeof($data_array) > 0 )
            exit("No valid data in the file.\n");
        foreach($data_array as &$value){
            $value[0] = trim(ucfirst(strtolower(($value[0])))) ;
            $value[1] = trim(ucfirst(strtolower(($value[1])))) ;
            $value[2] = trim(strtolower($value[2])) ; 
            //Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].  
            $value[2] = filter_var($value[2], FILTER_SANITIZE_EMAIL);  
            //Checks whether the value is a valid e-mail address
            //and alla flag         
            if (filter_var(trim($value[2]), FILTER_VALIDATE_EMAIL)) 
                $value['valid_email'] = true; 
            else                   
                $value['valid_email'] = false;
        }
        return $data_array;
    }


    private function getDataFromFile(){
        $this->validateFile();
        $data_array = $this->readDatatoArray();
        $sanitised_data = $this->sanitizeData($data_array);
        $this->data = $sanitised_data;
    }

    private function dryRun(){
        echo "List of Users with Valid Emails:\n";
        foreach($this->data as $value) {
            if ($value['valid_email'])
            echo str_pad($value[0],8) . str_pad($value[1],8) . str_pad($value[2],8) ."\n";
        }
        echo "\nList of Users with Invalid Emails:\n";
        foreach($this->data as $value) {
            if (!$value['valid_email'])
            echo str_pad($value[0],8) . str_pad($value[1],8) . str_pad($value[2],8) ."\n";
        }
    }

    private function createTable(){  

        $sql_drop = "DROP TABLE IF EXISTS user"  ;
            try{
                $this->conn->query($sql_drop);
                echo "user table is dropped successfully IF it existed.\n" ;  
            }
            catch (Exception $e){
                exit($e->getMessage() . "\n");
            } 
        $sql_create = "CREATE TABLE IF NOT EXISTS user (".
            "user_id INT NOT NULL AUTO_INCREMENT, ".
            "fname VARCHAR(30) NOT NULL,".
            "lname VARCHAR(30) NOT NULL,".
            "email VARCHAR(30) NOT NULL,".
            "PRIMARY KEY(user_id),".
            "UNIQUE KEY unique_email (email) )";
            try{
                $this->conn->query($sql_create);
                echo "user table created successfully\n\n";  
            }
            catch (Exception $e){
                echo $e->getMessage() . "\n";
            } 
    }

    private function uploadDataToDB(){

        $this->createTable();
        foreach($this->data as $value) {  
            if ($value['valid_email']){

                $sql_insert = $this->conn->prepare(" INSERT INTO user (fname,lname,email) VALUES (?, ?, ?)" );
                $sql_insert->bind_param("sss",$value[0], $value[1], $value[2]);
                try {
                    $sql_insert->execute();
                    echo str_pad($value[0],8) . str_pad($value[1],8) . str_pad($value[2],8)  . " successfully uploaded to db.\n" ;           
                }
                catch(Exception $e) {
                    echo $e->getMessage(). "\n";
                }
            }
            else{
                echo "\n" . str_pad($value[0],8) . str_pad($value[1],8) . str_pad($value[2],8) . " is not uploaded because invalid email.\n";
            }
        }
    }
    
    private function displayHelpMessage(){
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

    //destructor(Automicatically called)
    public function __destruct(){
        if (isset($this->conn))
            $this->conn->close();
    }
}
?>