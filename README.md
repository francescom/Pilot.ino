Arduino Pilot - v 0.1 - a script to control the Arduino pins from a connected computer via serial port


Original version for UDOO board by Francesco Munafo'


The idea is to generically control the Arduino with some simple "Sketch Control Language"

much like an SQL query controls (reads/writes) a database.

We may slowly add commands when needed, to cover all possible interactions.


To add commands modify interpretCommand() to parse your new xyz command and then add a doXyz() function with the correct params




Available "SCL" ("Sketch Control Language") commands (you can add more to the sketch):


dir &lt;in or out> &lt;pin>: set input or output direction for a pin

dir i 12

dir out 10

(any string beginning with i will be for input, anything else will be output)



get &lt;analog or digital> &lt;pin>: get current pin value

get a A0

get d 12

(writes to serial the value returned, 0/1 for digital, 0-1023 for analog)



set &lt;analog or digital> &lt;pin> &lt;value>: set pin value to &lt;value>

set a A0 128

set a A1 0x7a

set d 10 hi

(for &lt;value> use hi or lo, or a numeric value for analog)


Use numeric values for pins, or you can use A0-An strings for analog, will be converted to numeric


Script options at the top of the script.


Some ideas for commands to add (anything that the speed of serial port can't handle,

or that can be done better on the Arduino side), for example:

fading analogs in/out:

playing tunes/notes:



Some ideas for extending the script:

Add an interface to add custom commands at script startup:

setCommand("cmdword",&commandFunction,"spv");  adds command cmdword that calls function commandFunction() with string,pin,value params


Contact me at francesco [A T] esurfers d o t com");



