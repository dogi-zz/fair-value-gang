<?php

require_once 'analyse-data.php';

file_put_contents('fvgs.txt', '');
file_put_contents('alarmlog.txt', '');


var_dump(analyseAlarm('APTUSDT.P RSI-Divergence Bullish'));
var_dump(analyseAlarm('APTUSDT.P FVG Dedeted size: 0.60% [tf:5,ts:1750402800000,dir:bull,type:FVG-DETECTED,size:0.6]'));// Friday, 20. June 2025 07:00:00
var_dump(analyseAlarm('ONDO.P FVG Dedeted size: 0.70% [tf:5,ts:1750402800000,dir:bull,type:FVG-DETECTED,size:0.7]'));
var_dump(analyseAlarm('ONDO.P FVG Dedeted size: 0.70% [tf:5,ts:1750402800000,dir:bull,type:FVG-NEAR,size:0.7,ratio:4.2]'));

var_dump(analyseAlarm('BTCUSD.P FVG Dedeted size: 0.50% [tf:5,ts:1750403700000,dir:bear,type:FVG-DETECTED,size:0.5]'));
var_dump(analyseAlarm('BTCUSD.P FVG Dedeted size: 0.50% [tf:5,ts:1750403700000,dir:bear,type:FVG-NEAR,size:0.5,ratio:3.]'));
var_dump(analyseAlarm('BTCUSD.P FVG Dedeted size: 0.50% [tf:5,ts:1750403700000,dir:bear,type:FVG-INVALIDATED,size:0.5,ratio:3.]'));

var_dump(analyseAlarm('TAU.P FVG Dedeted size: 1.90% [tf:5,ts:1750410824000,dir:bull,type:FVG-DETECTED,size:1.8]'));
var_dump(analyseAlarm('TAU.P FVG Dedeted size: 1.90% [tf:5,ts:1750410824000,dir:bull,type:FVG-ENTER,size:1.8,ratio:2.2]'));

var_dump(file_get_contents('alarmlog.txt'));
var_dump(file_get_contents('fvgs.txt'));
var_dump(readFvgs());
;

?>