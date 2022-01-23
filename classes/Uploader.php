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

    // public function sanitizeOptions(){
    //     //expect to validate commandline options using a third party library.
    // }

    public function processOptions() {
        
        // if ( isset($this->options['file']) && !isset($this->options['dry_run'])) 
        //     $this->options['upload'] = true ;

        //var_dump($this->options);
        //$keys = array_keys($this->options);
        //var_dump($keys);
        //in_array('help',$keys) ? $this->displayHelpMessage() : df;
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
        
        //     foreach($this->options as $key => $val)
        // {
        //     //echo $key. "\n";
        //     switch($key){
        //         case 'help':
                    
        //         break;
        //         case 'dry_run':
                    
        //         break;
        //         case 'create_table':
                    
        //         break;
        //         case ('file'):
        //             !isset($this->options['dry_run']) && 
        //         break;
        //         default:
                    
        //     }
        // }
    }

    private function connectToDB(){
         ;
        try{
            $conn = new mysqli($this->options['h'], $this->options['u'], $this->options['p'], self::DATABASE);
            
            if (!$conn->connect_error) {
                $this->conn = $conn;
            }
        }
        catch (Exception $e){
            //print_r($e->getTrace());
            //is exit necessary?
            exit($e->getMessage() . "\n");
            
        }        
    }

    private function validateFile()
    {
        //if file exists
        //type.csv
        try {
            if (!file_exists($this->options['file']) ) {
                throw new Exception('File not found.');
            }
            //check if .csv
            // if (  ) {
            //     throw new Exception('File openning failed.');
            // } 
            //if empty
        } 
        catch ( Exception $e ) {
            //is exit necessary?
            exit($e->getMessage() . "\n");
        } 
        return true ;
    }

    private function readDatatoArray(){

        $data_array = [];
        //$data_array = file($this->options['file']);
        $file = fopen($this->options['file'],"r");

        while($data = fgetcsv($file))
        {

        //$data = fgetcsv($file);
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
            $value[0] = ucfirst(strtolower(($value[0]))) ;
            $value[1] = ucfirst(strtolower(($value[1]))) ;
            $value[2] = strtolower($value[2]) ; 
            //Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].  
            $value[2] = filter_var($value[2], FILTER_SANITIZE_EMAIL);  
            //Validates whether the value is a valid e-mail address.         
            if (filter_var(trim($value[2]), FILTER_VALIDATE_EMAIL)) {  
                $value['valid_email'] = true; 
            }
            else{                    
                $value['valid_email'] = false; 
            }
        }
        return $data_array;
    }


    private function getDataFromFile(){
        $this->validateFile();
        $data_array = $this->readDatatoArray();
        $sanitised_data = $this->sanitizeData($data_array);
       
        $this->data = $sanitised_data;
        //var_dump($this->data);
    }

    private function dryRun(){
        echo "In dry run\n";
        foreach($this->data as $value) {
        echo implode(" ",$value) . "\n";
        //exit();
    }

    }
    private function createTable(){   
        //echo 'in createTable\n';

        $sql_drop = "DROP TABLE IF EXISTS user"  ;
        try{
            $this->conn->query($sql_drop);
            echo "user table is dropped successfully IF it existed.\n" ;                   
    
        }
        catch (Exception $e){
            //print_r($e->getTrace());
            echo $e->getMessage() . "\n";
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
            //print_r($e->getTrace());
            echo $e->getMessage() . "\n";
        } 
        
    }

       
        
    
    private function uploadDataToDB(){
        // echo 'in upload to db' ;
        
        // $this->conn
        $this->createTable();
        //var_dump($this->data);
        
        foreach($this->data as $value) {  
            if ($value['valid_email']){
            $fname = mysqli_real_escape_string($this->conn,$value[0]);     
            $lname = mysqli_real_escape_string($this->conn,$value[1]);     
            $email = mysqli_real_escape_string($this->conn,$value[2]); 

            $sql_insert  = " INSERT IGNORE INTO user (fname,lname,email) VALUES " . 
                           "('" . $fname . "','" ."$lname" . "','" . $email . "')" ;
                          
                echo $this->conn->query($sql_insert);
                if ($this->conn->query($sql_insert)) {
                    echo implode(",",$value) . " successfully inserted to db.\n" ;           
                }
                else{
                    echo implode(",",$value) . "Error inserting to db.";
                }
            }
            else{
                
            }
        
        }
        
        //exit();
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
        //exit();
    }

    //destructor(Automicatically called)
    public function __destruct(){
        if (isset($this->conn))
            $this->conn->close();
    }
}
?>