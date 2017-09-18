from EmulatorGUI import GPIO
from wordpress_xmlrpc import Client, WordPressPost
from wordpress_xmlrpc.methods import options
#import RPi.GPIO as GPIO
import time
import traceback
import requests
import threading


MORSECODE = {' ': ' ',
        "'": '.----.',
        '(': '-.--.-',
        ')': '-.--.-',
        ',': '--..--',
        '-': '-....-',
        '.': '.-.-.-',
        '/': '-..-.',
        '0': '-----',
        '1': '.----',
        '2': '..---',
        '3': '...--',
        '4': '....-',
        '5': '.....',
        '6': '-....',
        '7': '--...',
        '8': '---..',
        '9': '----.',
        ':': '---...',
        ';': '-.-.-.',
        '?': '..--..',
        'A': '.-',
        'B': '-...',
        'C': '-.-.',
        'D': '-..',
        'E': '.',
        'F': '..-.',
        'G': '--.',
        'H': '....',
        'I': '..',
        'J': '.---',
        'K': '-.-',
        'L': '.-..',
        'M': '--',
        'N': '-.',
        'O': '---',
        'P': '.--.',
        'Q': '--.-',
        'R': '.-.',
        'S': '...',
        'T': '-',
        'U': '..-',
        'V': '...-',
        'W': '.--',
        'X': '-..-',
        'Y': '-.--',
        'Z': '--..',
        '_': '..--.-'}

speed=1
pinNum=7
timeInterval=30
outputString="Loading.."
#GPIO.cleanup()
GPIO.setmode(GPIO.BCM)
GPIO.setup(2,GPIO.OUT)
GPIO.setup(3,GPIO.OUT)
GPIO.setup(4,GPIO.OUT)
GPIO.setup(5,GPIO.OUT)
GPIO.setup(6,GPIO.OUT)
GPIO.setup(7,GPIO.OUT)
GPIO.setup(8,GPIO.OUT)
GPIO.setup(18,GPIO.OUT)

def dot():
        GPIO.output(pinNum,1)
        time.sleep(0.2*speed)
        GPIO.output(pinNum,0)
        time.sleep(0.2*speed)

def dash():
        GPIO.output(pinNum,1)
        time.sleep(0.5*speed)
        GPIO.output(pinNum,0)
        time.sleep(0.2*speed)

def callApi():
        global speed
        global pinNum
        global timeInterval
        # wp = Client('http://192.168.1.42/rpie/wordpress/xmlrpc.php', 'user', 'pass')
        # display_text = wp.call(options.GetOptions('python_button_clicked'))
        # pinNum = wp.call(options.GetOptions('pinNum'))
        # speed = wp.call(options.GetOptions('speed'))
        # timeInterval = wp.call(options.GetOptions('timeInterval'))
        r = requests.get("http://192.168.1.42/rpie/wordpress/wp-json/rest/v1/get_python_message")
        data = r.json()
        display_text = data["message"]
        pinNum = data["output_pin"]
        timeInterval = data["time"]
        speed = data["speed"]
        print ("")
        print ("API call successful to wordpress. New message to display is : ")
        print (display_text)
        print ("pinNum : ",pinNum)
        print ("speed :",speed)
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
                    # outputString = input('Enter your text : ')
                    #outputString = 'Hello World';
                    # print (user_input)
                    if user_input[0] is not None:
                            outputString = user_input[0];
                            displaying_terminal_message = True
                            print ("Message overridden by terminal input : ",outputString)
                            print ("----------------------------------------------------")
                    else:
                            displaying_terminal_message = False
                            outputString = callApi();

                    for letter in outputString:
                                    for symbol in MORSECODE[letter.upper()]:
                                            if symbol == '-':
                                                    dash()
                                            elif symbol == '.':
                                                    dot()
                                            else:
                                                    time.sleep(0.5*speed)
                                    time.sleep(0.5*speed)
                    
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




