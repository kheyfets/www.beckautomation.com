<?php


$fields = Array(
        Array(
            'name' => 'name',
            'title' => 'Name',
            'required' => 1,
        ),
        Array(
            'name' => 'phone',
            'title' => 'Phone Number',
            'required' => 1,
            'validator' => 'phoneIsValid'
        ),
        Array(
            'name' => 'referrer',
        )
    );

include('validationFuncs.php');
include('mailFuncs.php');
include('userFuncs.php');

define( 'BODY', <<<EOM
Hello,

Please call %NAME% for a sales call at the number %PHONE%.

The user decided to request a phone call after coming from this site:
%REFERRER%
EOM
);

$contact_you_content = 0;
if(isset($_POST['name']) || isset($_POST['phone']))
{
    if(postIsValid())
    {
        $contact_you_content = handle_form();
    }
    else
    {
        $error .= "Information invalid, please try again.<br/>";
        $contact_you_content = generate_form();
    }
}
else
{
    $contact_you_content = generate_form();
}

function extendedEmailContent($user, $body)
{
    $content = makeEmailContent($user, $body);
    $content .= "All other data about the person requesting the call is below\n";
    $content .= print_r($user, true);
    return $content;
}

function handle_form()
{
    global $user;

    sendMail( EMAILFROM, extendedEmailContent($user, BODY), EMAILTO, 'Sales Call Request');
    return "Thank you, a representative from Beck Automation should call you soon.";
}

function generate_form()
{
    global $error;
    $script = $_SERVER['SCRIPT_NAME'];
    $message = "Simply provide us with a phone number and a name and we will call you back as soon as we can.";
    $message = ($error == '' ? $message : '');
    $referrer = $_SERVER['HTTP_REFERER'];

    return <<<EOF
    <p style="width: 50%">
    $message
    </p>
    <p>
    <span class="error">$error</span>
    </p>
    <form action="$script?page=sales" method="post">
        <table>
            <tr>
                <td>
                    <label for="name">Name:</label>
                </td>
                <td>
                    <input name="name" id="name"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="phone">Phone Number (area code first):</label>
                </td>
                <td>
                    <input name="phone" id="phone"/>
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                    <input type="hidden" name="referrer" value="$referrer"/>
                    <input type="submit" value="Call me"/>
                </td>
            </tr>
        </table>
    </form>
EOF
    ;
}

?>
<div class="item">
        <h3>Let Us Contact You</h3>
        <?php print($contact_you_content) ?>
</div>
<div class="item" id="addressInfo" style="height: 150pt;">
        <h3>Or, You can Contact Us</h3>
        Phone: 314.576.9736<br/>
        Fax:   314.227.2104 <br/>
        <br/>
        <a href="mailto:sales@BeckAutomation.com">sales@BeckAutomation.com</a><br/>
        <br/>
        Beck Automation, LLC.<br/>
        814 Fee Fee Rd.<br/>
        St. Louis, MO 63043<br/>
</div>
