<?php

/**
+----------------------------------------------------------
 * 导入所需的类库 同java的Import
 * 本函数有缓存功能
+----------------------------------------------------------
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
+----------------------------------------------------------
 * @return boolen
+----------------------------------------------------------
 */
function import($class, $baseUrl = '', $ext='.class.php') {
    static $_file = array();
    $class = str_replace(array('.', '#'), array('/', '.'), $class);
    if ('' === $baseUrl && false === strpos($class, '/')) {
        // 检查别名导入
        return alias_import($class);
    }
    if (isset($_file[$class . $baseUrl]))
        return true;
    else
        $_file[$class . $baseUrl] = true;
    $class_strut = explode('/', $class);
    if (empty($baseUrl)) {
        if ('@' == $class_strut[0] || APP_NAME == $class_strut[0]) {
            //加载当前项目应用类库
            $baseUrl = dirname(LIB_PATH);
            $class = substr_replace($class, basename(LIB_PATH).'/', 0, strlen($class_strut[0]) + 1);
        }elseif ('think' == strtolower($class_strut[0])){ // think 官方基类库
            $baseUrl = CORE_PATH;
            $class = substr($class,6);
        }elseif (in_array(strtolower($class_strut[0]), array('org', 'com'))) {
            // org 第三方公共类库 com 企业公共类库
            $baseUrl = LIBRARY_PATH;
        }else { // 加载其他项目应用类库
            $class = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
            $baseUrl = APP_PATH . '../' . $class_strut[0] . '/'.basename(LIB_PATH).'/';
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl .= '/';
    $classfile = $baseUrl . $class . $ext;
    if (!class_exists(basename($class),false)) {
        // 如果类不存在 则导入类库文件
        return require_cache($classfile);
    }
}

/**
 * 导出EXCEL表格
 * @param array $data 数据，二维数组，每条数据一条记录
 * @param array $title 每列数据的字段名，一唯数组，必须和数据顺序一致（可省略）
 * @param string $filename excel名称
 * @param array $field 需要指定导出的数据字段，排序必须和title一致，就是和查出数据的数组key值
 */
function exportExcel($data='',$title='',$filename='excel',$field=array()){
    if(!$data || !is_array($data)) return false;
    if($filename=='') $filename='excel';
    if($field && is_array($field)){//只要导出指定字段，且按这个顺序导出
        $dateNew=array();
        foreach ($data as $k=>$v){
            foreach ($field as $fkey){
                $dateNew[$k][$fkey]=$v[$fkey];
            }
        }
        $data=$dateNew;
    }

    import("@.ORG.Util.ExcelXml");//调用导出excel类
    $xls = new ExcelXml('UTF-8', false, 'Sheet1');
    $xls->addArray($data,$title);
    $xls->generateXML($filename);
}
?>