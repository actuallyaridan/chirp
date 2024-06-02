<?php
$myfile = fopen("chirp.json", "w") or die("Unable to open file!");
$chirp = array("status"=>$_POST['chirpComposeText'], "user"=>"blank", "timestamp"=>time());
fwrite($myfile, json_encode($chirp));
fclose($myfile);
?>