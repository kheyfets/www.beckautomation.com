<?php
// This is the file that contains all of the user data
// The location should be relative to this file
define( 'USERSFILE', './users'); 

// this is the user data array, it is global so that various functions can 
// use it to read information about the user
$user = generateUser();

function generateUser()
{
    global $content;
    $userData = Array();
    if(isset($_POST['email']) || isset($_POST['phone']))
    {
        $userData = generateUserDataFromPOST();
    } else if(isset($_COOKIE['user']))
    {
        $userData = generateUserDataFromFile($_COOKIE['user']);
    }
    return $userData;
}

// generates data from post information
function generateUserDataFromPOST()
{
        global $fields;
        $userData = Array();
        foreach($fields as $field)
        {
            $userData[$field['name']] = $_POST[$field['name']];
        }
        return $userData;
}

// generates data from file if uesr is present inside
function generateUserDataFromFile($email)
{
    global $fields;
    $user = Array();

    if(is_readable(USERSFILE))
    {
        $userlines = file(USERSFILE);

        foreach($userlines as $line)
        {
            $slots = split("\t", $line);
            if($slots[0] == $email)
            {
                $inc = 0;
                foreach($fields as $field)
                {
                    $user[$field['name']] = $slots[$inc++]; 
                }

            }
        }
    }
    return $user;
}
?>
