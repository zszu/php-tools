<?php
declare(strict_types=1);
function handle(int $a){
    return $a;
}
function a():int
{
    return 1;
}
function a2():?int
{
    return null;
}
function make():string {
    return '100';
}

try {
//    handle('111');
//    print_r(a());
//    print_r(a2());
    print_r(make());
}catch (Throwable $t){
    echo $t->getMessage();
}