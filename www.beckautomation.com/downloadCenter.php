<?php
// This array defines the elements AND THEIR ORDER for information requested, received, and stored from the user
// modifying this array will add new elements for the user to fill in and will add them to the users file
// HOWEVER modifying the order will break previously stored user information
$fields = Array
    (
        Array(
            'name' => 'email',     
            'title' => 'Email',           
            'required' => 1,
            'validator' => 'emailIsValid'
        ),
        Array(
            'name' => 'firstName', 
            'title' => 'First Name',      
            'required' => 1
        ),
        Array(
            'name' => 'lastName',  
            'title' => 'Last Name',       
            'required' => 1
        ),
        Array(
            'name' => 'title',
            'title' => 'Title',           
            'required' => 0
        ),
        Array(
            'name' => 'company',   
            'title' => 'Company',         
            'required' => 1
        ),
        Array(
            'name' => 'address1',  
            'title' => 'Address 1',       
            'required' => 1
        ),
        Array(
            'name' => 'address2',      
            'title' => 'Address 2',       
            'required' => 0
        ),
        Array(
            'name' => 'city',     
            'title' => 'City',            
            'required' => 1
        ),
        Array(
            'name' => 'state',     
            'title' => 'State/Providence',            
            'required' => 1
        ),
        Array(
            'name' => 'zip',       
            'title' => 'Zip/Postal Code',  
            'required' => 1
        ),
        Array(
            'name' => 'country',   
            'title' => 'Country', 
            'required' => 0
        ),
        Array(
            'name' => 'phone',     
            'title' => 'Phone',           
            'required' => 1,
            'validator' => 'phoneIsValid'
        )
    );

include('validationFuncs.php');
include('mailFuncs.php');
include('userFuncs.php');

// This should give the location of the file that lists all of the
// title and thumbnails of the items that can be downloaded
// this file must be readable by the web server user
// this file should be in a format of "logicalName\t title\t thumbnailLocationRelativeToBrowser\n"
define( 'FILELIST',  './filesList.txt');

// This file is where the files listed in the file above can be found
// If a file is only here and not in the file above it will not be listed
define( 'FILEDIR',   './manuals'); 

// This is the delimiter for the file above
define( 'DELIM',     "\t"); 


// This is the subject line of emails sent
define( 'EMAILTITLE', 'Files Downloaded');


// initializing global variables

// content is global so that it can be written to and functions can display 
// messages without having to use print and preempting things like cookies 
// and other header modifications
$content = "";

$download_message="";

// this is the title of the page
$title   = "";

// this is the registration level of the current session, it 
// is global so that various functions can easily tell if they should 
// be allowing access to certain things
$registered = registered();

define ('BODY', <<<EOM
Hello,

Recently %FIRSTNAME% %LASTNAME% from %COMPANY% downloaded several files.  Their contact information is:
%FIRSTNAME% %LASTNAME%
%TITLE%
%COMPANY%
%ADDRESS1%
%ADDRESS2%
%CITY%, %STATE% %ZIP%
%PHONE%
%EMAIL%

They downloaded the files:
%DOWNLOADED%
EOM
);

// main script fork, if the user is trying to download something and
// can then allow them to do so, if not prompt them with the normal 
// interface and inform them they cannot download that
if(!isset($_GET['download']) || ($registered != 'bot' && $registered != 'registered') ||!isset($_GET['file']) )
{
    if(($registered != 'bot' || $registered != 'registered') && isset($_GET['download']))
    {
        $error = "Sorry, you must first provide us with contact information"
               . " to download files.";
    }
    generateForm();
}
else
{
    // it is vital not to write to the output when doDownload is being called, 
    // doing so will corrupt what the user is downloading
    doDownload($_GET['file']);
}


/****
 * @func chosen
 * @desc returns true if an item has been chosen to be downloaded
 ****/
function chosen()
{
    if(count(chosenList()) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/******
 * @func chosenHTMLList
 * @desc Generates an HTML unorderd list of links from the passed in array
 * @param $list is an array of elements you wish to generate HTML for
 ******/
function chosenHTMLList($list)
{
    $retVal = "<ul>";
    foreach($list as $item)
    {
        $retVal .= "<li><a href='" . $_SERVER['SCRIPT_NAME']
                 . "?page=downloadCenter&download=true&noTemplate=true&file=" . $item['escapedName']
                 . "'>" . $item['title'] . "</a></li>";
    }
    return $retVal . "</ul>";
}

/*******
 * @func chosenList
 * @desc Generates a list of items chosen according to POST data
 *******/
function chosenList()
{
    $list = Array();
    $files = generateList();

    foreach( array_keys($_POST) as $item)
    {
        if(isset($files[$item]))
        {
            $list[$item] = $files[$item];
        }
    }
    return $list;
}

function extendedContent($user, $body)
{
    $body = makeEmailContent($user, $body);
    $list = '';
    foreach(chosenList() as $item)
    {
        $list .= $item['title'];
    }
    $body = str_replace("%DOWNLOADED%", $list, $body);
    return $body;
}

/******
 * @desc This function will write the POSTed user data to the file
 *       defined by the global constant USERSFILE
 *****/
function storePostData()
{
    global $fields;
    // make sure we have write access
    if( is_writeable(USERSFILE) )
    {
        $stringToWrite = '';
        foreach( $fields as $field)
        {
            $stringToWrite .= $_POST[$field['name']] . "\t";
        }
        $stringToWrite .= "\n";

        // append string to end of file
        $handle = fopen(USERSFILE, 'a');
        fwrite($handle, $stringToWrite);
        fclose($handle);
    }
}

/****
 * @TODO complete this function
 ****/
function isBot()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];
    return stristr($ua, "bot") || stristr($ua, "msn") || stristr($ua, "Slurp") || stristr($ua, "google");
}

/***
 * @desc This function uses post data, cookies, and user agent string
 *       to determine if the user has registered and needs to or if
 *       it has exempt status
 * @returns 'registered' if the user is registered
 *          'not' if the user is not registered
 *          'bot' if the user is a bot and has exempt status
 *****/
function registered()
{
    // if the user has a user cookie then we have already processed
    // them
    if(isset($_COOKIE['user']))
    {
        return 'registered';
    }
    // if the user has an email address already in POST
    // then they are waiting to be processed
    else if(isset($_POST['email']))
    {
        if(postIsValid())
        {
            storePostData();
            setcookie('user', $_POST['email'], time()+31556926);
            return 'registered';
        }
        else
        {
            return 'failed';
        }
    }
    else if(isBot())
    {
        return 'bot';
    }
    return 'not';
}

/******
 * This function is used to generate an array of hashes
 * each hash represents one item from the FILELIST file
 * that is available for download
 * Each hash contains a 'title', 'name' which is the
 * logical location of the file relative to this file,
 * and 'thumbnail' which is the location of the thumbnail
 * relative to the user
 ***/
function generateList()
{
    $files = Array();
    if(is_file(FILELIST))
    {
        foreach(file(FILELIST) as $line)
        {
            $value = explode(DELIM, $line);  
            $escapedName = escapeName($value[0]);
            $files[$escapedName] = Array(
                                    "name" => $value[0], 
                                    "title" => $value[1], 
                                    "thumbnail" => (isset($value[2]) ? $value[2] : "")
                                    );
            $files[$escapedName]['escapedName'] =  $escapedName;
        }
    }
    if(is_dir(FILEDIR))
    {
        foreach( scandir(FILEDIR) as $key)
        {
            $key = escapeName($key);
            if( !isset($files[$key]) )
            {
                unset($files[$key]);
            }
        }
    }

    return $files;
}

/*****
 * This function escapes names for various purposes, it should not be used for
 * database or system operations, or anything of that nature
 *****/
function escapeName($val)
{
    return preg_replace("/[><. ]/", "_", $val);
}

/***
 * @desc this function will generate the HTML list that can
 *        be displayed on a page of items that can be downloaded
 *        according to the generateList function
 ***/
function generateHTMLList()
{
    $retVal = "<h5>File Selection</h5><ul id='fileList'>";

    $files = generateList();

    foreach($files as $item)
    {
        $retVal .= "<li>";
        if($item["thumbnail"])
        {
            $retVal .= "<img src=" . $item["thumbnail"] . "/>";
        }
        $retVal .= "<span class='itemTitle'><input type='checkbox' value='true' name='" . $item['escapedName'] . "' id='" . $item['escapedName'] . "'/><label for='" . $item['escapedName'] . "'>" . $item['title'] . "</label></span></li>";
    }
    return $retVal . "</ul>";
}

/***
 * This will generate the form according to the current state the user is in
 **/
function generateForm()
{
    global $error;
    global $content;
    global $fields;
    global $registered;
    global $user;

    global $download_message; 
    switch($registered)
    {
        // the user failed in attempt to login
        case 'failed':
        {

            $error .= "Information invalid, please try again.<br/>";
	    $download_message = $error;	
	}
        // the user is not registered
        case 'not':
        {
            $title = "Download Center";
            $content .= <<<EOT
            <h2>Registration Information</h2>
            <table>

EOT;
            foreach( $fields as $field)
            {
                $content .= "<tr><td><label class='person' id='${field['name']}' for='${field['name']}'>${field['title']} " 
                         . ($field['required'] ? "*" : "") . "</label></td><td><input"
                         . " type='text' name='${field['name']}'"
                         . " value='" . (isset($_POST[$field['name']]) ? $_POST[$field['name']] : '' ) 
                         . "'/></td></tr>";
            }
            $content .= <<<EOT
            </table>
            <p>
            *Required
            
            </p>

EOT;
            $content .= generateHTMLList();
            break;
        }
        // the user is registered
        case 'registered':
        {
            $title = "Download Center";
            if(chosen())
            {
                // and has chosen some files
               
                //sendMailWithAttachment();
                //$content .= chosenHTMLList(chosenList());
               
                $count .= getEmailAndAttachment();
                
                $content .= "Requested information has been sent to your email(" . $count .")";
                $download_message = "Requested information has been sent to your email(" . $count .")"; 
                 sendMail(EMAILFROM, extendedContent($user, BODY), EMAILTO, EMAILTITLE);
            }
            else {
		$download_message="No Files Selected";
	    } 
	    $content .= generateHTMLList();

	    break;
        }
        // the user is a bot, just show them all the links
        case 'bot':
        {
            $content .= chosenHTMLList(generateList());
        }
    
}

$url = "Location:./index.html?download_message=" . $download_message;
header($url);
} // end of function
function getEmailAndAttachment()
{
    $email;
    $file;
    $count;
    
	if(isset($_COOKIE['user']))
	{
		$email .= $_COOKIE['user'];
	}
	else
	{
		if( isset( $_POST['email'] ) ) 
		{
			$email .= $_POST['email'];
		}
	}

	$list = chosenList();
	foreach($list as $item)
	{
		$file = $item["name"];
		if( isset($file) && isset($email) )
		{
		   $content = sendMailWithAttachment($email, $file);
		   if( $content == "Success" )
		   { 
		      $count += 1;
		   }
		}
	}
	return $count;
}

function sendMailWithAttachment($email, $file)
{
    $status = "";
    
    $fileatt =  FILEDIR . '/' . $file;
	$fileatt_type = "application/pdf"; // File Type
	$fileatt_name = $file; // Filename that will be used for the file as the attachment
	$email_from = "automailer@beckautomation.com"; // Who the email is from
	$email_subject = "Your Requested File from Beck Automation"; // The Subject of the email
	$email_message = "Thanks for visiting"; // Message that the email has in it
	$email_to = $email; // Who the email is to
	$headers = "From: ".$email_from;
	$file = fopen($fileatt,'rb');
	$data = fread($file,filesize($fileatt));
	fclose($file);
	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
	$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
	$email_message .= "This is a multi-part message in MIME format.\n\n" .	"--{$mime_boundary}\n" . "Content-Type:text/html; charset=\"iso-8859-1\"\n" .	"Content-Transfer-Encoding: 7bit\n\n";
		 
	$email_message .= "\n\n";
	$data = chunk_split(base64_encode($data));
	$email_message .= "--{$mime_boundary}\n" .
	"Content-Type: {$fileatt_type};\n" .
	" name=\"{$fileatt_name}\"\n" .
	//"Content-Disposition: attachment;\n" .
	//" filename=\"{$fileatt_name}\"\n" .
	"Content-Transfer-Encoding: base64\n\n" .
	$data .= "\n\n" .
	"--{$mime_boundary}--\n";
	
	$sent = @mail($email_to, $email_subject, $email_message, $headers);
	
	if($sent) {
		$status = "Success";
	} else {
		$status = "Fail";
	}	
	return $status;
}

/*****
 * This function attempts to guess the content type according to the suffix
 * it only tries doc, pdf, and s19, everything else is assumed to be
 * text
 *****/
function getContentType($file)
{
    $type ='';

    // guess the content type based on the suffix
    $suffix = strrchr($file, ".");
    switch($suffix)
    {
    case '.pdf':
        $type = "application/pdf";
        break;
    case '.doc':
        $type = "application/msword";
        break;
    case '.s19':
    default:
        $type = "text/plain";
    }
    return $type;
}

/****
 * doDownload performs the actual act of handing the file to the user
 ***/
function doDownload($escFileName)
{
    global $error;
    $files = generateList();
    if($files[$escFileName])
    {

        $fileName = $files[$escFileName]['name'];
        $file = FILEDIR . '/' . $fileName;
        header('Content-type: ' . getContentType($fileName));
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        readfile($file);
    }
    else
    {
        // couldn't find the file, don't fret just apologize
        // though, this really shouldn't happen 
        $error = "Sorry, the requested file was not found.";
        generateForm();
    }
}

/**
 * Generates the current userdata
 **/
?>
