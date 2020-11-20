<?php

   /*
    *文件上传类
    **/
class Upload
{

    private $file_size;//上传源文件大小
    private $file_tem;//上传文件临时储存名
    private $file_name;//上传文件名
    private $file_type;//上传文件类型
    private $file_max_size=2000000;//允许文件上传最大
    private $file_folder="uploadFiles";//文件上传路径
    private $over_write=false;//是否覆盖同名文件
//允许上传图片的类型
    private $allow_type=array('image/jpg','image/jpeg','image/png','image/pjpeg','image/gif','image/bmp','image/x-png');


//构造类，file:上传文件域
    function __construct($file){
        $this->file_name=$_FILES[$file]['name'];//客户端原文件名
        $this->file_type=$_FILES[$file]['type'];//文件类型
        $this->file_tem=$_FILES[$file]['tmp_name'];//储存的临时文件名，一般是系统默认
        $this->file_size=$_FILES[$file]['size'];//文件大小
    }

//如果文件夹不存在则创建文件夹
    function creatFolder($f_path){
        if(!file_exists($f_path)){
            mkdir($f_path,0777);
        }
    }

//判断文件是不是超过上传限制
    function is_big(){
        if($this->file_size>$this->file_max_size){
            echo "文件太大，超过限制！";
            exit;
        }
    }

//检查文件类型
    function check_type(){
        if(!in_array($this->file_type,$this->allow_type)){
            echo "上传文件类型不正确";
            exit;
        }
    }

//检查文件是否存在
    function check_file_name(){
        if(!file_exists($this->file_tem)){
            echo "上传文件不存在";
            exit;
        }
    }

//检查是否有同名文件，是否覆盖
    function check_same_file($filename){
        if(file_exists($filename)&&$this->over_write!=true){
            echo "同名文件已存在！";
            exit;
        }
    }

//移动文件
    function move_file($filename,$destination){
        if(!move_uploaded_file($filename,$destination)){
            echo "移动文件出错";
            exit;
        }
    }

//检查文件是否是通过 HTTP POST 上传的
    function is_upload_file(){
        if(!is_uploaded_file($this->file_tem)){
            echo "文件不存在";
            exit;
        }
    }

//获得文件后缀名
    function getext(){
        $ext=$this->file_name;
        $extstr=explode('.',$ext);
        $count=count($extstr)-1;
        return $extstr[$count];
    }

//新建文件名
    function set_name(){
        return time().".".$this->getext();
    }

//建立以年月日为文件夹名
    function creat_mulu(){
        $this->creatFolder("../../upload/".date(Ymd));
        return "upload/".date(Ymd);
    }

//生成的文件名
    function files_name(){
        $name=$this->set_name();
        $folder=$this->creat_mulu();
        return "../../".$folder."/".$name;
    }

//上传文件
    function upload_file(){
        $f_name=$this->files_name();
        move_uploaded_file($this->file_tem,$f_name);
        return $f_name;
    }

//生成缩略图
//最大宽：120，高：120
    function create_simg($img_w,$img_h){
        $name=$this->set_name();
        $folder=$this->creat_mulu();
        $new_name="../../".$folder."/s_".$name;
        $imgsize=getimagesize($this->files_name());

        switch ($imgsize[2]){
            case 1:
                if(!function_exists("imagecreatefromgif")){
                    echo "你的GD库不能使用GIF格式的图片，请使用Jpeg或PNG格式！返回";
                    exit();
                }
                $im = imagecreatefromgif($this->files_name());
                break;
            case 2:
                if(!function_exists("imagecreatefromjpeg")){
                    echo "你的GD库不能使用jpeg格式的图片，请使用其它格式的图片！返回";
                    exit();
                }
                $im = imagecreatefromjpeg($this->files_name());
                break;
            case 3:
                $im = imagecreatefrompng($this->files_name());
                break;
            case 4:
                $im = imagecreatefromwbmp($this->files_name());
                break;
            default:
                die("is not filetype right");
                exit;
        }

        $src_w=imagesx($im);//获得图像宽度
        $src_h=imagesy($im);//获得图像高度
        $new_wh=($img_w/$img_h);//新图像宽与高的比值
        $src_wh=($src_w/$src_h);//原图像宽与高的比值
        if($new_wh<=$src_wh){
            $f_w=$img_w;
            $f_h=$f_w*($src_h/$src_w);
        }else{
            $f_h=$img_h;
            $f_w=$f_h*($src_w/$src_h);
        }
        if($src_w>$img_w||$src_h>$img_h){
            if(function_exists("imagecreatetruecolor")){//检查函数是否已定义
                $new_img=imagecreatetruecolor($f_w,$f_h);
                if($new_img){
                    imagecopyresampled($new_img,$im,0,0,0,0,$f_w,$f_h,$src_w,$src_h);//重采样拷贝部分图像并调整大小
                }else{
                    $new_img=imagecreate($f_w,$f_h);
                    imagecopyresized($new_img,$im,0,0,0,0,$f_w,$f_h,$src_w,$src_h);
                }
            }else{
                $$new_img=imagecreate($f_w,$f_h);
                imagecopyresized($new_img,$im,0,0,0,0,$f_w,$f_h,$src_w,$src_h);
            }
            if(function_exists('imagejpeg')){
                imagejpeg($new_img,$new_name);
            }else{
                imagepng($new_img,$new_name);
            }
            imagedestroy($new_img);
        }
//imagedestroy($new_img);
        return $new_name;
    }


}