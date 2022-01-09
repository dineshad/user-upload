<?php
function  print_help_message()
{
    echo "\n----------------------------------------\n";
    echo "Options/Directives for user_upload.php\n";
    echo "----------------------------------------\n";
    echo str_pad("--file <csv file name>",30). "Provides the CSV file for parsing. Value Required.".
    str_pad("\n",31) ."Value is file name with .csv extension. Make sure the file exists in the import folder.\n\n";
    echo str_pad("--create_table",30). "Creates the MySQL users table. No value".
    str_pad("\n",31)."It is required to use -u, -p and -h options together with create_table option.";
    echo str_pad("\n",31). "Any other command line option  will be ignored.No further action is taken\n\n";
    echo str_pad("--file <csv file name>",30). "Provides the CSV file for parsing. Value Required.".
    str_pad("\n",31) ."Value is file name with .csv extension. Make sure the file exists in the import folder.\n\n";
    echo str_pad("--dry_run" ,30). "Executes the script with the --file directive without inserting data into the DB.No value".
    str_pad("\n",31) ."All other functions will be executed, but the database will not be altered or updated.\n\n";
    echo str_pad("-u <MySQL username>",30). "Provides MySQL username. Value Required.\n";
    echo str_pad("-p <MySQL password>",30). "Provides MySQL password. Value Required.\n";
    echo str_pad("-h <MySQL host>",30). "Provides MySQL host. Value Required.\n\n";
    echo str_pad("--help",30). "Explains options/directives of user_upload.php\n\n"; 
}
?>