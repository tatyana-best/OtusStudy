<?php

namespace MyDebug;

class debugFirst
{
    public function printVar(array $var): void
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
