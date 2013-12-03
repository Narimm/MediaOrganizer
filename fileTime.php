<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
date_default_timezone_set('AUSTRALIA/Brisbane');
$mediatypes = array(
    'jpg' => 'image',
    'jp2' => 'image',
    'jpx' => 'image',
    'jpeg' => 'image',
    'png' => 'image',
    'psd' => 'image',
    'bmp' => 'image',
    'tiff' => 'image',
    'tif' => 'image',
    'swf' => 'video',
    'gif' => 'image',
    'mp4' => 'video',
    'm2ts' => 'video',
    'mov' => 'video',
    'mod' => 'video',
    'avi' => 'video',
    'mpg' => 'video',
    '3gp' => 'video'
);

function NmCDFile($filename, $newDateTime) {
    $cmd = "C:\\nircmd\\nircmdc.exe setfilefoldertime \"$filename\" \"$newDateTime\"";
    $sys = FALSE;
    system($cmd, $sys);
    if ($sys !== FALSE) {
        $sysout = TRUE;
    } else {
        $sysout = FALSE;
    }
    return $sysout;
}

function GetFiles($dirName) {
    $root = scandir($dirName);
    foreach ($root as $value) {
        if ($value === '.' || $value === '..') {
            continue;
        }
        if (is_file("$dirName\\$value")) {
            $result[] = "$dirName\\$value";
            continue;
        }
        if (is_dir("$dirName\\$value")) {
            continue;
        }
    }
    return $result;
}

function GetFilesRecursive($dirName) {
    $root = scandir($dirName);
    $result = array();
    foreach ($root as $value) {
        if ($value === '.' || $value === '..') {
            continue;
        }
        if (is_file("$dirName\\$value")) {
            $result[] = "$dirName\\$value";
            continue;
        }
        foreach (GetFilesRecursive("$dirName\\$value") as $value) {
            $result[] = $value;
        }
    }
    return $result;
}

function FileReorder($filename, $basedir, $extradir = null) {
    $filebase = basename($filename);
    $date = getdate(filectime($filename));
    if ($date['mon'] < 10) {
        $datem = '0' . $date['mon'];
    } else {
        $datem = $date['mon'];
    }
    $datey = $date['year'];
    if($extradir != null){$newPath = "$basedir\\$extradir\\$datey\\$datem";}else{$newPath = "$basedir\\$datey\\$datem";}
    if (!is_dir($newPath)) {
        mkdir($newPath, '0777', TRUE);
    }
    $newFileName = "$newPath\\$filebase";
    if ($newFileName != $filename) {
        if (file_exists($filename)) {
            //$filhandle = fopen($filename, 'r');
            //fclose($filhandle);
            //sleep(1);
            $renamed = rename($filename, $newFileName);
            if ($renamed) {
               // printf('File Renamed: old-' . $filename . ' new-' . $newFileName . ' ');
            } else {
         //       printf('File Rename failed');
            }

            return $renamed;
        } else {
          //  printf('FILE DOESNT EXIST CANT RENAME');
            return FALSE;
        }
    } else {
       // printf('Files already ordered not renamed');
        return false;
    }
}

/**
 * 
 * @param string $filename File name full path
 * @return datetime Date Time in unix format
 */
function GetFileCreateTime($filename) {
    $FPtime = filectime($filename);
    $FPtimeRe = date('d-m-Y H:m:s', $FPtime);
    $FMtime = filemtime($filename);
    // printf(' FileCreatedTime:' . $FPtimeRe);
    if($FMtime<$FPtime){$FPtime=$FMtime;}
    if ($FPtime == 0) {$FPtime = (time() + 10000000);}
    // $exiffilename = str_replace(" ","%20",$filename);
    $exifType = exif_imagetype($filename);
    if ($exifType !== '0') {
        $ExifData = @exif_read_data($filename, 'FILE'); //supress's warning for files that exif funcs can interpret but dont actually have exif data
    } else {
        $ExifData = FALSE;
    }
    if ($ExifData !== FALSE) {
        $ExifFT = $ExifData['DateTimeOriginal'];
        $ExifFTRe = date('d-m-Y H:m:s', $ExifFT);
       // printf(' ExifFileCreatedTime:' . $ExifFTRe);
        $fileTime = $ExifFT;
    } else {
        $info = pathinfo($filename);
        $fileBase = $info['filename'];
        if (strlen($info['filename']) == 14) {
            $fYear = substr($fileBase, 0, 4);
            $fMonth = substr($fileBase, 4, 2);
            $fDay = substr($fileBase, 6, 2);
            $fHour = substr($fileBase, 8, 2);
            $fMin = substr($fileBase, 10, 2);
            $fSec = substr($fileBase, 12, 2);
            $FNDateTime = new DateTime($fYear . '-' . $fMonth . '-' . $fDay . ' ' . $fHour . ':' . $fMin . ':' . $fSec);
            $FNDateTimeRe = date('d-m-Y H:i:s', $FNDateTime->getTimestamp());
          //  printf(' FileNameExtractedTime: ' . $FNDateTimeRe);
        } elseif (strlen($info['filename']) == 19) {
            //check the pattern fits
            if (is_numeric($info['filename'])) {
                //here we assime the file name is in the format YYYY/mm/dd HH:ii:ss.extension
                $FNDateTime = new DateTime(basename($fileBase, '.' . $info['extension']));
            }
        }
        if (isset($FNDateTime)) {
            $fileTime = min($FPtime, $FNDateTime->getTimestamp());
        } else {
            $fileTime = $FPtime;
        }
    }
    if ($fileTime <> $FPtime) {
        return $fileTime;
    } else {
        return FALSE;
    }
}

foreach ($_POST as $key => $value) {
    $formpost[$key] = $value;
}
if (array_key_exists('nonmediamove', $formpost)) {
    $reordernonmedia = TRUE;
} else {
    $reordernonmedia = FALSE;
}
$startDir = $formpost['directory'];
$recursive = $formpost['recursive'];
if ($recursive == '1') {
    $files = GetFilesRecursive($formpost['directory']);
} else {
    $files = GetFiles($formpost['directory']);
}
        $totalfiles = 0;
        $processcount = 0;
        $timenochange = 0;
        $filereordercount = 0;
        $filenotreordered = 0;
        $notmediacount = 0;
        $filnoexistcount = 0;
        foreach ($files as $file) {
            $totalfiles++;
            if (file_exists($file)) {
                if (array_key_exists(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $mediatypes)) {
                    $filemediatype = $mediatypes[strtolower(pathinfo($file, PATHINFO_EXTENSION))];
                    $filetime = GetFileCreateTime($file);
                   // printf(' Filename: ' . $file . ' :');
                    if ($filetime !== False) {
                        $newTime = date('d-m-Y H:m:s', $filetime);
                        printf($newTime);
                        $processed = NmCDFile($file, $newTime);
                      //  printf('Processed: ' . $processed . '<br/><hr/>');
                        $processcount++;
                    } else {
                     //   printf('File Time unchanged <br/><hr/>');
                        $timenochange++;
                    }
                    $reordered = FileReorder($file, $startDir,$filemediatype);
                    if ($reordered) {
                        $filereordercount ++;
                    } else {
                        $filenotreordered++;
                    }
                } else {
                    if ($reordernonmedia) {
                        $reordered = FileReorder($file, $startDir,'NonMedia');
                        if ($reordered) {
                            $filereordercount ++;
                        } else {
                            $filenotreordered++;
                        }
                    }
                  //  printf($file . ' - Was not found in media types');
                    $notmediacount++;
                }
            } else {
               // printf($file . ' - File Doesnt exists <br/><hr/>');
                $filnoexistcount++;
            }
        }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <body>
    <div class="container ">
    <div id="main" class="col-lg-offset-3 col-lg-6">
    <div id="result"><p>
<?php echo $totalfiles . ' in total Processed. </br>' . $filnoexistcount . ' files didnt exist.<br/>' . $notmediacount . ' files didnt not match correct media types.<br/>' . $filenotreordered . ' files were not moved into new structure <br/>'
 . $timenochange . ' files were did not get retagged.<br/>' . $processcount . ' files were retagged.<br/>' . $filereordercount . ' Files wer moved into new directory structure.<br/>';
?>
            </p></div>
        </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    </body></html>
