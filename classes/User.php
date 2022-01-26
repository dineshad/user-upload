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
    private boolean $is_valid_email;

    public function  __construct($fname,$lname,$email){
        $this->fname = trim(ucfirst(strtolower(($fname))));
        $this->lname = trim(ucfirst(strtolower(($lname))));
        $this->email = filter_var(trim(strtolower($email)),FILTER_SANITIZE_EMAIL); 
    }

    private function set_email_validity(){
        $this->is_valid_email = filter_var(trim($this->email), FILTER_VALIDATE_EMAIL) ?  true : false ;              
    }

    function get_fname()
    {
        return $this->fname;
    }

    function get_lname()
    {
        return $this->lname;
    }

    function get_email()
    {
        return $this->email;
    }

    function get_email_validity()
    {
        return $this->is_valid_email;
    }
 }
?>