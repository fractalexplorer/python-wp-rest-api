mode_wise.py
==============
Make sure you are using python 3+
install twython module using "pip install twython"
Run program using : python mode_wise.py 


To setup live Streaming execute following commands : 
=========================================================

$ sudo apt-get install libjpeg8-dev imagemagick libv4l-dev
$ sudo ln -s /usr/include/linux/videodev2.h /usr/include/linux/videodev.h
$ wget http://sourceforge.net/code-snapshots/svn/m/mj/mjpg-streamer/code/mjpg-streamer-code-182.zip
$ unzip mjpg-streamer-code-182.zip
$ cd mjpg-streamer-code-182/mjpg-streamer
$ make mjpg_streamer input_file.so output_http.so
$ sudo cp mjpg_streamer /usr/local/bin
$ sudo cp output_http.so input_file.so /usr/local/lib/
$ sudo cp -R www /usr/local/www
$ cd ../../
$ rm -rf mjpg-streamer-182


Start Live Streaming using following commands : 
=========================================================

$ mkdir /tmp/stream
$ raspistill --nopreview -w 640 -h 480 -q 5 -o /tmp/stream/pic.jpg -tl 100 -t 9999999 -th 0:0:0 &
$ LD_LIBRARY_PATH=/usr/local/lib mjpg_streamer -i "input_file.so -f /tmp/stream -n pic.jpg" -o "output_http.so -w /usr/local/www"

OR

You can execute live_stream.sh file by ./live_stream.sh from this folder. (Make sure file has executable permissions - chmod +x livestream.sh)