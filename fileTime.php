<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
date_default_timezone_set('AUSTRALIA/Brisbane');

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

function FileReorder($filename, $basedir) {
    $filebase = basename($filename);
    $date = getdate(filectime($filename));
    if ($date['mon'] < 10) {
        $datem = '0' . $date['mon'];
    } else {
        $datem = $date['mon'];
    }
    $datey = $date['year'];
    $newPath = "$basedir\\$datey\\$datem";
    if (!is_dir($newPath)) {
        mkdir($newPath, '0777', TRUE);
    }
    $newFileName = "$newPath\\$filebase";
    if (!$newFileName == $filename) {
        if (file_exists($filename)) {
            //$filhandle = fopen($filename, 'r');
            //fclose($filhandle);
            //sleep(1);
            $renamed = rename($filename, $newFileName);
            if ($renamed) {
                printf('File Renamed: old-' . $filename . ' new-' . $newFileName . ' ');
            } else {
                printf('File Rename failed');
            }

            return $renamed;
        } else {
            printf('FILE DOESNT EXIST CANT RENAME');
            return FALSE;
        }
    } else {
        printf('Files already ordered not renamed');
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
    printf(' FileCreatedTime:' . $FPtimeRe);
    if ($FPtime == 0) {
        $FPtime = (time() + 1000000);
    }
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
        printf(' ExifFileCreatedTime:' . $ExifFTRe);
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
            printf(' FileNameExtractedTime: ' . $FNDateTimeRe);
        } elseif (strlen($info['filename']) == 19) {
            //here we assime the file name is in the format YYYY/mm/dd HH:ii:ss.extension
            $FNDateTime = new DateTime(basename($fileBase, '.' . $info['extension']));
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
$startDir = $formpost['directory'];
$recursive = $formpost['recursive'];
if ($recursive == '1') {
    $files = GetFilesRecursive($formpost['directory']);
} else {
    $files = GetFiles($formpost['directory']);
}
?><html><body> <?php
foreach ($files as $file) {
    if (file_exists($file)) {
        $filetime = GetFileCreateTime($file);
        printf(' Filename: ' . $file . ' :');
        if ($filetime !== False) {
            $newTime = date('d-m-Y H:m:s', $filetime);
            printf($newTime);
            $processed = NmCDFile($file, $newTime);
            printf('Processed: ' . $processed . '<br/><hr/>');
        } else {
            printf('File Time unchanged <br/><hr/>');
        }
        $reodered = FileReorder($file, $startDir);
    } else {
        printf($file . ' - File Doesnt exists <br/><hr/>');
    }
}
?></body></html>
