<?php
header("Access-Control-Allow-Origin: *");
$upd = file_get_contents('php://input');
$upar = json_decode($upd, true);
if (isset($upar["inline_query"]["location"])){
	$long = $upar["inline_query"]["location"]["longitude"];
	$lat = $upar["inline_query"]["location"]["latitude"];
	$tz = json_decode(file_get_contents("http://api.geonames.org/timezoneJSON?lat=$lat&lng=$long&username=yoilyl"), true);
	$timezone = $tz['timezoneId'];
	$date = date_create(substr($tz['time'],0,10));
	$dater = date_format($date, 'm/d/Y');
	$res = file_get_contents("http://db.ou.org/zmanim/getCalendarData.php?mode=day&timezone=$timezone&dateBegin=$dater&lat=$lat&lng=$long");
	$res = json_decode($res, true);
	if (!isset($res['error'])){
	  $name = array(
      'alos_ma' => 'עלות מוקדם',
      'talis_ma' => 'משיכיר מג"א',
      'sunrise' => 'נץ',
      'sof_zman_shema_ma' => 'סוזק"ש מג"א',
      'sof_zman_shema_gra' => 'סוזק"ש גר"א',
      'sof_zman_tefila_ma' => 'סו"ז תפילה מג"א',
      'sof_zman_tefila_gra' => 'סו"ז תפילה גר"א',
      'chatzos' => 'חצות היום',
      'mincha_gedola_ma' => 'מנחה גדולה',
      'mincha_ketana_gra' => 'מנחה קטנה',
      'plag_mincha_ma' => 'פלג המנחה',
      'sunset' => 'שקיעה',
      'tzeis_42_minutes' => 'צאת הכוכבים',
      'tzeis_72_minutes' => 'לילה לר"ת',
    );
    $pln =json_decode( @file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&key=AIzaSyC7TgALDx_GjefjTvIzYR_5Q71sOVBylTg&result_type=political"), true);
    $place = $pln['results'][0]['formatted_address'];
    $qid = $upar['inline_query']['id'];
    $date = date_timestamp_get($date);
    $dateheb = jdtojewish(gregoriantojd(date('m', $date), date('d', $date), date('Y', $date)), true, CAL_JEWISH_ADD_GERESHAYIM );
    $dateheb =  iconv ('WINDOWS-1255', 'UTF-8', $dateheb);
    $dayar = array('זונטאג','מאנטאג','דינסטאג','מיטוואך','דאנערשטאג','פרייטאג','שבת קודש');
    $day = $dayar[date('w', $date)];
    $jpg = file_get_contents("https://maps.googleapis.com/maps/api/staticmap?scale=2&format=jpg&center=$lat,$long&zoom=14&size=640x400&key=AIzaSyC7TgALDx_GjefjTvIzYR_5Q71sOVBylTg");
    file_put_contents("$lat-$long.jpeg", $jpg);
    $message= "זמנים פון $day $dateheb\n$place\n\n";
    foreach ($name as $key => $zman){
        $time = date('g:i a', strtotime($res['zmanim'][$key]));
        $message.= "$time : $zman\n";
        }
     header('Content-Type: application/json');
	 echo json_encode(array(
    'method'=>'answerInlineQuery',
    'inline_query_id'=>$qid,
    'is_personal'=> true,
    'results'=> array(array(
	    'type'=>'location',
		'id'=> 'zmanim',
		'latitude'=> $lat,
		'longitude'=> $long,
		'title'=> "Zmanim for: $place",
		'input_message_content'=>array(
			'message_text'=> $message."[‌](https://jmusicbot-jmusic.c9users.io/$lat-$long.jpeg)",
			'parse_mode'=> 'markdown')
	    ))
    ));
  
	}}
