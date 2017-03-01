
<?php

$old_path = getcwd();
chdir('/vagrant/bistorm');
$output = shell_exec('./ffmpeg_kill /var/www/hls /var/www/dash');
chdir($old_path);
echo "<pre>$output</pre>";


