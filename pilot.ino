// Arduino Pilot - v 0.1 - a script to control the Arduino pins from a connected computer via serial port with any scripting language
//
// Original version for UDOO board by Francesco Munafo'
//
// The idea is to generically control the Arduino with some simple "Sketch Control Language"
// much like an SQL query controls (reads/writes) a database.
// We may slowly add commands when needed, to cover all possible interactions.
//
// To add commands modify interpretCommand() to parse your new xyz command and then add a doXyz() function with the correct params
//
//
//
// Available "SCL" ("Sketch Control Language") commands (you can add more to the sketch):
//
// dir <in or out> <pin>: set input or output direction for a pin
// dir i 12
// dir out 10
// (any string beginning with i will be for input, anything else will be output)
//
//
// get <analog or digital> <pin>: get current pin value
// get a A0
// get d 12
// (writes to serial the value returned, 0/1 for digital, 0-1023 for analog)
//
// set <analog or digital> <pin> <value>: set pin value to <value>
// set a A0 128
// set a A1 0x7a
// set d 10 hi
// (for <value> use hi or lo, or a numeric value for analog)
//
// Use numeric values for pins, or you can use A0-An strings for analog, will be converted to numeric
//
// Script options at the top of the script.
//
//
// Some ideas for commands to add (anything that the speed of serial port can't handle,
// or that can be done better on the Arduino side), for example:
// fading analogs in/out:
// playing tunes/notes:
//
//
// Some ideas for extending the script:
// Add an interface to add custom commands at script startup:
// setCommand("cmdword",&commandFunction,"spv"); // adds command cmdword that calls function commandFunction() with string,pin,value params
//
// Contact me at francesco [A T] esurfers d o t com");
//




const boolean DEBUG=false;
const char BUFFER_SIZE=255;
const char CMD_SEP=' ';
const char CMD_TERM1='\n';
const char CMD_TERM2='\r';
const char CMD_TERM3='|';


const int SERIAL_SPEED=57600;
const int FIRST_ANALOG_PIN=54;

const int SERIAL_SPEED=9600;
const int FIRST_ANALOG_PIN=54; // A0




///////////////////////
////////////
///////
////     INIT
//
//



int MAX_BUF=BUFFER_SIZE-2; // one for zero term one for safety
char buff[BUFFER_SIZE]="";
char *buffPtr=buff;


///////////////////////
////////////
///////
////     SETUP and LOOP
//
//


void setup() {
  Serial.begin(SERIAL_SPEED);
  delay(500);
}

void loop() {
  if(Serial.available()) {
    char inChar = (char)Serial.read();
    if(inChar==CMD_TERM1 || inChar==CMD_TERM2 || inChar==CMD_TERM3) {
      buffPtr=buff;
      if(DEBUG) {
        Serial.print("String: ");
        Serial.println(buff);
      }
      parseCommand(buff);
    } else if(buffPtr<buff+MAX_BUF) {
      *buffPtr++=inChar;
      *(buffPtr+1)='\0';
      if(DEBUG) {
        Serial.print(inChar);
      }
    }
    // Serial.print(buff);
  }  
}



///////////////////////
////////////
///////
////     PARSING PARAMETERS
//
//



int getInt(char* str) {
  int pin=0;
  if(*str=='0' && *(str+1)=='x') { return myAtoiHex(str+2); }
  else if(*str=='h') { return myAtoiHex(str+1); }
  else if(*str=='d') { return myAtoi(str+1); }
  else { return myAtoi(str); }
}
int getPin(char* str) {
  int pin=0;
  if(*str=='A') { return FIRST_ANALOG_PIN+myAtoiHex(str+1); }
  else if(*str=='0' && *(str+1)=='x') { return myAtoiHex(str+2); }
  else if(*str=='h') { return myAtoiHex(str+1); }
  else if(*str=='d') { return myAtoi(str+1); }
  else { return myAtoi(str); }
}

int getValue(char*str) {
  if(!strcmp(str,"hi") || !strcmp(str,"on") || !strcmp(str,"true")) return HIGH;
  else if(!strcmp(str,"lo") || !strcmp(str,"off") || !strcmp(str,"false")) return LOW;
  else return getInt(str);
}



///////////////////////
////////////
///////
////     COMMAND FUNCTIONS TO CALL FROM SERIAL set=>doSet, get=>doGet, etc..
//
//



void doSet(char* anOrDig,int pinNum,int value) {
    /**/
    if(DEBUG) {
      Serial.print("doSet( ");
      Serial.print(anOrDig);
      Serial.print(", ");
      Serial.print(pinNum);
      Serial.print(", ");
      Serial.print(value);
      Serial.println(" )");
    }
    //*/
    
    if(!strcmp(anOrDig,"a") || !strcmp(anOrDig,"A") || !strcmp(anOrDig,"analog")) {
      analogWrite(pinNum, value); 
    } else {
      digitalWrite(pinNum, value);
    }
    Serial.println("OK");
}

void doGet(char* anOrDig,int pinNum) {
    /**/
    if(DEBUG) {
      Serial.print("doGet( ");
      Serial.print(anOrDig);
      Serial.print(", ");
      Serial.print(pinNum);
      Serial.println(" )");
    }
    //*/
    
    if(*anOrDig=='a' || *anOrDig=='A') {
      int sensorValue = analogRead(pinNum);
      writelnPadded(sensorValue,4);
    } else {
      pinMode(pinNum, INPUT);
      int pinState = digitalRead(pinNum);
      Serial.println(pinState);
    }
}

void doDir(char* inOrOut,int pinNum) {
    /**/
    if(DEBUG) {
      Serial.print("doDir( ");
      Serial.print(inOrOut);
      Serial.print(", ");
      Serial.print(pinNum);
      Serial.println(" )");
    }
    //*/
    
    if(*inOrOut=='i' || *inOrOut=='I') {
      pinMode(pinNum, INPUT);
    } else {
      pinMode(pinNum, OUTPUT);
    }
    Serial.println("OK");

}

void doVers() {
    /**/
    if(DEBUG) {
      Serial.print("doVers( ");
      Serial.println(" )");
    }
    //*/
    
    Serial.println("Arduino Pilot - v 0.1 - a script to control the Arduino pins from a connected computer via serial port");
}

void doHelp() {
    /**/
    if(DEBUG) {
      Serial.print("doHelp( ");
      Serial.println(" )");
    }
    //*/
    
    Serial.println("Arduino Pilot - v 0.1 - a script to control the Arduino pins from a connected computer via serial port");
    Serial.println("Original version for UDOO board by Francesco Munafo'");
    Serial.println("");
    Serial.println("Available commands (you can add more to the sketch):");
    Serial.println("");
    Serial.println("dir <in or out> <pin>: set input or output direction for a pin");
    Serial.println("    (any string beginning with i will be for input, anything else will be output)");
    Serial.println("");
    Serial.println("get <analog or digital> <pin>: get current pin value");
    Serial.println("    (writes to serial the value returned, 0/1 for digital, 0-1023 for analog)");
    Serial.println("");
    Serial.println("set <analog or digital> <pin> <value>: set pin value to <value>");
    Serial.println("    (for <value> use hi or lo, or a numeric value for analog)");
    Serial.println("");
    Serial.println("Use numeric values for pins, you can use A0-An for analog, will be converted to numeric");
    Serial.println("");
    Serial.println("Script options:");
    Serial.println("");
    Serial.print("Command separator:'");
    Serial.print(CMD_SEP);
    Serial.println("'");
    Serial.print("Buffer size:");
    Serial.print(BUFFER_SIZE);
    Serial.println("");
    Serial.print("First analog pin (A0):");
    Serial.print(FIRST_ANALOG_PIN);
    Serial.println("");
    Serial.println("");
    Serial.print("Serial port speed:");
    Serial.print(SERIAL_SPEED);
    Serial.println("");
    Serial.println("");
    Serial.println("Contact me at francesco [A T] esurfers d o t com");
    Serial.println("");
    
}




///////////////////////
////////////
///////
////     COMMAND FUNCTIONS DISPATCHER  if cmd=="set" call doSet(..), etc.. 
//
//




void interpretCommand(char* blocks[],int numBlocks) {
  int curCmd=0;
  if(DEBUG) {
    Serial.print("Command, ");
    Serial.print(numBlocks);
    Serial.print(" words:");
    for(int i=0;i<numBlocks;i++) {
      Serial.print(" ");
      Serial.print(blocks[i]);
    }
    Serial.println(".");
  }
  if(!strcmp(blocks[curCmd],"set") && curCmd+3<numBlocks) {
    doSet(blocks[curCmd+1],getPin(blocks[curCmd+2]),getValue(blocks[curCmd+3]));
    curCmd+=3;
  } else if(!strcmp(blocks[curCmd],"get") && curCmd+2<numBlocks) {
    doGet(blocks[curCmd+1],getPin(blocks[curCmd+2]));
    curCmd+=2;
  } else if(!strcmp(blocks[curCmd],"dir") && curCmd+2<numBlocks) {
    doDir(blocks[curCmd+1],getPin(blocks[curCmd+2]));
    curCmd+=2;
  } else if(!strcmp(blocks[curCmd],"vers") && curCmd<numBlocks) {
    doVers();
    curCmd+=0;
  } else if(!strcmp(blocks[curCmd],"help") && curCmd<numBlocks) {
    doHelp();
    curCmd+=0;
  } else {
    Serial.print("ERROR Unknown command ");
    Serial.println(blocks[curCmd]);
  }
}



///////////////////////
////////////
///////
////     GENERIC WORD-SPLIT STRING PARSER
//
//



void parseCommand(char*cmd) { // cmd is C string (\0 terminated char array)
  const int BLOCKS_MAX_NUM=10;
  
  int MAX_BLOCK=BLOCKS_MAX_NUM-2; // 1 for safety 1 for next block - MAX_BLOCK must be at least one for first
  char* blocks[BLOCKS_MAX_NUM];
  int curBlock=0;
  
  blocks[curBlock++]=cmd;
  blocks[curBlock+1]=(char*)0;
  for(char* i=cmd;i<cmd+MAX_BUF;i++) {
    if(*i=='\0') break;
    if(*i==CMD_SEP && curBlock<MAX_BLOCK) {
      *i='\0';
      blocks[curBlock++]=(i+1);
      blocks[curBlock+1]=(char*)0;
    }
  }
  interpretCommand(blocks,curBlock);
}




///////////////////////
////////////
///////
////     STRINGS TO NUMBERS UTILS (no standard libs requires)
//
//

void writelnPadded( int number, byte width ) {
  int currentMax = 10;
  for (byte i=1; i<width; i++){
    if (number < currentMax) {
      Serial.print("0");
    }
    currentMax *= 10;
  } 
  Serial.println(number);
}

bool isNumericChar(char x) {
    return (x >= '0' && x <= '9');
}
 
bool isHexChar(char x) {
    return (x >= '0' && x <= '9' || x >= 'A' && x <= 'F');
}

int hexCharVal(char x) {
  if(x >= '0' && x <= '9') return (x - '0');
  if(x >= 'A' && x <= 'F') return (x - 'A')+10;
  if(x >= 'a' && x <= 'f') return (x - 'a')+10;
  return 0;
}

char charToUpper(char c) {
	if(c>='a' && c<='z') return c-('a'-'A');
	return c;
}
 
int myAtoi(char *str) {
    if (*str == NULL) return 0;
 
    int res = 0;
    int sign = 1;
    int i = 0;
 
    if (str[0] == '-') {
        sign = -1;
        i++;
    }
    for (; str[i] != '\0'; ++i) {
        if (!isHexChar(str[i])) return sign*res;
        res = res*10 + hexCharVal(str[i]);
    }
    return sign*res;
}
int myAtoiHex(char *str) {
    if (*str == NULL) return 0;
 
    int res = 0;
    int sign = 1;
    int i = 0;
 
    if (str[0] == '-') {
        sign = -1;
        i++;
    }
    for (; str[i] != '\0'; ++i) {
        if (!isNumericChar(str[i])) return sign*res;
        res = res*16 + hexCharVal(str[i]);
    }
    return sign*res;
}


