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
        system($cmd,$sys);
        if (!$sys === FALSE) {
            $sysout = TRUE;
        } else {
            $sysout = TRUE;
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
        foreach (GetFiles("$dirName\\$value") as $value) {
            $result[] = $value;
        }
    }
    return $result;
}

function FileReorder($filename,$basedir){
        $filebase = basename($filename);
        $date = getdate(filectime($filename));
        if($date['mon'] < 10){$datem ='0'. $date['mon'];}
        $datey = $date['year'];
        $newPath = "$basedir\\$datey\\$datem";
        $newFileName = $newPath.'/'.$filebase;
        $renamed = rename($filename, $newFileName);
        if($renamed){
                        printf('File Renamed: old-'.$filename.' new-'.$newFileName.' ');
        }
        return $renamed;
          
}
/**
     * 
     * @param string $filename File name full path
     * @return datetime Date Time in unix format
     */
    function GetFileCreateTime($filename){
        $FPtime = filectime($filename);
        $FPtimeRe = date('d-m-Y H:m:s', $FPtime);
        printf(' FileCreatedTime:'.$FPtimeRe);
        if($FPtime==0){$FPtime = 999999999999999;}
        $ExifData = @exif_read_data($filename,'FILE');
        if($ExifData!==FALSE){
            $ExifFT = $ExifData['FileDateTime'];
            $ExifFTRe = date('d-m-Y H:m:s', $ExifFT);
            printf(' ExifFileCreatedTime:'.$ExifFTRe);
        }
        $info = pathinfo($filename);
        $fileBase = basename($filename);
        if(strlen(basename($fileBase,'.'.$info['extension']))==14){
            $fYear = substr($fileBase,0,4);
            $fMonth = substr($fileBase,4,2);
            $fDay = substr($fileBase,6,2);
            $fHour = substr($fileBase,8,2);
            $fMin = substr($fileBase,10,2);
            $fSec = substr($fileBase,12,2);
            $FNDateTime = new DateTime($fYear.'-'.$fMonth.'-'.$fDay.' '.$fHour.':'.$fMin.':'.$fSec);
            $FNDateTimeRe = date('d-m-Y H:i:s', $FNDateTime->getTimestamp());
            printf(' FileNameExtractedTime: '.$FNDateTimeRe);
        }
        if (isset($FNDateTime)) {
        $fileTime = min($FPtime, $FNDateTime->getTimestamp());
        } else {
              $fileTime = $FPtime;
 }
    if ($ExifData !== False){$fileTime = $ExifFT;}
    if($fileTime<>$FPtime){return $fileTime;}else{return FALSE;}
    }
    
    
foreach ($_POST as $key => $value) {
    $formpost[$key] = $value;
}
$startDir=$formpost['directory'];
$files=  GetFiles($formpost['directory']);
?><html><body> <?php
foreach ($files as $file) {
    if (file_exists($file)) {
        $filetime = GetFileCreateTime($file);
        printf(' Filename: '.$file.' :');
        if($filetime !== False){
        $newTime = date('d-m-Y H:m:s', $filetime);
        printf($newTime);
        $processed = NmCDFile($file, $newTime);
        printf('Processed: '.$processed.'<br/>');}else{
        printf('File Time unchanged <br/>');}
        $reodered = FileReorder($file, $startDir);
    } else {
        printf($file.' - File Doesnt exists <br/>');
    }
}
?></body></html>
