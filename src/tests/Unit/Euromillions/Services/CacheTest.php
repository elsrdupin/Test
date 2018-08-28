<?php

namespace Tests\Unit\Euromillions\Services;

use Illuminate\Support\Facades\Cache;

use \InvalidArgumentException;
use \StdClass;
use \DateTime;
use \RuntimeException;
use \Exception;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;


use Tests\TestCase;

class CacheTest extends TestCase
{
    const DRAWS_TABLE = 'euromillions_draws';
    protected $sut;
    
    protected $proxyMock;

    public function setUp():void
    {
        parent::setUp();
        // HACK:: Hedious work-around to gain access to the container
        if (!isset($this->sut)) {
            $this->sut = $this->app->make('Euromillions\Contracts\ICacheWithMockProxy');
            // $this->proxyMock = new Mio();
        }
        DB::table(self::DRAWS_TABLE)->delete();
        Cache::flush();
    }
 
    protected function tearDown()
    {
        DB::table(self::DRAWS_TABLE)->delete();
    }

    public function testPutRejectsInvalidJsonString()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = $this->sut->put("");
        $this->assertTrue(false);
    }

    public function testPutDecodesAndPersistsValidJsonString()
    {
        DB::table(self::DRAWS_TABLE)->insert([
            'draw_date' =>DateTime::createFromFormat('Y-m-d', '2018-08-23'),
            'result_regular_number_one' => 100,
            'result_regular_number_two' =>14,
            'result_regular_number_three' =>31,
            'result_regular_number_four' =>36,
            'result_regular_number_five' =>50,
            'result_lucky_number_one' =>01,
            'result_lucky_number_two' =>12
            ]);

        $result = $this->sut->put('{
                "error" : 0,
                "draw" : "2018-08-24",
                "results" : "300,14,31,36,50,01,12"
                }');



        $this->assertEquals(300, $result['result_regular_number_one']);
    }


    public function testRetrieveLastKnownDrawComesFromDbTheFirtsTime()
    {
        Cache::forget('referential_key');
        
        DB::table(self::DRAWS_TABLE)->insert([
            [
            'draw_date' =>DateTime::createFromFormat('Y-m-d', '2100-08-24'),
            'result_regular_number_one' => 100,
            'result_regular_number_two' =>14,
            'result_regular_number_three' =>31,
            'result_regular_number_four' =>36,
            'result_regular_number_five' =>50,
            'result_lucky_number_one' =>01,
            'result_lucky_number_two' =>12,
            ],
            [
            'draw_date' =>DateTime::createFromFormat('Y-m-d', '2101-08-24'),
            'result_regular_number_one' => 200,
            'result_regular_number_two' =>14,
            'result_regular_number_three' =>31,
            'result_regular_number_four' =>36,
            'result_regular_number_five' =>50,
            'result_lucky_number_one' =>01,
            'result_lucky_number_two' =>12,
            ]
        ]);
                
    
        $result = $this->sut->retrieveLastKnownDraw();
        $this->assertEquals(200, $result['result_regular_number_one']);
    }

    public function testToDBRepresentationCreatesValidRepresentation()
    {
        $decoded = json_decode('{
                "error" : 0,
                "draw" : "2018-08-24",
                "results" : "201,14,31,36,50,01,12"
                }');

        // TODO: This assertion doesn't work well..
        $result = $this->sut->toDBRepresentation($decoded);
        $this->assertEquals(201, $result['result_regular_number_one']);
    }
        
    public function testGetAskApiForNewValues(){
        $result = $this->sut->get();
        $this->assertEquals(500, $result['result_regular_number_one']);

    }
}