<?php
require_once('../Model/Player.php');
require_once('../Model/Card.php');

class PrintService
{
    public function printEnter()
    {
        echo("\n");
    }

    public function printText(string $text)
    {
        echo $text;
    }
}