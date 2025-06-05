<?php 


$server = "localhost";
$db ="mateatrj_bamV2";
$user ="mateatrj_root";
$pass ="Bam2024$#@";

$connection = mysqli_connect($server,$user, $pass,$db); 

if($connection) {
    echo 'connected!';
}
?>