<?php
/*
 * register.php
 *
 * Creates a new account from the information submitted
 *
 */

session_start();

include 'config.php';
include 'db.php';
include 'lib/utility.php';

// Get the information the new user posted
include 'get_user_info.php';

// Let's see if there were any error messages
if ( ! empty($message) )
{
  $_SESSION['message'] = 'You did not submit the following required information:<br/>' .
             $message;

  $_SESSION['POST'] = $_POST;
  header( "Location: newaccount.php" ); // Show the form again!
  exit(); 
}

// Ensure that the user's email address or username does not exist in the DB
$query        = "SELECT count(*) FROM people WHERE email='$email'";
$result       = mysql_query($query);
list($count)  = mysql_fetch_row($result);
 
if ( $count > 0 )
{
    $_SESSION['message'] = "Your email address is already registered.<br/>"  .
               "Please submit a different Email address "  .
               "or <a href=lost_password.php>request a new password</a> for this address.<br/>";

    $_SESSION['POST'] = $_POST;
    unset( $_SESSION['POST']['email'] );
    header( "Location: newaccount.php" ); // Show the form again!
    exit(); 
}

// Not going back
unset( $_SESSION['POST'] );

// Now we can start drawing the page
$page_title = "New Account Registration";
include 'header.php';
?>
<div id='content'>

  <h1 class="title">New Account Registration</h1>

<?php
$random_password = makeRandomPassword();
$db_password = md5($random_password);

$uuid = uuid();

// Enter info into the Database.
$query = "INSERT INTO people " .
         "SET personGUID  = '$uuid', " .
         "lname           = '$lname', " .
         "fname           = '$fname', " .
         "organization    = '$organization', " .
         "address         = '$address', " .
         "city            = '$city', " .
         "state           = '$state', " .
         "zip             = '$zip', " .
         "country         = '$country', " .
         "phone           = '$phone', " .
         "email           = '$email', " .
         "password        = '$db_password', " .
         "userlevel       = 0, " .
         "activated       = 0, " .
         "signup          = now() ";

$result = mysql_query($query) 
          or die( "Query failed : $query<br/>" . mysql_error() );
$userid = mysql_insert_id();

if ( ! $result )
{
    echo '<p>There has been an error creating your account. ' .
         'Please contact the system administrator: '          .
         "<a href='mailto:$admin_email'>"              .
         "&lt;$admin_email&gt;</a>.</p>"               .
         "</div></body></html>";
    exit();
} 

// Mail the user

$subject = "Your new $org_name Account";

$message = "Dear $fname $lname,
You have registered your $org_name account at $org_site.
You are two steps away from logging in and accessing the system.
To activate your account, please click here:

http://$org_site/activate.php?id=$userid&code=$db_password
    
Once you activate your membership, you will be able to login with the 
following information:

    E-mail Address: $email
    Password: $random_password
    
Please save this message for your reference.
Thanks!
The $org_name Admins.

This is an automated response, do not reply!";

$now = time();
$headers = "From: $org_name Admin<$admin_email>"     . "\n";

// Set the reply address
$headers .= "Reply-To: $org_name<$admin_email>"      . "\n";
$headers .= "Return-Path: $org_name<$admin_email>"   . "\n";

// Try to avoid spam filters
$headers .= "Message-ID: <" . $now . "info@" . $_SERVER['SERVER_NAME'] . ">\n";
$headers .= "X-Mailer: PHP v" . phpversion()         . "\n";
$headers .= "MIME-Version: 1.0"                      . "\n";
$headers .= "Content-Transfer-Encoding: 8bit"        . "\n";

mail($email, $subject, $message, $headers);

echo "<p>Your login information has been mailed to your email address.\n" .
     "   Please check your e-mail and follow the directions in order to \n" .
     "   activate your new account within one week.</p>\n";

?>
</div>

<?php
include 'footer.php';
?>
