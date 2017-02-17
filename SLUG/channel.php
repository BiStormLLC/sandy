<?php
include 'common.php';
$old_path = getcwd();
chdir('/vagrant/bistorm/hdhomerun');
ini_set('max_execution_time', 5);
$output = shell_exec("./channel " . getChannel());
echo $output;
?>