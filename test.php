<?php
echo "hello world";
file_put_contents('test.txt', 'testing testing');
$s = file_get_contents('test.txt');
echo "\n";
echo $s;
