<?php
/*
 * 正则匹配
 * /规则/
 */
/* ------------------------------*/
/*
 * 元字符            说明                                    范围
 * \d               匹配任意一个数字                         [0-9]
 * \D               与除了数字以外的任何一个字符匹配            [^0-9]
 * \w               与任意一个英文字母,数字或下划线匹配        [a-zA-Z_0-9]
 * \W               除了字母,数字或下划线外与任何字符匹配	     [^a-zA-Z_0-9]
 * \s	            与任意一个空白字符匹配	                 [\n\f\r\t\v]
 * \S	            与除了空白符外任意一个字符匹配	        [^\n\f\r\t\v]
 * \n	            换行字符
 * \t	            制表符
 */

//匹配 除了678外的任何字符
$rule = '/^678/';
$subject =  '678678678';
//匹配大小写字母
$rule = '/[a-zA-Z]/';
$subject =  'a';
$s = preg_match($rule , $subject);

var_dump($s);
$str = "官网www.houdunwang.com 论坛http://bbs.houdunwang.com，我在网名叫houdun";
$preg = "/(houdun)wang/is";
$newStr= preg_replace($preg, '<span style="color:#f00">\1</span>wang', $str);
echo $newStr;
$content = <<<str
<a href="https://www.houdunren.com">后看人</a>
<a href="https://www.hdcms.com">hdcms</a>
str;
echo preg_replace('/<a.*?>(.*?)<\/a>/i', '<h2>$1</h2>', $content);


