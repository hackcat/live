<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 12:53
 */
namespace App\Lib;

class Util
{
    public static function info($msg)
    {
        $consoleColor=new \JakubOnderka\PhpConsoleColor\ConsoleColor();
        echo $consoleColor->apply("white", date("Y-m-d H:i:s")." ".$msg."\r\n");
        unset($consoleColor);
    }

    public static function error($msg)
    {
        $consoleColor=new \JakubOnderka\PhpConsoleColor\ConsoleColor();
        echo $consoleColor->apply("red", date("Y-m-d H:i:s")." ".$msg."\r\n");
        unset($consoleColor);
    }

    public static function success($msg)
    {
        $consoleColor=new \JakubOnderka\PhpConsoleColor\ConsoleColor();
        echo $consoleColor->apply("light_green", date("Y-m-d H:i:s")." ".$msg."\r\n");
        unset($consoleColor);
    }
}