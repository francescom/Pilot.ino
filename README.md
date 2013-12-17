<p><b>Arduino Pilot - v 0.1</b> - a script to control the Arduino pins from a connected computer via serial port</p>





<p>Original version for UDOO board by Francesco Munaf&ograve;</p>





<p>
The idea is to generically control the Arduino with some simple "Sketch Control Language"

much like an SQL query controls (reads/writes) a database.

We may slowly add commands when needed, to cover all possible interactions.


To add commands modify interpretCommand() to parse your new xyz command and then add a doXyz() function with the correct params
</p>



<p>Available "SCL" ("Sketch Control Language") commands (you can add more to the sketch):</p>


<p>dir &lt;in or out> &lt;pin>: set input or output direction for a pin</p>

<ul><p>dir i 12</p>

<p>dir out 10</p>

<p>(any string beginning with i will be for input, anything else will be output)
</p>
</ul>

<p>get &lt;analog or digital> &lt;pin>: get current pin value</p>

<ul><p>get a A0</p>

<p>get d 12</p>

<p>(writes to serial the value returned, 0/1 for digital, 0-1023 for analog)
</p>
</ul>

<p>set &lt;analog or digital> &lt;pin> &lt;value>: set pin value to &lt;value>

<ul><p>set a A0 128</p>

<p>set a A1 0x7a</p>

<p>set d 10 hi</p>

<p>(for &lt;value> use hi or lo, or a numeric value for analog)
</p>
</ul>


<p>Use numeric values for pins, or you can use A0-An strings for analog, will be converted to numeric
</p>

<p>Script options at the top of the script.
</p>

<p>Some ideas for commands to add (anything that the speed of serial port can't handle,

or that can be done better on the Arduino side), for example:
</p>
<p>fading analogs in/out:
playing tunes/notes:
</p>


<p>Some ideas for extending the script:
</p>

<p>Add an interface to add custom commands at script startup:

setCommand("cmdword",&commandFunction,"spv");  adds command cmdword that calls function commandFunction() with string,pin,value params
</p>

<p>Contact me at francesco [A T] esurfers d o t com
</p>


