<?php
//https://houdunren.gitee.io/note/php/6%20%E6%96%87%E4%BB%B6%E4%B8%8E%E7%9B%AE%E5%BD%95.html#%E8%87%AA%E5%8A%A8%E6%B7%BB%E5%8A%A0%E5%8D%95%E4%BD%8D
class Tools
{
    /**
     * 获取有单位的大小
     * @param int $total 大小单位字节
     * @return string|null
     */
    public static function space_total(int $total):?string {
        $arr = [1=>'KB' , 2=>'MB',3=>'GB'];
        foreach ($arr as $k => $v){
            if($total > pow(1024 , $k)){
                return round($total / pow(1024 , $k)) . $v;
            }
        }
        return '0KB';
    }
}

//round — 对浮点数进行四舍五入

//pow — 指数表达式
//  pow ( number $base , number $exp ) : number
// 返回 base 的 exp 次方的幂。如果可能，本函数会返回 integer。

//disk_total_space  返回指定目录的磁盘总大小。

echo Tools::space_total(disk_total_space('.'));