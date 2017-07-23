<html>

<head>
<title>Script to update details on database</title>
<base href="http://www.societies.cam.ac.uk/cgs/isw/" />
</head>

<body style="font-family: arial,sans-serif,helvetica; size: 3px;">

<?php
// Obtain variables from POST.
$form=$_POST["form"];
$name=$_POST["name"];
$from=$_POST["email"];
$headers = "From: $from";
$messagetext=$_POST["message"];
$first_name=$_POST["first_name"];
$last_name=$_POST["last_name"];
$gender=$_POST["gender"];
$church=$_POST["church"];
$college=$_POST["college"];
$department=$_POST["department"];
$year=$_POST["year"];
$email=$_POST["email"];
$phone=$_POST["phone"];
$country=$_POST["country"];
$know_arrival=$_POST["know_arrival"];
$time=$_POST["time"];
$day=$_POST["day"];
$month=$_POST["month"];
$arrival_location=$_POST["arrival_location"];
$duration=$_POST["duration"];
$visit=$_POST["visit"];
$training=$_POST["training"];
$comments=$_POST["comments"];

// Create a variable for the volunteer's name, for screen messages.
if (!$name)
{
$name=$first_name;
}
if (strlen($email)==0)
{

// Send error message if no email address is entered.
echo "
<h2>Missing Email</h2>
<p>Please enter your email address, ".$name.".</p>
<p><i>The ISW Team</i></p><p></p>
<hr>
<p><i><a href=about.html>Return to ISW home page</a></i></p>";
}
else
{

switch ($form)
{case "general":

// Display message confirmation for a general enquiry, and construct email content.
echo "
<h2>Message Confirmation</h2>
<p>Thank you for your message ".$name.".</p>
<p>We will endeavour to reply as soon as possible.</p>
<p><i>The ISW Team</i></p><p></p>
<hr>
<p><i><a href=about.html>Return to ISW home page</a></i></p>";
$subject=sprintf("ISW Website Message (General): %s",$_POST["subject"]);
$message=$messagetext;
break;
case "volunteer":

// Deal with volunteer's information, by updating the database as well as constructing the email content.
// Open the database.
$conn = db_open();

// Declare blank variables for sending details by email.
$shift_22_Sept = "";
$shift_23_Sept = "";
$shift_24_Sept = "";
$shift_25_Sept = "";
$shift_26_Sept = "";
$shift_27_Sept = "";
$shift_28_Sept = "";
$shift_29_Sept = "";
$shift_30_Sept = "";
$shift_01_Octo = "";
$shift_02_Octo = "";


// Obtain name of volunteer from database.
$query = sprintf("SELECT email, last_name, first_name FROM volunteer_info WHERE email='%s'",
mysql_real_escape_string($email));
$result = mysql_query($query);
WHILE ($row = mysql_fetch_array($result))
{
$email_database=$row['email'];
$last_name_database=$row['last_name'];
$first_name_database=$row['first_name'];
}

// Delete all entries with volunteer's name in, in availability database.
$name_database = "%".$last_name_database.", ".$first_name_database."%";
$old_name="; ".$last_name_database.", ".$first_name_database;
$query = sprintf("SELECT date_availability, hour_availability, names FROM time_availability WHERE names LIKE'%s'",
mysql_real_escape_string($name_database));
$result = mysql_query($query);
while($row = mysql_fetch_array($result))
{
$old_names=$row['names'];
$new_names=str_replace($old_name, "", $old_names);
$date_avl=$row['date_availability'];
$hour_avl=$row['hour_availability'];
$sql_avl = "UPDATE time_availability SET names = '$new_names'
WHERE `date_availability` = '$date_avl' and `hour_availability` = '$hour_avl'";
mysql_query($sql_avl);
}
if ($email_database)
{

// Update previously-entered entries if the volunteer is updating, and display message confirmation.
echo "
<h2>Message Confirmation</h2>
<p>Thank you for updating your details, ".$name.".</p>
<p></p>
<p>Until this time, you are free to <a href=webpages_volunteer/update_details.html>update your details</a> further.</p>
<p>If you wish to change your details after 31 July, you can do so by <a href=mailto:cgs.isw@gmail.com>emailing us</a>.</p>
<p><i>The ISW Team</i></p><p></p>
<hr>
<p><i><a href=about.html>Return to ISW home page</a></i></p>";
$sql = "UPDATE volunteer_info SET ";
$sql .= "first_name='$first_name', ";
$sql .= "last_name='$last_name', ";
$sql .= "gender='$gender', ";
$sql .= "church='$church', ";
$sql .= "college='$college', ";
$sql .= "department='$department', ";
$sql .= "year='$year', ";
$sql .= "email='$email', ";
$sql .= "phone='$phone', ";
$sql .= "duration='$duration', ";
$sql .= "visit='$visit', ";
$sql .= "training='$training', ";
$sql .= "comments='$comments'";
$sql .= "WHERE email='$email'";
}
else
{

// Create new entries if the volunteer is new, and display message confirmation.
echo "
<h2>Message Confirmation</h2>
<p>Thank you for volunteering for ISW, ".$name.".</p>
<p>We plan to draw-up the welcoming rota after 31 July.</p>
<p>Until this time, you are free to <a href=webpages_volunteer/update_details.html>update your details</a>.</p>
<p>If you wish to change your details after this time, you can do so by <a href=mailto:cgs.isw@gmail.com>emailing us</a>.</p>
<p><i>The ISW Team</i></p><p></p>
<hr>
<p><i><a href=about.html>Return to ISW home page</a></i></p>";
$sql = "insert into volunteer_info ";
$sql .= "values (";
$sql .= "'$first_name', ";
$sql .= "'$last_name', ";
$sql .= "'$gender', ";
$sql .= "'$church', ";
$sql .= "'$college', ";
$sql .= "'$department', ";
$sql .= "'$year', ";
$sql .= "'$email', ";
$sql .= "'$phone', ";
$sql .= "'$duration', ";
$sql .= "'$visit', ";
$sql .= "'$training', ";
$sql .= "'$comments')";
}

// Perform the query to update or insert volunteer's details.
mysql_query($sql);

// Insert new entries of volunteer's name into the availability database.
foreach($_POST as $index => $postvar)
{
if (strpos($postvar, "shift_") !== false)
{
$date_avl = substr($postvar, 6, 7);
$hour_avl = substr($postvar, 14, 5);
$emailvar = "shift_" . $date_avl;
$$emailvar .= ($$emailvar <> "" ? ",  " : "") . $hour_avl;
$sql_avl = "update time_availability set names = concat(names, '; ', '" .  ($last_name . ", " . $first_name) . "') ";
$sql_avl .= "where `date_availability` = '$date_avl' and `hour_availability` = '$hour_avl'";
mysql_query($sql_avl);
}
}

// Close the database.
db_close($conn);

// Construct email content.
$subject=sprintf("ISW Website Message (Volunteer): %s",$_POST["subject"]);
$message=sprintf("First name: %s\n
Last name: %s\n
Gender: %s\n\n
Church: %s\n
College: %s\n
Department: %s\n
Year: %s\n\n
Email: %s\n
Contact telephone number: %s\n\n
Times available on 22 Sept: %s\n
Times available on 23 Sept: %s\n
Times available on 24 Sept: %s\n
Times available on 25 Sept: %s\n
Times available on 26 Sept: %s\n
Times available on 27 Sept: %s\n
Times available on 28 Sept: %s\n
Times available on 29 Sept: %s\n
Times available on 30 Sept: %s\n
Times available on 01 Oct: %s\n
Times available on 02 Oct: %s\n
Maximum time willing to help over whole period: %s\n
Whether volunteer is willing to visit: %s\n
Whether volunteer is interested in training: %s\n\n
Comments: %s\n
",$first_name,$last_name,$gender,$church,$college,$department,$year,$email,$phone,$shift_22_Sept,$shift_23_Sept,$shift_24_Sept,$shift_25_Sept,$shift_26_Sept,$shift_27_Sept,$shift_28_Sept,$shift_29_Sept,$shift_30_Sept,$shift_01_Octo,$shift_02_Octo,$duration,$visit,$training,$comments);        
break;
case "arrival":

// Display message confirmation for a new arrival, and construct email content.
echo "
<p>We hope you're looking forward to coming to Cambridge, UK, ".$name."!</p>
<p>We will endeavour to reply to your message shortly.</p>
<p><i>The ISW Team</i></p><p></p>
<hr>
<p><i><a href=about.html>Return to ISW website</a></i></p>";
$subject=sprintf("ISW Website Message (Arrival): %s at %s on %s %s at %s",$name,$time,$day,$month,$arrival_location);
$message=sprintf("
Name: %s\n
Email: %s\n
College: %s\n
Home country: %s\n\n
Knows when arriving: %s\n
Arrival time: %s\n
Arrival date: %s %s\n
Arrival location: %s\n\n
Message: %s\n
",$name,$email,$college,$country,$know_arrival,$time,$day,$month,$arrival_location,$messagetext);
break;
default:

// Display default message if no form is sent (this should not happen).
$subject=sprintf("ISW Website Message (Default): %s",$_POST["subject"]);}
$to="cgs.isw@gmail.com";

// Email information.
mail($to,$subject,$message,$headers);
}
?>

</body>

</html>

<?php
// Function for opening the database.
function db_open()
{
@$conn = mysql_connect('127.0.0.1', 'rgl27', 'llavendar');
if (!$conn)
{
die('Could not connect: ' . mysql_error());
exit;
}
mysql_select_db('cucgs', $conn);
return $conn;
}

// Function for closing the database.
function db_close($conn)
{
mysql_close($conn);
}
?>
