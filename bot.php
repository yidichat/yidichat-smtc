<?php
$upd = json_decode(file_get_contents('php://input'), true);
$uid = $upd['message']['from']['id'];
curl_get_contents("https://api.telegram.org/bot263336681:AAF9QR9Y17jBSk5mlvcG9Vmko4o_5wY4YJw/sendChatAction?chat_id=$uid&action=typing");
$msg = urlencode($upd['message']['text']);
$name = urlencode($upd['message']['from']['first_name']);
$res = curl_get_contents("http://api.brainshop.ai/get?bid=219&key=5laVJtJPqcXzHMRe&uid=$uid-$name&msg=$msg");
$rep = urlencode(json_decode($res, true)['cnt']);
curl_get_contents("https://api.telegram.org/bot263336681:AAF9QR9Y17jBSk5mlvcG9Vmko4o_5wY4YJw/sendMessage?chat_id=$uid&parse_mode=HTML&text=$rep");
function curl_get_contents($url){
  static $ch;
  if (!$ch) $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  return curl_exec($ch);
}
