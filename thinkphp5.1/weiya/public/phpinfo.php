<?php
$conn=mysql_connect('127.0.0.1','root','msy');
if ($conn){
 echo "LNMP platform connect to mysql is successful!";
}else{
 echo "LNMP platform connect to mysql is failed!";
}
phpinfo();
?>