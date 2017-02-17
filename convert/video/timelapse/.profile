p_demux="1"
p_custom_string=" -r 10 -vcodec libx264 -crf 18 -preset fast -filter:v 'setpts=0.10*PTS' -profile:v 'baseline' -f mp4"
p_fileext="mp4"