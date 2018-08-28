<?php

namespace Tests\Unit\Euromillions\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class HttpsutTest extends TestCase
{
    protected $sut;
    
    public function setUp():void
    {
        parent::setUp();
        // HACK:: Hedious work-around to gain access to the container
        if (!isset($this->sut)) {
            $this->sut = $this->app->make('Euromillions\Contracts\IResultsApi');
        }
    }
 
    public function testFetchIteratesThrougUrls()
    {        
        $success = "{\r\n\"error\" : 0,\r\n\"draw\" : \"2018-08-24\",\r\n\"results\" : \"03,14,31,36,50,01,12\"\r\n}\r\n";
        $this->assertEquals($success, $this->sut->fetch());
        $this->assertTrue(true);
    }

    public function testIsSuccesrResposeBody()
    {
        $success = '{
    "error" : 0,
    "draw" : "2018-08-24",
    "results" : "03,14,31,36,50,01,12"
    }';
        $error = '{
    "error" : 300,
    "draw" : "-",
    "results" : "-"
    }';
        $weird = 'Hola mundo';

        $this->assertTrue($this->sut->isSuccesrResposeBody($success));
        $this->assertNotTrue($this->sut->isSuccesrResposeBody($error));
        $this->assertNotTrue($this->sut->isSuccesrResposeBody($weird));
    }

}
