#!/bin/bash
mkdir /tmp/stream
raspistill --nopreview -w 1024 -h 720 -q 10 -o /tmp/stream/pic.jpg -tl 100 -t 9999999 -th 0:0:0 &
LD_LIBRARY_PATH=/usr/local/lib mjpg_streamer -i "input_file.so -f /tmp/stream -n pic.jpg" -o "output_http.so -w /usr/local/www"