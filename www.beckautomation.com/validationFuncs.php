<?php

// this is the global error string, if a function throws an error the user 
// should know about it should append it to this string // preferably with 
// a <br/> so that it is on its own line.
$error   = "";

/******
 * @desc verifies that the information posted by the user meets
 *       the requirements
 *****/
function postIsValid()
{
    global $fields;
    global $error;
    $retVal = true;
    
    foreach($fields as $field)
    {
        // make sure that fields that shouldn't be blank aren't
        if($field['required'] && $_POST[$field['name']] == "" )
        {
            $retVal = false;
            $error .= "Please provide " . $field['title'] . "<br/>";
        }
        // call the validator if there is one
        if(isset($field['validator'])
        && !call_user_func($field['validator'], $_POST[ $field['name'] ] ) )
        {
            $error .= $field['title'] . " is invalid.<br/>";
            $retVal = false;
        }
    }
    return $retVal;
}


/****
 * @func emailIsValid
 * @desc This function validates an email address (mostly)
 ****/
function emailIsValid($email)
{

    // crazy expression ahead,
    // for more information on this regex please see
    //  http://www.breakingpar.com/bkp/home.nsf/Doc!OpenNavigator&87256B280015193F87256C40004CC8C6
    $exp = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[(2([0-4]\d|5[0-5])|1?\d{1,2})(\.(2([0-4]\d|5[0-5])|1?\d{1,2})){3} \])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
    return preg_match($exp, $email) == 1; 
}

/****
 * @func phoneIsValid
 * @desc This function validates a phone number,
 *       the only way I could think to do this is verify if there were at least 10 digits.
 ****/
function phoneIsValid($phone)
{
    $exp = "/([\d].*){10,}/";
    return preg_match($exp, $phone) == 1;
}

?>
