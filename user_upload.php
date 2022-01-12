<?php
require "user_upload_functions.php";

$shortopts  = "";
$shortopts .= "u:";  
$shortopts .= "p:";
$shortopts .= "h:";


$longopts  = array(
    "file:",
    "create_table",
    "dry_run", 
    "help"        
);
$options = getopt($shortopts, $longopts);
//var_dump($options);
if (isset($options['file'])){
    //echo 'file option is there' ;
    $filename = "import/" . $options['file'];
    if (file_exists($filename))  {
        //echo 'file exists' ;
        $users = get_csv_to_array($filename);        
        //var_dump($users);       

    }    
    else
    {
        echo 'The CSV file doesn not exist in import folder.<br>' ;
    }

}
else{
    //echo 'no file option';
    if (isset($options['create_table']))
    {
        if ((isset($options['u'])&& isset($options['p']) && isset($options['h']) ))
        {
            create_users_table($options['u'],$options['p'],$options['h']);
        }
        else
        {
            echo "Please provide username,password and host for the database" ;
        }
    }
    if (isset($options['help']))
        print_help_message();

}

?>