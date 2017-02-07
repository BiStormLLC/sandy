
<?php

$old_path = getcwd();
chdir('/vagrant/bistorm');
$output = shell_exec('./ffmpeg-kill /var/www/public/hlsc /var/www/public/hlsd');
chdir($old_path);
echo "<pre>$output</pre>";

?>