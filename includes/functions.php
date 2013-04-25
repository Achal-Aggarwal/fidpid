<?php

function strip_zeros_from_date( $marked_string="" ) {
  // first remove the marked zeros
  $no_zeros = str_replace('*0', '', $marked_string);
  // then remove any remaining marks
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit();
  }
}

function output_message($message="") {
  if (!empty($message)) { 
    return "<p class=\"message\">{$message}</p>";
  } else {
    return "";
  }
}

function __autoload($class_name) {
	$class_name = strtolower($class_name);
  $path = LIB_PATH.DS."{$class_name}.php";
  if(file_exists($path)) {
    require_once($path);
  } else {
		die("The file {$class_name}.php could not be found.");
	}
}

function include_layout_template($template="") {
	include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

function log_action($action, $message="") {
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
  if($handle = fopen($logfile, 'a')) { // append
    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$content = "{$timestamp} | {$action}: {$message}".PHP_EOL;
    fwrite($handle, $content);
    fclose($handle);
    if($new) { chmod($logfile, 0755); }
  } else {
    echo "Could not open log file for writing.";
  }
}

function datetime_to_text($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

/*
 * Takes a unix timestamp and returns a relative time string such as "3 minutes ago",
 *   "2 months from now", "1 year ago", etc
 * The detailLevel parameter indicates the amount of detail. The examples above are
 * with detail level 1. With detail level 2, the output might be like "3 minutes 20
 *   seconds ago", "2 years 1 month from now", etc.
 * With detail level 3, the output might be like "5 hours 3 minutes 20 seconds ago",
 *   "2 years 1 month 2 weeks from now", etc.
 */
function nicetime($timestamp, $detailLevel = 1) {

  $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
  $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

  $now = time();
  // check validity of date
  if(empty($timestamp)) {
    return "Unknown time";
  }

  // is it future date or past date
  if($now > $timestamp) {
    $difference = $now - $timestamp;
    $tense = "ago";

  } else {
    $difference = $timestamp - $now;
    $tense = "from now";
  }

  if ($difference == 0) {
    return "1 second ago";
  }
  $remainders = array();

  for($j = 0; $j < count($lengths); $j++) {
    $remainders[$j] = floor(fmod($difference, $lengths[$j]));
    $difference = floor($difference / $lengths[$j]);
  }

  $difference = round($difference);
  
  $remainders[] = $difference;

  $string = "";

  for ($i = count($remainders) - 1; $i >= 0; $i--) {
    if ($remainders[$i]) {
      $string .= $remainders[$i] . " " . $periods[$i];

      if($remainders[$i] != 1) {
        $string .= "s";
      }

      $string .= " ";

      $detailLevel--;

      if ($detailLevel <= 0) {
        break;
      }
    }
  }

  return $string . $tense;

}


function rand_string( $length ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
  $str = "";
  $size = strlen( $chars );
  for( $i = 0; $i < $length; $i++ ) {
    $str .= $chars[ rand( 0, $size - 1 ) ];
  }

  return $str;
}


function sendMailTo($to, $body ,$subject){
  $mail = new PHPMailer(true); //New instance, with exceptions enabled

  $body             = preg_replace('/\\\\/','', $body); //Strip backslashes

 
  
  $mail->From       = "134u09@gmail.com";
  $mail->FromName   = "Achal Aggarwal";
  

  $mail->AddAddress($to);

  $mail->Subject  = $subject;

  $mail->WordWrap   = 80; // set word wrap

  $mail->MsgHTML($body);

  $mail->IsHTML(true); // send as HTML

  if($mail->Send())return true;
  else return false;
}

function checkDateTime($data) {
    if (date('m-d-Y', strtotime($data)) == $data) {
        return true;
    } else {
        return false;
    }
}
?>