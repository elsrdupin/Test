<?php
namespace Tests\Unit\Euromillions\Services;

use Euromillions\Contracts\IResultApi;

class HtppProxyMock implements IResultApi
{
    public function fetch():string
    {
        return '{
            "error" : 0,
            "draw" : "2018-08-24",
            "results" : "500,14,31,36,50,01,12"
            }';
    }
}
