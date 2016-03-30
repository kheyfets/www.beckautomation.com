<?php

// This is the email address that information should go to
define( 'EMAILTO',   'sales@beckautomation.com');

// This is the email address that information should claim to be from
define( 'EMAILFROM', 'automailer@beckautomation.com');

/*******
 * @func sendMail
 * @desc This function sends an email to a recipient stating all of the \
 *       current user's contact info and what they've downloaded
 * @pre  The server must be able to send an email
 ******/
function sendMail($from, $body, $to, $title )
{
    $headers = "FROM: " . $from . "\r\n" .
               "X-Mailer: php\r\n" .
               "X-Author: Beck Automation Download Center\r\n";
    mail($to, $title, $body, $headers);
}

function makeEmailContent($user, $body)
{
    foreach(array_keys($user) as $attribute)
    {
        $body=str_replace("%".strtoupper($attribute)."%", $user[$attribute], $body);
    }
    return $body;
}

?>
