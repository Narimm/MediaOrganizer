<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <title>Form for Media Optimizer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
        <link href="css/custom.css" rel="stylesheet"/>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><style type="text/css">
<!--
body {
	background-color: #06C;
}
#main 
{
	margin: 10px;
	background-color:#FFFFFF;
	border-radius: 5px ;
  -webkit-border-radius: 5px;
     -moz-border-radius: 5px;
          border-radius: 5px;
}
-->
</style>
</head>    
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
    <body>
    <div class="container ">
    <div id="main" class="col-lg-offset-3 col-lg-6">
    <h1>Form to Organize Media Files</h1>
    	<div class="form-group">
        <form action="fileTime.php" method="post">
        <label>Enter the Full path to the directory to scan</label>
            <input name="directory" type="text" id="directory" placeholder="Enter Full path to directory to scan" class="form-control"/> <span class=".glyphicon .glyphicon-folder-open"></span>
            <br/>
            <input type="radio" name="recursive" value='1'>Full Recursive<br/>
            <input type="radio" name="recursive" value='0'>No Recursion<br/>
            <input type="checkbox" name="nonmediamove" value="1">Move files not recognised as media<br/>
            <input type ="submit" name="SUBMIT" class='btn btn-primary'/>
        </form>
        </div>
        </div>
    </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    </body>     
</html>

