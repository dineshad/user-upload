<?
/**
 * User Class
 * 
 * @param      $fname,$lname,$email
 * 
 * @author     Dinesha Dayananda <dinesha.dayananda@gmail.com> 
 * @version    1.0
 */
 class User{

    private string $fname;
    private string $lname;
    private string $email;

    public function  __construct($fname,$lname,$email){
        $this->fname = trim(ucfirst(strtolower(($fname))));
        $this->lname = trim(ucfirst(strtolower(($lname))));
        $this->email = filter_var(trim(strtolower($email)),FILTER_SANITIZE_EMAIL); 
    }

    public function get_fname(){
        return $this->fname;
    }

    public function get_lname(){
        return $this->lname;
    }

    public function get_email(){
        return $this->email;
    }

    public function is_email_valid(){
        return $is_valid = filter_var($this->email, FILTER_VALIDATE_EMAIL) ?  true : false ;
    }
 }
?>