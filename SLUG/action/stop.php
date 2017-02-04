
<?php

$old_path = getcwd();
chdir('/vagrant/bistorm');
$output = shell_exec('./ffmpeg-kill');
chdir($old_path);
echo "<pre>$output</pre>";

?>