MediaOrganizer
==============

Version 0.2 beta

A simple php media renaming and reorganizing script.  This PHP script is installed on a webserver such as WAMP or LAMP.
You unzip the contents of the ZIP file into your wamp/www directory and then access it via http://localhost/MediaOrganizer

It takes a directory as an input.  You can choose if you want to recursively anaylze sub directories. 

Also you can choose NOT to reorder any non media files.  The script will update the File Creation Dates to be either 
 Date the image was generated according to EXIF tags, if the Filename can be interpreted into a date format it will use that or if the file modification date is earlier than the file creation date it will use that. 
 
 At the moment file names can be YYYYmmddHHiiss.extension or YY-mm-dd HH:ii:ss.ext YY:mm:dd HH:ii:ss.extension
 
Future settings can be added.

Files will be reorganized into base directory/FILE TYPE/YEAR/MONTH/FILE.ext 
File type is defined by extension and then classified as image or video.  Further classifications and types can be added into the setup.

I intend to enlarge the settings as I go.





