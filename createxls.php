<?php require_once("includes/initialize.php"); ?>
<?php
$form = Forms::find_by_user_id($session->user_id);
$form->question=explode("$#%",$form->question);
$form->ch_lowest=explode("$#%",$form->ch_lowest);
$form->ch_low=explode("$#%",$form->ch_low);
$form->ch_average=explode("$#%",$form->ch_average);
$form->ch_high=explode("$#%",$form->ch_high);
$form->ch_highest=explode("$#%",$form->ch_highest);

$options = array();

for($i=0;$i<5;$i++){
    $options[] = array("",$form->ch_lowest[$i],$form->ch_low[$i],$form->ch_average[$i],$form->ch_high[$i],$form->ch_highest[$i]);
}

$responses = Response::find_all_by_formid($form->id,26,0,0);//26 for all responses
//Optional: print out title to top of Excel or Word file with Timestamp
//for when file was generated:
//set $Use_Titel = 1 to generate title, 0 not to use title
$Use_Title = 0;
//define date for title: EDIT this to create the time-format you need
$now_date = DATE('m-d-Y H:i');
//define title for .doc or .xls file: EDIT this if you want
$title = "Responses to your Form";

$file_type = "vnd.ms-excel";
$file_ending = "xls";

//header info for browser: determines file type ('.doc' or '.xls')
HEADER("Content-Type: application/$file_type");
HEADER("Content-Disposition: attachment; filename=feedbacks.$file_ending");
HEADER("Pragma: no-cache");
HEADER("Expires: 0");
 

     /*    FORMATTING FOR EXCEL DOCUMENTS ('.xls')   */
     //create title with timestamp:
     IF ($Use_Title == 1)
     {
         ECHO("$title\n");
     }
     //define separator (defines columns in excel & tabs in word)
     $sep = "\t"; //tabbed character
 
     
     //end of printing column names
     PRINT("Bill no".$sep."Name".$sep."Email".$sep.$form->question[0].$sep.$form->question[1].$sep.$form->question[2].$sep.$form->question[3].$sep.$form->question[4].$sep."Comment".$sep."Time\n");
     //start foreach loop to get data
     $i=0;
     foreach($responses as $resp)
     {
         $schema_insert = $resp->bill_no.$sep.$resp->name.$sep.$resp->email.$sep;
         $schema_insert .= $options[0][$resp->response_one].$sep.$options[1][$resp->response_two].$sep.$options[2][$resp->response_three].$sep.$options[3][$resp->response_four].$sep.$options[4][$resp->response_five].$sep;
         $schema_insert .= $resp->comment.$sep.strftime("%B %d, %Y at %I:%M %p", $resp->time);
         //following fix suggested by Josue (thanks, Josue!)
         //this corrects output in excel when table fields contain \n or \r
         //these two characters are now replaced with a space
         $schema_insert = PREG_REPLACE("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
         $schema_insert .= "\t";
         PRINT(TRIM($schema_insert));
         PRINT "\n";
         $i++;

     }
 
?>