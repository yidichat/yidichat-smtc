<?php
$upd = json_decode(file_get_contents('php://input'), true);
$uid = $upd['message']['from']['id'];
file_get_contents("https://api.telegram.org/bot263336681:AAF9QR9Y17jBSk5mlvcG9Vmko4o_5wY4YJw/sendChatAction?chat_id=$uid&action=typing");
$msg = urlencode($upd['message']['text']);
$name = $upd['message']['from']['first_name'];
$res = file_get_contents("http://api.brainshop.ai/get?bid=219&key=5laVJtJPqcXzHMRe&uid=$uid-$name&msg=$msg");
$rep = json_decode($res, true)['cnt'];
file_get_contents("https://api.telegram.org/bot263336681:AAF9QR9Y17jBSk5mlvcG9Vmko4o_5wY4YJw/sendMessage?chat_id=$uid&parse_mode=HTML&text=$rep");
