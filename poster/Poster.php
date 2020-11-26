<?php
class Poster
{
    private $backgroundResource; //背景图片资源
    private $color;
    private $width;
    private $height;

    private $imageConfig = [
        'left'=>0,
        'top'=>0,
        'right'=>0,
        'bottom'=>0,
        'width'=>100,
        'height'=>100,
        'opacity'=>100
    ]; //二维码图片
    private $textConfig = [
        'text'=>'',
        'left'=>0,
        'top'=>0,
        'fontSize'=>32,       //字号
        'fontColor'=>'255,255,255', //字体颜色
        'angle'=>0,
    ]; //添加字体配置
    private $backgroundFile;
    private $isCreateFile = false; //是否生产图片

    private $config;

    public function __construct($config , $isCreateFile = false)
    {
        $this->config = $config;
        if(!empty($config['text']))
            $this->textConfig = array_merge($this->textConfig , $config['text']);

        if(!$config['backgroundFile']){
            echo '参数 backgroundFile没有配置';
        }

        $this->backgroundFile = $config['backgroundFile'];
        $this->isCreateFile = $isCreateFile ?? $this->isCreateFile;
    }
    public function showImg(){
        //第一步：设置背景图像
        $this->setBackgroundImage();
        //第二步：设置新增的图像
        if($this->getDept($this->config['image']) > 1){
            $this->setMutil($this->config['image'] ,'setFillImage' );
        }else{
            $this->setFillImage($this->config['image']);
        }
        //第三步：设置图像中文本
        if($this->getDept($this->config['text']) > 1){
            $this->setMutil($this->config['text'] ,'outputText' );
        }else{
            $this->outputText($this->config['text']);
        }
        //第四步：输出图像
        $this->outputImage();
    }
    //设置背景图像
    private function setBackgroundImage(){
        $info = getimagesize($this->backgroundFile);
        $func = 'imagecreatefrom' . image_type_to_extension($info[2] , false);
        $_backgroundResource = $func($this->backgroundFile);
        $this->width = imagesx($_backgroundResource);
        $this->height = imagesy($_backgroundResource);
        //创建画布
        $this->backgroundResource = imagecreatetruecolor($this->width , $this->height);
        //创建颜色
        $this->color = imagecolorallocate($this->backgroundResource , 0 , 0 ,0);
        //填充颜色
        imagefill($this->backgroundResource , 0 , 0 ,$this->color);
        //图片裁剪
        imagecopyresampled($this->backgroundResource,$_backgroundResource,0,0,0,0,$this->width,$this->height,$this->width,$this->height);
    }

    //设置 多个新增的图像 或者 文字
    private function setMutil($config , $function){
        foreach ($config as $v){
            $this->$function($v);
        }
    }
    //设置新增的图像
    private function setFillImage($config){
        $config = array_merge($this->imageConfig , $config);
        $info = getimagesize($config['url']);

        $function = 'imagecreatefrom'.image_type_to_extension($info[2], false);

        if($config['stream']){   //如果传的是字符串图像流
            $info = getimagesizefromstring($config['url']);
            $function = 'imagecreatefromstring';
        }
        $res = $function($config['url']);
        $resWidth = $info[0];

        $resHeight = $info[1];

        //建立画板 ，缩放图片至指定尺寸
        $canvas=imagecreatetruecolor($config['width'], $config['height']);
        imagefill($canvas, 0, 0, $this->color);
        //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
        imagecopyresampled($canvas, $res, 0, 0, 0, 0, $config['width'], $config['height'],$resWidth,$resHeight);
        $config['left'] = $config['left']<0?$this->width- abs($config['left']) - $config['width']:$config['left'];
        $config['top'] = $config['top']<0?$this->width- abs($config['top']) - $config['height']:$config['top'];
        //放置图像
        //左，上，右，下，宽度，高度，透明度
        imagecopymerge($this->backgroundResource,$canvas, $config['left'],$config['top'],$config['right'],$config['bottom'],$config['width'],$config['height'],$config['opacity']);

    }
    //设置图像中文本
    private function outputText($config){

        list($R,$G,$B) = explode(',', $config['fontColor']);

        $fontColor = imagecolorallocate($this->backgroundResource, $R, $G, $B);

        $config['left'] = $config['left']<0?$this->width- abs($config['left']):$config['left'];

        $config['top'] = $config['top']<0?$this->height- abs($config['top']):$config['top'];

        imagettftext($this->backgroundResource,$config['fontSize'],$config['angle'],$config['left'],$config['top'],$fontColor,$config['fontPath'],$config['text']);
    }
    //输出图像
    private function outputImage(){
        //生成图片
        $filename = date('Y' , time()).'_' .date('m' , time()).'_'. date('d' , time()).'_' . '.png';
        if($this->isCreateFile){
            $res = imagejpeg ($this->backgroundResource,$filename,90); //保存到本地
            imagedestroy($this->backgroundResource);
            if(!$res) return false;
            return $filename;
        }else{
            header("content-type: image/png");
            imagejpeg ($this->backgroundResource);     //在浏览器上显示
        }

    }
    public function __destruct()
    {
        imagedestroy($this->backgroundResource);
    }

    //获取数组 的 维度
    private function getDept($array){
        $dept = 0;

        if(!is_array($array)) return $dept;
        else{
            foreach ($array as $value){
                $d = $this->getDept($value);
                $dept = $d > $dept ? $d : $dept;
            }
            return $dept+1;
        }
    }

}


$config = array(
    'text' => array(
        [
            'text' => '李珍珍',
            'left' => 182,
            'top'=>105,
            'fontSize'=>18,             //字号
            'fontColor'=>'255,0,0',       //字体颜色
            'angle'=>0,//字体倾斜度
            'fontPath' => 'C:\Windows\Fonts\msyh.ttc',

        ],
        [
            'text' => '特效它',
            'left' => 282,
            'top'=>105,
            'fontSize'=>18,             //字号
            'fontColor'=>'255,0,0',       //字体颜色
            'angle'=>0,//字体倾斜度
            'fontPath' => 'C:\Windows\Fonts\msyh.ttc',
        ]
    ),
    'image'=>array(
        array(
            'url'=>'assets/qrcode.png',     //二维码资源
            'stream'=>0,
            'left'=>600,
            'top'=>150,
            'right'=>0,
            'bottom'=>0,
            'width'=>150,
            'height'=>150,
            'opacity'=>100
        ),
        array(
            'url'=>'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eofD96opK97RXwM179G9IJytIgqXod8jH9icFf6Cia6sJ0fxeILLMLf0dVviaF3SnibxtrFaVO3c8Ria2w/0',
            'left'=>40,
            'top'=>150,
            'right'=>0,
            'stream'=>0,
            'bottom'=>0,
            'width'=>150,
            'height'=>150,
            'opacity'=>100
        ),
    ),
    'backgroundFile'=>'assets/bg.png'          //背景图
);

$p = new Poster($config);
echo $p->showImg();
