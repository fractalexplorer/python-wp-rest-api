#from EmulatorGUI import GPIO
import RPi.GPIO as GPIO
from twython import Twython
#from wordpress_xmlrpc import Client, WordPressPost
#from wordpress_xmlrpc.methods import options
import time
import traceback
import requests
import threading

TWITTER_APP_KEY = 'pZCp6Ys42KU0vACixwi8dy6hR' #supply the appropriate value
TWITTER_APP_KEY_SECRET = 'K4S4HE0dqe2J38jer8sRYvfKYfT3rzfLPHY8XCfGLpEnCcld1G' 
TWITTER_ACCESS_TOKEN = '193824747-hESsFkU96CVvQhuHyYoOHAB5u4wnB7u9iSiFskkG'
TWITTER_ACCESS_TOKEN_SECRET = 'ikLEUTFuH5E3eeZuqWaXzgSQbvypfF2B72v9NmeAGcsAe'

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

pinNum=7
speed=1
timeInterval=30
outputString="Loading.."
mode = "single_pin"
hashtag = "rpitweet"
twitter_mode = "off"

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
GPIO.setup(27   ,GPIO.OUT)

apiurl = "http://rpi.testplanets.com/"
twitterObj = Twython(app_key=TWITTER_APP_KEY, 
    app_secret=TWITTER_APP_KEY_SECRET, 
    oauth_token=TWITTER_ACCESS_TOKEN, 
    oauth_token_secret=TWITTER_ACCESS_TOKEN_SECRET)
# wp = Client(apiurl+'xmlrpc.php', 'user', 'pass')

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

def displayChar(symbol):
        GPIO.output(PINMAP[symbol],1)
        time.sleep(1)
        GPIO.output(PINMAP[symbol],0)

def callApi():
        global timeInterval
        global pinNum
        global twitter_mode
        global hashtag
        global mode
        # display_text = wp.call(options.GetOptions('python_button_clicked'))
        # pinNum = wp.call(options.GetOptions('pinNum'))
        # speed = wp.call(options.GetOptions('speed'))
        # timeInterval = wp.call(options.GetOptions('timeInterval'))
        r = requests.get(apiurl+"wp-json/rest/v1/get_python_message")
        # print (r)
        data = r.json()
        display_text = data["message"]
        pinNum = data["output_pin"]
        timeInterval = data["time"]
        mode = data["rpi_mode"]
        hashtag = data["hashtag"]
        twitter_mode = data["twitter_mode"]
        print ("")
        print ("API call successful to wordpress. New message to display is : ")
        print (display_text)
        print ("pinNum : ",pinNum)
        print ("mode : ",mode)
        print ("Twitter mode : ",twitter_mode)
        print ("timeInterval :",timeInterval)
        print ("----------------------------------------------------")
        return display_text

def fetchFirstTweetFromHashtag():
        global hashtag
        global twitterObj
        print ("Twitter mode is on, So fetching hashtag from twitter for tag : #",hashtag)
        search = twitterObj.search(q='#'+hashtag, result_type='recent' , count=1)
        tweets = search['statuses']
        if not tweets:
                print("No tweets found. Displaying wordpress message now")
                return ""
        for tweet in tweets:
                print ("Fetching first tweet from twitter",tweet['text'])
                tUrl = 'http://twitframe.com/show?url=https%3A%2F%2Ftwitter.com%2F'+ str(tweet['user']['screen_name']) +'%2Fstatus%2F'+ str(tweet['id'])
                print (tUrl)
                return {'text': tweet['text'], 'username': str(tweet['user']['screen_name']), 'id': str(tweet['id'])}

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
                            #messagePost.post_type = 'rpi-message'
                            #messagePost.title = 'Message overridden by Raspberry Pi terminal'
                            #messagePost.content = outputString
                            #messagePost.custom_fields = []
                            #messagePost.custom_fields.append({
                            #        'key': 'terminal_id',
                            #        'value': '1'
                            #})
                            #messagePost.id = wp.call(posts.NewPost(messagePost))

                            r = requests.post(apiurl+"wp-json/rest/v1/add_message_post",{'message':outputString, 'terminal_id':'1'})
                            # print (r)
                    else:
                            displaying_terminal_message = False
                            outputString = callApi();
                            if twitter_mode == "on":
                                    print ("----------------------------------------------------")
                                    twitterTweet = fetchFirstTweetFromHashtag()
                                    outputString = twitterTweet['text']
                                    r = requests.post(apiurl+"wp-json/rest/v1/update_twitter_message",{'twitter_last_message':outputString, 'username':twitterTweet['username'] , 'user_id':twitterTweet['id'] , 'terminal_id':'1'})
                                    # print (r)
                    

                    if mode == "single_pin":
                            for letter in outputString:
                                    if letter.upper() in MORSECODE:
                                        for symbol in MORSECODE[letter.upper()]:
                                                if symbol == '-':
                                                        dash()
                                                elif symbol == '.':
                                                        dot()
                                                else:
                                                        time.sleep(0.5*speed)
                                    else:
                                        print("Skipping character : No support for ",letter.upper())
                                    
                                    time.sleep(0.5*speed)
                    
                    if mode == "26_pin_mode":
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
                            print ("User message overridden successfully, Now calling wordpress in 2 seconds")
                            time.sleep(2)

except Exception as ex:
        traceback.print_exc()
finally:
        GPIO.cleanup() #this ensures a clean exit




