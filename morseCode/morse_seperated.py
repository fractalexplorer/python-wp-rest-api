from EmulatorGUI import GPIO
# import RPi.GPIO as GPIO
from wordpress_xmlrpc import Client, WordPressPost
from wordpress_xmlrpc.methods import options
import time
import traceback
import requests
import threading

CHARMAP = {' ': ' ',
        'A': 'A',
        'B': 'B',
        'C': 'C',
        'D': 'D',
        'E': 'E',
        'F': 'F',
        'G': 'G',
        'H': 'H',
        'I': 'I',
        'J': 'J',
        'K': 'K',
        'L': 'L',
        'M': 'M',
        'N': 'N',
        'O': 'O',
        'P': 'P',
        'Q': 'Q',
        'R': 'R',
        'S': 'S',
        'T': 'T',
        'U': 'U',
        'V': 'V',
        'W': 'W',
        'X': 'X',
        'Y': 'Y',
        'Z': 'Z'}

PINMAP = {'A': 2,
        'B': 2,
        'C': 3,
        'D': 4,
        'E': 5,
        'F': 6,
        'G': 7,
        'H': 8,
        'I': 9,
        'J': 10,
        'K': 11,
        'L': 12,
        'M': 13,
        'N': 14,
        'O': 15,
        'P': 16,
        'Q': 17,
        'R': 18,
        'S': 19,
        'T': 20,
        'U': 21,
        'V': 22,
        'W': 23,
        'X': 24,
        'Y': 25,
        'Z': 26}

speed=1
timeInterval=30
outputString="Loading.."
#GPIO.cleanup()
GPIO.setmode(GPIO.BCM)
# GPIO.setup(1,GPIO.OUT)
GPIO.setup(2,GPIO.OUT)
GPIO.setup(3,GPIO.OUT)
GPIO.setup(4,GPIO.OUT)
GPIO.setup(5,GPIO.OUT)
GPIO.setup(6,GPIO.OUT)
GPIO.setup(7,GPIO.OUT)
GPIO.setup(8,GPIO.OUT)
GPIO.setup(9,GPIO.OUT)
GPIO.setup(10,GPIO.OUT)
GPIO.setup(11,GPIO.OUT)
GPIO.setup(12,GPIO.OUT)
GPIO.setup(13,GPIO.OUT)
GPIO.setup(14,GPIO.OUT)
GPIO.setup(15,GPIO.OUT)
GPIO.setup(16,GPIO.OUT)
GPIO.setup(17,GPIO.OUT)
GPIO.setup(18,GPIO.OUT)
GPIO.setup(19,GPIO.OUT)
GPIO.setup(20,GPIO.OUT)
GPIO.setup(21,GPIO.OUT)
GPIO.setup(22,GPIO.OUT)
GPIO.setup(23,GPIO.OUT)
GPIO.setup(24,GPIO.OUT)
GPIO.setup(25,GPIO.OUT)
GPIO.setup(26,GPIO.OUT)

def displayChar(symbol):
        GPIO.output(PINMAP[symbol],1)
        time.sleep(1)
        GPIO.output(PINMAP[symbol],0)

def callApi():
        global timeInterval
        # wp = Client('http://192.168.1.42/rpie/wordpress/xmlrpc.php', 'user', 'pass')
        # display_text = wp.call(options.GetOptions('python_button_clicked'))
        # pinNum = wp.call(options.GetOptions('pinNum'))
        # speed = wp.call(options.GetOptions('speed'))
        # timeInterval = wp.call(options.GetOptions('timeInterval'))
        r = requests.get("http://192.168.1.42/rpie/wordpress/wordpress/wp-json/rest/v1/get_python_message")
        # print (r)
        data = r.json()
        display_text = data["message"]
        timeInterval = data["time"]
        print ("")
        print ("API call successful to wordpress. New message to display is : ")
        print (display_text)
        print ("timeInterval :",timeInterval)
        print ("----------------------------------------------------")
        return display_text

# Declare a mutable object so that it can be pass via reference
user_input = [None]

# spawn a new thread to wait for input 
def get_user_input(user_input_ref):
        while True:
                user_input_ref[0] = input("Override Message: ")

inputThread = threading.Thread(target=get_user_input, args=(user_input,))
inputThread.daemon = True
inputThread.start()

displaying_terminal_message = False

try:
        while True:
                    #outputString = 'Hello World';
                    if user_input[0] is not None:
                            outputString = user_input[0];
                            displaying_terminal_message = True
                            print ("Message overridden by terminal input : ",outputString)
                            print ("----------------------------------------------------")
                    else:
                            displaying_terminal_message = False
                            outputString = callApi();

                    for letter in outputString:
                                            print (letter)
                                            if letter is ' ':
                                                    time.sleep(1)
                                            else:
                                                    displayChar(letter.upper())
                    
                    if user_input[0] is None:
                            print ("Now waiting for ",timeInterval," seconds for another call")
                            time.sleep(timeInterval)
                    
                    if displaying_terminal_message:
                            user_input[0] = None
                            print ("User message overriden successfully, Now calling wordpress in 2 seconds")
                            time.sleep(2)

except Exception as ex:
        traceback.print_exc()
finally:
        GPIO.cleanup() #this ensures a clean exit




