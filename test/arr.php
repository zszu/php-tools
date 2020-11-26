<?php
$users = [
    ['name'=>'test1','age'=>16],
    ['name'=>'test2','age'=>18],
    ['name'=>'test3','age'=>19],
];

function cache(string $name , array $data = null){
    $file = 'cache'.DIRECTORY_SEPARATOR.md5($name).'.txt';
    if(is_null($data)){
        $content = is_file($file) ? file_get_contents($file) : null;
        return unserialize($content) ? : null;
    }else{
        return file_put_contents($file , serialize($data));
    }
}

cache('test' , $users);
print_r(cache('test'));