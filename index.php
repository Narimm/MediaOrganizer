<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <title>Form for Media Optimizer</title>
    </head>    
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
    <body>
        <form action="fileTime.php" method="post">
            <input id="directory" type="text" name="directory" placeholder="Enter Full path to directory to scan" /><br/>
            <input type="radio" name="recursive" value='1'>Full Recursive<br/>
            <input type="radio" name="recursive" value='0'>No Recursion<br/>
            <input type ="submit" name="SUBMIT"/>
        </form>
    </body>     
</html>

