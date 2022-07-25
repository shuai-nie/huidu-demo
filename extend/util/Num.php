<?php
namespace util;

class Num
{

    /**
     * 把数字1-1亿换成汉字表述，如：123->一百二十三
     * @param [num] $num [数字]
     * @return [string] [string]
     */
    public static function numToWord($num)
    {
        $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');
        $num_str = (string)$num;
        $count = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num = null; //临时数字
        $chiStr = '';//拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num] . $chiUni[1];
            $temp_num = $num_str[1];
            $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
        }else if($count > 2){
            $index = 0;
            for ($i=$count-1; $i >= 0 ; $i--) {
                $temp_num = $num_str[$i];
                if ($temp_num == 0) {
                    if (!$zero_flag && !$last_flag ) {
                        $chiStr = $chiNum[$temp_num] . $chiStr;
                        $last_flag = true;
                    }
                }else{
                    $chiStr = $chiNum[$temp_num] . $chiUni[$index % 9] . $chiStr;
                    $zero_flag = false;
                    $last_flag = false;
                }
                $index ++;
            }
        }else{
            $chiStr = $chiNum[$num_str[0]];
        }
        return $chiStr;
    }

    /**
     * 数字转换为中文
     * @param string|integer|float $num 目标数字
     * @param integer $mode 模式[true:金额(默认),false:普通数字表示]
     * @param boolean $sim 使用小写(默认)
     * @return string
     */
    public static function number2chinese($num,$mode = true,$sim = true){
        if(!is_numeric($num)) return '含有非数字非小数点字符！';
        $char = $sim ? array('零','一','二','三','四','五','六','七','八','九') : array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
        $unit = $sim ? array('','十','百','千','','万','亿','兆') : array('','拾','佰','仟','','萬','億','兆');
        $retval = $mode ? '元':'点';
        $out = [];
        //小数部分
        if(strpos($num, '.')){
            list($num,$dec) = explode('.', $num);
            $dec = strval(round($dec,2));
            if($mode){
                $retval .= "{$char[$dec['0']]}角{$char[$dec['1']]}分";
            }else{
                for($i = 0,$c = strlen($dec);$i < $c;$i++) {
                    $retval .= $char[$dec[$i]];
                }
            }
        }
        //整数部分
        $str = $mode ? strrev(intval($num)) : strrev($num);
        for($i = 0,$c = strlen($str);$i < $c;$i++) {
            $out[$i] = $char[$str[$i]];
            if($mode){
                $out[$i] .= $str[$i] != '0'? $unit[$i%4] : '';
                if($i>1 and $str[$i]+$str[$i-1] == 0){
                    $out[$i] = '';
                }
                if($i%4 == 0){
                    $out[$i] .= $unit[4+floor($i/4)];
                }
            }
        }

        return join('', array_reverse($out)) . $retval;
    }

}