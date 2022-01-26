<?
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
    private string $type;
    private string $location; 
    private boolean $is_exist;

    public function __construct($file_name,$type,$location = null){
        $this->file_name = $file_name ; 
        $this->type = $type;
        $this->location = $location;
    }

    public function get_file_name(){
        return $this->file_name;
    }

    public function get_type(){
        return $this->type;
    }

    public function get_location(){
        return $this->location;
    }

    public function is_file_exist(){  
        return $file_exist = file_exists($this->location.$this->name) ? true : false;
    }

    public function is_right_type($right_type){
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return $is_right_type = strcasecmp($right_type, $extension) == 0 ? true : false ;            
    }
}
?>