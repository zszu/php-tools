<?php
/**
 * CSV 文件处理类
 */
class Csv{
    public $csv_array; //csv数组数据
    public $csv_str;  //csv文件数据
    private $inEncode;
    private $outEncode;
    public function __construct($param_arr,$path,$column ){
        $this->csv_array = $param_arr;
        $this->path = $path;
        $this->column = $column;
    }

    /**
     * 导出
     * */
    public function export(){
        if(empty($this->csv_array) || empty($this->column)){
            return false;
        }
        $param_arr = $this->csv_array;

        //组装数据
        $content = '';
        foreach($param_arr as $k=>$v){

            foreach ($v as $vv){
                if(is_array($vv)){
                    $content .= $this->setRow($vv);
                }else{
                    $content .= $this->setRow($v);
                    break;
                }
            }

        }

        $filename = date('Y_m_d_'.time() , time());

        //将$export_str导出
        header( "Cache-Control: public" );
        header( "Pragma: public" );
        header("Content-type:application/vnd.ms-csv");
        header("Content-Disposition:attachment;filename=$filename.csv");
        header('Content-Type:APPLICATION/OCTET-STREAM');
        ob_start();
        if($this->inEncode!=$this->outEncode){
            $content = iconv($this->inEncode,$this->outEncode,$content);
        }
        ob_end_clean();
        echo $content;
    }

    /**
     * 导入
     * */
    public function import($path,$title = 'title' , $res = 'data'){
        $column = 3;
        $flag = false;
        $code = 0;
        $msg = '未处理';
        $filesize = 1; //1MB
        $maxsize = $filesize * 1024 * 1024;
        $max_column = 1000;

        //检测文件是否存在
        if($flag === false){
            if(!file_exists($path)){
                $msg = '文件不存在';
                $flag = true;
            }
        }
        //检测文件格式
        if($flag === false){

//            $ext = preg_replace("/.*.([^.]+)/","$1",$path);
            $ext = substr(strrchr($path , '.') , 1);

            if($ext != 'csv'){
                $msg = '只能导入CSV格式文件';
                $flag = true;
            }
        }

        //检测文件大小
        if($flag === false){
            if(filesize($path)>$maxsize){
                $msg = '导入的文件不得超过'.$maxsize.'B文件';
                $flag = true;
            }
        }

        //读取文件
        if($flag == false){
            $row = 0;
            $handle = fopen($path,'r');
            $dataArray = [];
            $titleArr = [];

            while(($data = fgetcsv($handle,$max_column,",")) !== false){
                $data = array_flip(array_flip($data));

                $num = count($data);
                if($row == 0){
                    //表头
                    $data = implode(',' , $data);
                    $data = explode(',' , $data);
                    $titleArr = $data;
                    $row++;
                }else if($flag === false){

                    //组建数据
                    if(empty($data)) continue;
                    $dataArray[$row] = $data;
                    $row++;

                }
            }
        }

        if($flag){
            return $msg;
        }

        return [$title => $titleArr, $res => $dataArray];
    }
    //设置 每行数据
    private function setRow($arr){

        $rowContent="";

        $rowContent = implode(',' , $arr);

        $rowContent.="\n";

        return rtrim($rowContent , ',');
    }


}

$param_arr = [
    'title' => ['用户名','密码','邮箱'],
    'data' =>
        ['xiaohai1','123456','xiaohai1@zhongsou.com'],
        ['xiaohai2','223456','xiaohai1@zhongsou.com'],
        ['xiaohai3','323456','xiaohai1@zhongsou.com'],
];

$column = 3;
$csv = new Csv($param_arr,'./', $column);

$csv->export();
$path = './test.csv';
//$import_arr = $csv->import($path);
//var_dump($import_arr);

?>

