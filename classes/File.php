<?php
/**
 * File class
 * 
 * @param   $file_name,$type,$location
 * 
 * @author   Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version  1.0
 */
class File{

    private string $file_name;
    private string $location; 
    private bool $is_exist;
    public $error ;

    public function __construct($file_name,$location = ''){
        $this->file_name = $file_name ; 
        $this->location = $location;
       
    }

    public function get_file_name(){
        return $this->file_name;
    }
    
    public function get_location(){
        return $this->location;
    }

    public function is_file_exist(){  
        $file_path = $this->location.$this->file_name;
        $file_exist = file_exists($file_path) ? true : false;
        return $file_exist;
    }

    public function is_right_type($right_type){
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return $is_right_type = strcasecmp($right_type, $extension) == 0 ? true : false ;            
    }

    public function get_data_into_array(){
        $data_array = [];
        $file = fopen($this->file_name,"r");

        while($data = fgetcsv($file))
        {
            $data_array[] = $data; 
        }

        fclose($file);      
        return $data_array ;
    }
}
?>