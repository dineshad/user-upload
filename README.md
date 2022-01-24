# Upload Users

A PHP script which accepts a CSV file as input,processes the CSV file and insert the parsed file data into a MySQL database. This script is executed from the command line. 

## Requirements:
- Ubuntu 18.04
- PHP 8.0
- MySQL  Ver 14.14 Distrib 5.7.36

## Assumptions:
- users.csv which contains user data is saved in the same directory as user_upload.php
- user.csv has a header row.
- The database ‘user_details’ is created before executing the script.

## Options:
- -u <MySQL username>  : MySQL username
- -p <MySQL password>  : MySQL password
- -h <MySQL host>      : MySQL host
- --file <csv file name> : CSV file containing user_data. Save the file in the same directory as user_upload.php.Use with --dry_run or -h,-u and -p. When --file is used with -h,-u and -p, data is uploaded to the database
- --create_table : Creates the user table to which user data is uploaded.
- --dry_run : Parse,format and validates data without uploading to the database. Use with --file option.
- --help : Lists options for user_upload.php


* Requirements Document,Design Document,Testing Document and User Guide of the PHP script are available in the documents folder for more information.


