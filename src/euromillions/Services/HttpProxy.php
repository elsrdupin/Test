<?php
declare(strict_types=1);

namespace Euromillions\Services;

use \GuzzleHttp\Client;

use Euromillions\Contracts\IResultApi;

class HttpProxy implements IResultApi
{
    const SUCCESS_RESPONSE_BODY_PATT = '/\"error\"\s*\:\s*0/m';
    protected $client;
    protected $urls;

    public function __construct($urls=[])
    {
        $this->client = new Client();
        $this->urls = $urls;
    }
    public function fetch():String
    {        
        $outcome = '';
        foreach ($this->urls as $url) {            
            try {
                $res = $this->client->request('GET', $url);
                $body = $res->getBody()->getContents();
               
                if (
                    $res->getStatusCode() == 200
                    && $res->getHeaderLine('content-type') ==   'application/json; charset=UTF-8'
                    && $this->isSuccesrResposeBody($body)
                ) {
                    $outcome = $body;
                    break;
                }
            } catch (\GuzzleHttp\Exception\ConnectException $t) {
                // TODO: Make some log
                continue;
            } catch (Throwable $t) {
                 // TODO: Make some log
                continue;
            }
        }
        return $outcome;
    }
    // HACK : Public just for unit testing purpouses
    public function isSuccesrResposeBody($body)
    {
        $outcome = false;
        if (preg_match(self::SUCCESS_RESPONSE_BODY_PATT, $body)) {
            $outcome = true;
        }
        return $outcome;
    }
}
