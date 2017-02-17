
<?php

$old_path = getcwd();
chdir('/vagrant/bistorm');
$output = shell_exec('./ffmpeg_kill /var/www/hls');
chdir($old_path);
echo "<pre>$output</pre>";

?>
<a href="../../bistorm/ffmpeg_kill"></a>