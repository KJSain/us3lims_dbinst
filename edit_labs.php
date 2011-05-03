<?php
/*
 * edit_labs.php
 *
 * A place to edit/update the lab table
 *
 */
session_start();

// Are we authorized to view this page?
if ( ! isset($_SESSION['id']) )
{
  header('Location: index.php');
  exit();
} 

if ( ( $_SESSION['userlevel'] != 3 ) &&
     ( $_SESSION['userlevel'] != 4 ) &&
     ( $_SESSION['userlevel'] != 5 ) )  // super, admin and superadmin only
{
  header('Location: index.php');
  exit();
}

include 'config.php';
include 'db.php';
include 'lib/utility.php';

// Are we being directed here from a push button?
if (isset($_POST['prior']))
{
  do_prior();
  exit();
}

else if (isset($_POST['next']))
{
  do_next();
  exit();
}

else if (isset($_POST['new']))
{
  do_new();
  exit();
}

// Are we being directed here from a push button?
if (isset($_POST['update']))
{
  do_update();
  exit();
}

// Start displaying page
$page_title = 'Edit Labs';
$js = 'js/edit_labs.js';
include 'top.php';
include 'links.php';

?>
<!-- Begin page content -->
<div id='content'>

  <h1 class="title">Edit Labs</h1>
  <!-- Place page content here -->

<?php
// Edit or display a record
if ( isset($_POST['edit']) || isset($_GET['edit']) )
  edit_record();

else
  display_record();

?>
</div>

<?php
include 'bottom.php';
exit();

// Function to redirect to prior record
function do_prior()
{
  $labID = $_POST['labID'];

  $query  = "SELECT labID FROM lab " .
            "ORDER BY name ";
  $result = mysql_query($query)
      or die("Query failed : $query<br />\n" . mysql_error());

  // Find prior record
  $current = null;
  list($current) = mysql_fetch_array($result);
  while ($current != null && $labID != $current)
  {
    $prior = $current;
    list($current) = mysql_fetch_array($result);
  }

  $redirect = ($prior == null) ? "" : "?ID=$prior";
  header("Location: $_SERVER[PHP_SELF]$redirect");
  exit();
}

// Function to redirect to next record
function do_next()
{
  $labID = $_POST['labID'];

  $query  = "SELECT labID FROM lab " .
            "ORDER BY name ";
  $result = mysql_query($query)
      or die("Query failed : $query<br />\n" . mysql_error());

  // Find next record
  $next = null;
  while ($labID != $next)
    list($next) = mysql_fetch_array($result);
  list($next) = mysql_fetch_array($result);

  $redirect = ($next == null) ? "?ID=$labID" : "?ID=$next";
  header("Location: $_SERVER[PHP_SELF]$redirect");
  exit();
}

// Function to create a new record
function do_new()
{
  $uuid = uuid();

  // Insert an ID
  $query = "INSERT INTO lab ( labGUID ) " .
           "VALUES (' $uuid' ) ";
  mysql_query($query)
    or die("Query failed : $query<br />\n" . mysql_error());
  $new = mysql_insert_id();

  header("Location: $_SERVER[PHP_SELF]?edit=$new");
  exit();
}

// Function to update the current record
function do_update()
{
  $labID       =                         $_POST['labID'];
  $name        = addslashes(htmlentities($_POST['name']));
  $building    = addslashes(htmlentities($_POST['building']));
  $room        = addslashes(htmlentities($_POST['room']));
  $query = "UPDATE lab " .
           "SET name  = '$name', " .
           "building  = '$building', " .
           "room  = '$room', " .
           "dateUpdated  = NOW() " .
           "WHERE labID = $labID ";

  mysql_query($query)
    or die("Query failed : $query<br />\n" . mysql_error());

  header("Location: $_SERVER[PHP_SELF]?ID=$labID");
  exit();
}

// Function to display and navigate records
function display_record()
{
  // Find a record to display
  $labID = get_id();
  if ($labID === false)
    return;

  $query  = "SELECT name, building, room " .
            "FROM lab " .
            "WHERE labID = $labID ";
  $result = mysql_query($query) 
            or die("Query failed : $query<br />\n" . mysql_error());

  $row    = mysql_fetch_array($result, MYSQL_ASSOC);

  // Create local variables; make sure IE displays empty cells properly
  foreach ($row as $key => $value)
  {
    $$key = (empty($value)) ? "&nbsp;" : html_entity_decode( stripslashes( nl2br($value) ) );
  }

  // Populate a list box to allow user to jump to another record
  $nav_listbox =  "<select name='nav_box' id='nav_box' " .
                  "        onchange='get_lab(this);' >" .
                  "  <option value='null'>None selected...</option>\n";
  $query  = "SELECT labID, name FROM lab ORDER BY name";
  $result = mysql_query($query)
            or die("Query failed : $query<br />\n" . mysql_error());
  while (list($t_id, $t_something) = mysql_fetch_array($result))
  {
    $selected = ($labID == $t_id) ? " selected='selected'" : "";
    $nav_listbox .= "  <option$selected value='$t_id'>$t_something</option>\n";
  }
  $nav_listbox .= "</select>\n";

echo<<<HTML
  <form action="{$_SERVER['PHP_SELF']}" method='post'>
  <table cellspacing='0' cellpadding='10' class='style1'>
    <thead>
      <tr><th colspan='8'>Edit Labs</th></tr>
    </thead>
    <tfoot>
      <tr><td colspan='2'>Jump to: $nav_listbox
                          <input type='submit' name='prior' value='&lt;' />
                          <input type='submit' name='next' value='&gt;' />
                          <input type='submit' name='new' value='New' />
                          <input type='submit' name='edit' value='Edit' />
                          <input type='hidden' name='labID' value='$labID' />
          </td></tr>
    </tfoot>
    <tbody>
      <tr><th>Name:</th>
          <td>$name</td></tr>
      <tr><th>Building:</th>
          <td>$building</td></tr>
      <tr><th>Room:</th>
          <td>$room</td></tr>
    </tbody>
  </table>
  </form>

HTML;
}

// Function to figure out which record to display
function get_id()
{
  // See if we are being directed to a particular record
  if (isset($_GET['ID']))
    return( $_GET['ID'] );

  // We don't know which record, so just find the first one
  $query  = "SELECT labID FROM lab " .
            "ORDER BY name " .
            "LIMIT 1 ";
  $result = mysql_query($query)
      or die("Query failed : $query<br />\n" . mysql_error());

  if (mysql_num_rows($result) == 1)
  {
    list($labID) = mysql_fetch_array($result);
    return( $labID );
  }

  // If we're here, there aren't any records
echo<<<HTML
  <form action='{$_SERVER[PHP_SELF]}' method='post'>
  <table cellspacing='0' cellpadding='10' class='style1'>
    <thead>
      <tr><th colspan='2'>Edit Labs</th></tr>
    </thead>
    <tfoot>
      <tr><td colspan='2'><input type='submit' name='new' value='New' />
          </td></tr>
    </tfoot>
    <tbody>
      <tr><th>Status:</th>
          <td>There are no records to display</td></tr>
    </tbody>
  </table>
  </form>

HTML;

  return( false );
}

// Function to edit a record
function edit_record()
{
  // Get the record we need to edit
  if ( isset( $_POST['edit'] ) )
    $labID = $_POST['labID'];

  else if ( isset( $_GET['edit'] ) )
    $labID = $_GET['edit'];

  else
  {
    // How did we get here?
    echo "<p>There was a problem with the edit request.</p>\n";
    return;
  }

  $query  = "SELECT name, building, room " .
            "FROM lab " .
            "WHERE labID = $labID ";
  $result = mysql_query($query) 
            or die("Query failed : $query<br />\n" . mysql_error());

  $row = mysql_fetch_array($result);


  $name                = html_entity_decode( stripslashes( $row['name'] ) );
  $building            = html_entity_decode( stripslashes( $row['building'] ) );
  $room                = html_entity_decode( stripslashes( $row['room'] ) );

echo<<<HTML
  <form action="{$_SERVER['PHP_SELF']}" method="post">
  <table cellspacing='0' cellpadding='0' class='style1'>
    <thead>
      <tr><th colspan='8'>Edit Labs</th></tr>
    </thead>
    <tfoot>
      <tr><td colspan='2'><input type='submit' name='update' value='Update' />
                          <input type='hidden' name='labID' value='$labID' />
                          <input type='reset' /></td></tr>
    </tfoot>
    <tbody>


    <tr><th>Name:</th>
        <td><textarea name='name' rows='6' cols='65' 
                      wrap='virtual'>$name</textarea></td></tr>
    <tr><th>Building:</th>
        <td><textarea name='building' rows='6' cols='65' 
                      wrap='virtual'>$building</textarea></td></tr>
    <tr><th>Room:</th>
        <td><textarea name='room' rows='6' cols='65' 
                      wrap='virtual'>$room</textarea></td></tr>

    </tbody>
  </table>
  </form>

HTML;
}

?>