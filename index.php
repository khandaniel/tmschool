<script src="./assets/clipboard.js/dist/clipboard.min.js" type="text/javascript"></script>
<style type="text/css">
	textarea.form {width: 200px; height:100px;}
	button.form {width: 200px;}
	h1 > a {text-decoration: none;}
    .login:before, .login >* {display:inline-block; vertical-align:middle;}
    .login:before {content:"";  height:100%;}
    .login {text-align: center;}
    .login a {text-decoration: none;font-family: 'Roboto',sans-serif; font-size: 24px; font-weight: 400; line-height: 32px; color: #4285f4;}
    .login img {width: 24px; height: 24px; padding-top: 2px;}
    .logout {display:inline;text-align: center;}
    .logout a {text-decoration: none;font-family: 'Roboto',sans-serif; font-size: 16px; font-weight: 400; line-height: 32px; color: #4285f4;}
    html, body {height: 100%;}
    html {display: table;margin: auto;}
    body {display: table-cell;vertical-align: top;}
</style>
<?php
require_once './vendor/autoload.php';
require_once 'model/login.php';
error_reporting(0);
session_start();
dropClient(isset($_GET['logout']));
$client = getClient();
$service = new Google_Service_Sheets($client);
$spreadsheetId = '1IA_4q1_oHL3PQAeq1E9wcCKdPZyj5cznxBeMNMCsdVo';
$range = '\'2018\'!A1:BE28';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$sheet = $response->getValues();

if(count($sheet) == 0) {
    print "No data found.\n";
} else {
    $week = defineWeek();
    $talks = getTalks($sheet, $week);
    slideWeeks($week);
    printOut($talks, $week);
}
?>
<span class="logout"><a href="?logout">Logout</a></span><br />
<script type="text/javascript">
var clipboard = new Clipboard('.btn_reader');
var clipboard = new Clipboard('.btn_first_call');
var clipboard = new Clipboard('.btn_second_call');
var clipboard = new Clipboard('.btn_third_talk');
</script>
