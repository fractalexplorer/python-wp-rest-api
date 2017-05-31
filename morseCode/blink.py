from EmulatorGUI import GPIO
#import RPi.GPIO as GPIO
import time
import traceback

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
pinNum=7
GPIO.setmode(GPIO.BCM)
GPIO.setup(pinNum,GPIO.OUT)


def dot():
	GPIO.output(pinNum,1)
	time.sleep(0.2)
	GPIO.output(pinNum,0)
	time.sleep(0.2)

def dash():
	GPIO.output(pinNum,1)
	time.sleep(0.5)
	GPIO.output(pinNum,0)
	time.sleep(0.2)

while True:
	outputString = input('Enter your text : ')
	#outputString = 'Hello World';
	for letter in outputString:
			for symbol in MORSECODE[letter.upper()]:
				if symbol == '-':
					dash()
				elif symbol == '.':
					dot()
				else:
					time.sleep(0.5)
			time.sleep(0.5)






