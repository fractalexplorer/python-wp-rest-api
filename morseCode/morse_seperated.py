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
        'Z': 'Z',
        '#': 'HASHTAG ',
        '@': 'AT ',
        '$': 'DOLLAR ',
        '&': 'AND ',
        '*': 'STAR ',
        '-': 'DASH ',
        ',': 'COMMA ',
        '.': 'PERIOD ',
        '1': 'ONE ',
        '2': 'TWO ',
        '3': 'THREE ',
        '4': 'FOUR ',
        '5': 'FIVE ',
        '6': 'SIX ',
        '7': 'SEVEN ',
        '8': 'EIGHT ',
        '9': 'NINE ',
        '0': 'ZERO '}

PINMAP = {'A': 2,
        'B': 3,
        'C': 4,
        'D': 5,
        'E': 6,
        'F': 7,
        'G': 8,
        'H': 9,
        'I': 10,
        'J': 11,
        'K': 12,
        'L': 13,
        'M': 14,
        'N': 15,
        'O': 16,
        'P': 17,
        'Q': 18,
        'R': 19,
        'S': 20,
        'T': 21,
        'U': 22,
        'V': 23,
        'W': 24,
        'X': 25,
        'Y': 26,
        'Z': 27}

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

# wp = Client('http://192.168.1.42/rpie/wordpress/xmlrpc.php', 'user', 'pass')

def displayChar(symbol):
        GPIO.output(PINMAP[symbol],1)
        time.sleep(1)
        GPIO.output(PINMAP[symbol],0)

def callApi():
        global timeInterval
        # display_text = wp.call(options.GetOptions('python_button_clicked'))
        # pinNum = wp.call(options.GetOptions('pinNum'))
        # speed = wp.call(options.GetOptions('speed'))
        # timeInterval = wp.call(options.GetOptions('timeInterval'))
        r = requests.get("http://192.168.1.42/rpie/wordpress/wp-json/rest/v1/get_python_message")
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
                            # Insert overridden message as a custom post in wordpress
                            #messagePost = WordPressPost()
                            #messagePost.post_type = 'rpie-message'
                            #messagePost.title = 'Message overriden by Raspberry Pi terminal'
                            #messagePost.content = outputString
                            #messagePost.custom_fields = []
                            #messagePost.custom_fields.append({
                            #        'key': 'terminal_id',
                            #        'value': '1'
                            #})
                            #messagePost.id = wp.call(posts.NewPost(messagePost))

                            r = requests.post("http://192.168.1.42/rpie/wordpress/wp-json/rest/v1/add_message_post",{'message':outputString, 'terminal_id':'1'})
                            # print (r)
                    else:
                            displaying_terminal_message = False
                            outputString = callApi();

                    for letter in outputString:
                            if letter.upper() in CHARMAP:
                                    for symbol in CHARMAP[letter.upper()]:
                                            print (symbol)
                                            if symbol is ' ':
                                                    time.sleep(1)
                                            else:
                                                    displayChar(symbol)
                            else:
                                    print ("Character : ",letter.upper()," is not supproted.")
                    
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




