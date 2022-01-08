# user-upload

**NB: This script is still work in progress. The below are the expectation from the script. They're still being implemented.

A PHP script which accepts a CSV file as input,processes the CSV file and insert the parsed file data into a MySQL database. This script is executed from the command line. 


• The CSV file is saved in the import folder and contains user data which consists of three columns: name, surname, email
• CSV file have an arbitrary list of users.
• The script iterates through the CSV rows and insert each record into a dedicated MySQL database into the table “users”
• The users database table is created/rebuilt as part of the PHP script.
This will be defined as a Command Line directive.
• Name and surname field are capitalised before being inserted into DB.
• Emails are set to be lower case before being inserted into DB.
• The script  validates the email address before inserting, to make sure it is a legal email format.In case that an email is invalid, no insert is made to the database and an error message is reported to STDOUT.

This script is created and tested on:
Ubuntu 18.04
PHP version 8.0
MySQL  Ver 14.14 Distrib 5.7.36


