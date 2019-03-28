<?php
/**
 * [get_progress 项目进度]
 * @param  {[type]} value [description]
 * @param  {[type]} row   [description]
 * @return {[type]}       [description]
 */
function get_progress($value='') {
    if($value == 0){
        return '未开始';
    }else if($value == 1){
        return '进展中';
    }else if($value == 2){
        return '停滞状态';
    }else if($value == 3){
        return '项目结束';
    }
}
/**
 * [get_baifenbi 项目进展度]
 * @param  string $value [description]
 * @return [type]        [description]
 */
function get_baifenbi($value='')
{
	if($value == 0){
        return '0';
    }else if($value == 1){
        return '10';
    }else if($value == 2){
        return '50';
    }else if($value == 3){
        return '100';
    }
}