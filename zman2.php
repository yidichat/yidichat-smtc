<?php
$upd = file_get_contents('php://input');
if (empty($upd))
$upd='{"update_id":249658297,"message":{"message_id":112,"from":{"id":89093089,"first_name":"Yoily","username":"YoilyL"},"chat":{"id":89093089,"first_name":"Yoily","username":"YoilyL","type":"private"},"date":1470594886,"text":"\/zman","entities":[{"type":"bot_command","offset":0,"length":5}]}}';
//$upd = '{"update_id":249657871,"message":{"message_id":9182,"from":{"id":1468917,"first_name":"Hershey","username":"Hershel"},"chat":{"id":-1001001176415,"title":"SmarTech\u00ae - ENGLISH ONLY!\u2122","username":"smartechgroup","type":"supergroup"},"date":1469699019,"text":"/zman"}}';
$upar = json_decode($upd, true);
$dateheb = $webut = '';
$map = 'https://res.cloudinary.com/yoilyl/image/upload/zmanimbot.jpg';
$api = 'https://api.telegram.org/bot255436680:AAFISQzkn8_Ynlbw3s1QEXj_lZ-V7gUz7P0/';
function zmanLoc($lat,$long, $address = ''){
    global $place ,$map, $dateheb, $webut;
    $webut = array('inline_keyboard'=>array(array(array('text'=>'Weather','callback_data'=> "w,$long,$lat"))));
    $tz = json_decode(file_get_contents("http://api.geonames.org/timezoneJSON?lat=$lat&lng=$long&username=yoilyl"), true);
	$timezone = $tz['timezoneId'];
	$date = date_create(substr($tz['time'],0,10));
	$dater = date_format($date, 'm/d/Y');
	$resu = file_get_contents("http://db.ou.org/zmanim/getCalendarData.php?mode=day&timezone=$timezone&dateBegin=$dater&lat=$lat&lng=$long");
	$res = json_decode($resu, true);
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
    if ($address==''){
    $pln =json_decode( @file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&key=AIzaSyC7TgALDx_GjefjTvIzYR_5Q71sOVBylTg&result_type=political"), true);
    $place = $pln['results'][0]['formatted_address'];
    }else{$place=$address;}
    $qid = $upar['inline_query']['id'];
    $date = date_timestamp_get($date);
    $dateheb = jdtojewish(gregoriantojd(date('m', $date), date('d', $date), date('Y', $date)), true, CAL_JEWISH_ADD_GERESHAYIM );
    $dateheb =  iconv ('WINDOWS-1255', 'UTF-8', $dateheb);
    $dayar = array('זונטאג','מאנטאג','דינסטאג','מיטוואך','דאנערשטאג','פרייטאג','שבת קודש');
    $day = $dayar[date('w', $date)];
    $jpg = json_decode(file_get_contents("https://api.cloudinary.com/v1_1/yoilyl/image/upload?upload_preset=zmanim&file=https://maps.googleapis.com/maps/api/staticmap?scale=2%26format=jpg%26center=$lat,$long%26zoom=14%26size=640x400%26key=AIzaSyC7TgALDx_GjefjTvIzYR_5Q71sOVBylTg"), true);
    //file_put_contents("$lat-$long.jpeg", $jpg);
    $message= "זמנים פון $day $dateheb\n$place\n\n";
    foreach ($name as $key => $zman){
        $time = date('g:i a', strtotime($res['zmanim'][$key]));
        $message.= "$time : $zman\n";
        }
    $map = str_replace('upload/', 'upload/l_zmanb/', $jpg['url']);
    return '*'.$message."*[‌]($map)";
	}
}
if (isset($upar['message'])){
    $chatid=$upar['message']['from']['id'];
    $msg = $upar['message']['text'];
    $mid = $upar['message']['message_id'];
    if ($msg == '/start'||$msg=='/location'||$msg == '/start location'){
        $res['method']='sendMessage';
        $res['text']="Hi {$upar['message']['from']['first_name']}, to setup your default location do one of the following:\n1) press the button below to use your current location\n2) Send me any location by pressing the paperclip and choosing location\n3) type your address/zip code/town and send it to me.";
        $res['chat_id']=$chatid;
        $res['reply_markup']=array('keyboard'=>array(array(array('text'=>'send your location','request_location'=> true))),'one_time_keyboard'=>true,'resize_keyboard'=>true);
        
    }elseif($msg=='/zman'||$msg=='Zman'){
        $users=json_decode(file_get_contents('users.json'), true);
        if(isset($users[$chatid])){
           $long=$users[$chatid]['long'];
           $lat=$users[$chatid]['lat'];
           $res['method']='sendMessage';
           $res['chat_id']=$chatid;
           $res['text']=zmanLoc($lat,$long);
           $res['parse_mode']='markdown';
           $res['reply_markup']=array('inline_keyboard'=>array(array(array('text'=>'Weather','callback_data'=> "w,$long,$lat"))));
        }else{
           $res['method']='sendMessage';
           $res['chat_id']=$chatid;
           $res['text']='Sorry, you haven\'t set your default location yet. click /location to set it.';
        }
    }elseif(isset($upar['message']['location'])){
        $users=file_get_contents('users.json');
        while(empty($users)){
            sleep(1);
            $users=file_get_contents('users.json');
        }
        $users=json_decode($users, true);
        $users[$chatid]['long']=$upar['message']['location']['longitude'];
        $users[$chatid]['lat']=$upar['message']['location']['latitude'];
        file_put_contents('users.json',json_encode($users));
        $res['method']='sendMessage';
        $res['chat_id']=$chatid;
        $res['text']="Your location was set. here are the zmanim:\n\n".zmanLoc($users[$chatid]['lat'],$users[$chatid]['long']);
        $res['parse_mode']='markdown';
        $res['reply_markup']=$webut;
    }else {
        $adr = str_ireplace(array(' ',"\n"),'+',$msg);
        $locar = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$adr&key=AIzaSyC7TgALDx_GjefjTvIzYR_5Q71sOVBylTg"), true);
        $address =  $locar['results'][0]['formatted_address'];
        if ($locar['status']=='OK'){
        $res['method']='sendLocation';
        $res['chat_id']=$chatid;
        $res['longitude']= $long = $locar['results'][0]['geometry']['location']['lng'];
        $res['latitude']= $lat = $locar['results'][0]['geometry']['location']['lat'];
        $res['reply_markup']=array('inline_keyboard'=>array(array(array('text'=>'Yes! this is the location.','callback_data'=> "l,$long,$lat")),array(array('text'=>'No, not the location.','callback_data'=> '!'))));
        $res['reply_to_message_id']=$mid;
        }else{
        $res['method']='sendMessage';
        $res['chat_id']=$chatid;
        $res['text']="Sorry, no location found for *$msg*. _Make you you typed it correctly and try again._";
        $res['parse_mode']='markdown';
        $res['reply_to_message_id']=$mid;
        }
    }
} elseif (isset($upar['callback_query'])){
    $cbid = $upar['callback_query']['id'];
    $chatid=$upar['callback_query']['from']['id'];
    $cb = $upar['callback_query']['data'];
    
    if ($cb[0]=='l'){
        $cbar = explode(',',$cb);
        $long = $cbar[1];
        $lat = $cbar[2];
        $users=file_get_contents('users.json');
        while(empty($users)){
            sleep(1);
            $users=file_get_contents('users.json');
        }
        $users=json_decode($users, true);
        $users[$chatid]['long']=$long;
        $users[$chatid]['lat']=$lat;
        file_put_contents('users.json',json_encode($users));
        file_get_contents($api."answerCallbackQuery?callback_query_id=$cbid&text=location is set");
        file_get_contents($api."editMessageReplyMarkup?chat_id=$chatid&message_id={$upar['callback_query']['message']['message_id']}");
        $res['method']='sendMessage';
        $res['chat_id']=$chatid;
        $res['text']="Your location was set. here are the zmanim:\n\n".zmanLoc($lat,$long);
        $res['parse_mode']='markdown';
        $res['reply_markup']=$webut;
    } elseif ($cb == '!'){
        file_get_contents($api."editMessageReplyMarkup?chat_id=$chatid&message_id={$upar['callback_query']['message']['message_id']}");
        $res['method']='sendMessage';
        $res['text']="To setup your location do one of the following:\n1) press the button below to use your current location\n2) Send me any location by pressing the paperclip and choosing location\n3) type your address/zip code/town and send it to me.";
        $res['chat_id']=$chatid;
    } elseif ($cb[0]=='w'){
        $cbar = explode(',',$cb);
        $long = $cbar[1];
        $lat = $cbar[2];
        $weather = file_get_contents("https://api.forecast.io/forecast/3ebe010185612b1bf0e3fd34f8712463/$lat,$long");
        $war = json_decode($weather, true);
        $wrep = "Current weather for this location:\n".$war['currently']['summary'].', '.$war['currently']['temperature']."°F\n".$war['minutely']['summary']."\n".$war['hourly']['summary']."\n".$war['daily']['summary'];
        if(isset($wrep[200])) $wrep =substr($wrep,0,197).'...';
        $res['method']='answerCallbackQuery';
        $res['callback_query_id']=$cbid;
        $res['text']=$wrep;
        $res['show_alert']=true;
    }
    
} elseif (isset($upar['inline_query'])){
    $query = $upar['inline_query']['query'];
    $qid = $upar['inline_query']['id'];
    $chatid = $upar['inline_query']['from']['id'];
    if (empty($query)){
        if (isset($upar['inline_query']['location'])){
            $long = $upar["inline_query"]["location"]["longitude"];
	        $lat = $upar["inline_query"]["location"]["latitude"];
	        $qres = zmanLoc($lat,$long);
	        $qtit = "Current location: $place";
        }else{
            $users=file_get_contents('users.json');
            while(empty($users)){
            sleep(1);
            $users=file_get_contents('users.json');
            }
            $users=json_decode($users, true);
            if (isset($users[$chatid])){
                $qres = zmanLoc($users[$chatid]['lat'],$users[$chatid]['long']);
	            $qtit = "Default location: $place";
            }else{
                $qtit = "Could not determine your location";
                $qres = $qtit."\nType your address or zip code";
            }
        }
    }else{
        $adr = str_ireplace(array(' ',"\n"),'+',$query);
        $locar = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$adr&key=AIzaSyC7TgALDx_GjefjTvIzYR_5Q71sOVBylTg"), true);
        $address =  $locar['results'][0]['formatted_address'];
        if ($locar['status']=='OK'){
            $long = $locar['results'][0]['geometry']['location']['lng'];
            $lat = $locar['results'][0]['geometry']['location']['lat'];
            $qres = zmanLoc($lat,$long, $address);
	        $qtit = "Result for $query: $address";
        }else{
            $qtit = "No location found for $query";
            $qres = $qtit."\nTry again.";
        }
        
    }
    $res['method']='answerInlineQuery';
    $res['inline_query_id'] = $qid;
    $res['switch_pm_text'] = 'Change your default location';
    $res['switch_pm_parameter'] = 'location';
    $res['is_personal'] = true;
    $res['results']= array(array(
	    'type'=>'article',
		'id'=> 'zmanim',
		'title'=> $qtit,
		'description'=>'זמנים פון '.$dateheb,
		'thumb_url'=>$map,
		'input_message_content'=>array(
			'message_text'=> $qres,
			'parse_mode'=> 'markdown')
	    ));
	if ($webut !== '') $res['results'][0]['reply_markup']= $webut;
}
header('Content-Type: application/json');
echo json_encode($res);
