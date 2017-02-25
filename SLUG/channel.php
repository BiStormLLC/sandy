<?php
include 'common.php';
$old_path = getcwd();
chdir('/vagrant/bistorm/hdhomerun');
$output = shell_exec("./channel " . getChannel());
echo $output;
?>