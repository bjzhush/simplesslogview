<?php

$file = '/var/log/ssstatus.log';

$fp = fopen($file, 'r');
if (!$fp) {
    exit('error reading '.$file);
}

$isLatestDayDate = false;

$alias = [
    18888 => 'zhus',
    20001 => 'sqpQun',
    20005 => 'caoyl',
    20006 => 'zhaoz',
    20007 => 'lvs',
    20008 => 'gaoh',
    20009 => 'zhaor',
    20010 => 'dcmm',
    20011 => 'qizf',
    20012 => 'yangmq',
    20013 => 'pang',
    20014 => 'one@sale',
    20015 => 'wufq',
    20018 => 'zhoupp',
    20019 => 'han@sale'
];

//判断一行是否未日期行
function isTimeRow($line) {
   if (substr($line, 0, 3) == '#20')  {
      return true; 
   }
   return false;
}

//判断一行时间是否为24*31小时内
function isLatestDayDate($line) {
    if (!isTimeRow($line)) {
       return false; 
    }
    #bug fix for type time format
    $line[11] = " ";
    $timeStamp = strtotime(str_replace('#', '', $line));
    if (time() - $timeStamp < 24*31*3600) {
        return true;
    }
    return false;
}

//从时间行内获取m和d
function getMdFromLine($line) {
    if (!isTimeRow($line)) {
       return false; 
    }
    $line[11] = " ";
    $timeStamp = strtotime(str_replace('#', '', $line));
    return date('md', $timeStamp);
    
}

//判断是否为统计的端口
function isAnalyzePort($port) {
   if ($port == 18888 || $port > 20000)  {
      return true; 
   }
   return false;
}

function getDayBeforeFromLine($line) {
    if (!isTimeRow($line)) {
        return false;
    }
    $line[11] = " ";
    $timeStamp = strtotime(str_replace('#', '', $line));
    return floor(time()/86400) - floor($timeStamp/86400);
}

function getTimeStampFromLine($line) {
    if (!isTimeRow($line)) {
        return false;
    }
    $line[11] = " ";
    $timeStamp = strtotime(str_replace('#', '', $line));
    return $timeStamp;
}

$ret = [];
$date = 0;
$dayBefore = 0;

while (($line = fgets($fp)) !== false) {
    $line = trim($line);
    if ($isLatestDayDate === false) {
        if( isTimeRow($line) && isLatestDayDate($line)) {
            //到达31Days内数据区域
           $isLatestDayDate = true; 
        }

    } 
    
    if ($isLatestDayDate) {
        if (isTimeRow($line)) {
           $date = getMdFromLine($line);
           $dayBefore = getDayBeforeFromLine($line);
           $dayM =  date('m', getTimeStampFromLine($line));
           $dayD =  date('d', getTimeStampFromLine($line));
        } elseif ($line[0] != '#') {
            $tmp = explode(' ', $line);
            $port = $tmp[1];
            $count = $tmp[0];
            if (isAnalyzePort($port)) {
                if (!isset($ret[$port][$dayBefore])) {
                    $ret[$port][$dayBefore] = [
                        $dayBefore+1,
                        $count,
                        $dayM,
                        $dayD,
                    ];
                } else {
                    $ret[$port][$dayBefore][1] += $count;
                }
            }
        }
    }
}

fclose($fp);
//获取最大值
$maxValue = 0;

//获取图形化json
$jsonData = '';
foreach ($ret as $port => $portData) {
    foreach ($portData as $dayValue) {
       if ($dayValue[1] > $maxValue)  {
          $maxValue = $dayValue[1];
       }
    }
    $portData = array_values($portData);
    $tmpJson = json_encode($portData);
    $tmpJson[0] = '[';
    $tmpJson[strlen($tmpJson)-1] = ']';
    $name = isset($alias[$port]) ? $alias[$port] : 'Port'.$port;
    $jsonData .= sprintf("
    {
        name: '%s',
        type: 'scatter',
        itemStyle: itemStyle,
        data: %s 
    },
    ", $name, $tmpJson);
}
$jsonData = trim($jsonData, ',');


//获取标题
$ports = array_keys($ret);
foreach ($ports as $k => $port) {
   if (isset($alias[$port]))  {
      unset($ports[$k]);
   }
}
$title = array_merge(array_values($alias), $ports);
foreach($title as $k => $v) {
   if (is_numeric($v))  {
      $title[$k]  =  'port'.$v;
   }
}
$titleJson = json_encode($title);

include ('index.tpl');
