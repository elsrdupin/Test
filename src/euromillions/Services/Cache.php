<?php
declare(strict_types=1);

namespace Euromillions\Services;

use Illuminate\Support\Facades\DB;

use \InvalidArgumentException;
use \StdClass;
use \DateTime;

use Illuminate\Support\Facades\Cache as BaseCache;

use Euromillions\Contracts\IResultApi;
use Euromillions\Contracts\ICache;

class Cache implements ICache
{
    const VOLATILE_KEY = 'volatile_key';
    const REFERENTIAL_KEY = 'referential_key';
    const DRAWS_TABLE = 'euromillions_draws';

    protected $expire;
    protected $proxy;

    public function __construct(IResultApi $proxy)
    {
        $this->proxy = $proxy;
        $this->expire = config('euromillions.refresh');
    }

    public function put(string $json)
    {
        if ($decoded= $this->parseJsonString($json)) {
            $lastKnownDraw =  $this->retrieveLastKnownDraw();
            if (empty($lastKnownDraw) || $lastKnownDraw['draw_date'] !== $decoded['draw_date']->format('Y-m-d')) {
                $lastKnownDraw = $this->saveAndChacheAsLastKnownDraw($decoded);
            }
            return $lastKnownDraw;
        }
    }

    private function parseJsonString(string $str)
    {
        $decoded = json_decode($str);
        if (is_null($decoded)) {
            throw new InvalidArgumentException('json string cannot be decoded');
        }
        if (!$decoded->error) {
            return $this->toDBRepresentation($decoded);
        } else {
            throw new InvalidArgumentException("Parsed json string contains errors. Error code $decoded->error");
        }
        return $decoded;
    }

    // HACK:: Public just for unit testing purpouses
    public function toDBRepresentation($decoded)
    {
        $outcome = [
            'draw_date' => DateTime::createFromFormat('Y-m-d', $decoded->draw)
        ];

        $numberFields = [
            'result_regular_number_one',
            'result_regular_number_two',
            'result_regular_number_three',
            'result_regular_number_four',
            'result_regular_number_five',
            'result_lucky_number_one',
            'result_lucky_number_two'
         ];

        foreach (explode(',', $decoded->results) as $index => $number) {
            if (isset($numberFields[$index])) {
                $field = $numberFields[$index];
                $outcome[$field] = intval($number);
            } else {
                break;
            }
        }
        return $outcome;
    }

    // HACK: Public for unit testin purpouses
    public function retrieveLastKnownDraw()
    {
        return BaseCache::rememberForever(self::REFERENTIAL_KEY, function () {
            $dbRecord = DB::table(self::DRAWS_TABLE)
                ->orderBy('draw_date', 'desc')
                ->first();
            return  (array) $dbRecord;
        });
    }

    // HACK: Public for unit testin purpouses
    public function saveAndChacheAsLastKnownDraw($draw)
    {
        BaseCache::forget(self::REFERENTIAL_KEY);
        $lastKnownDraw = BaseCache::rememberForever(self::REFERENTIAL_KEY, function () use ($draw) {
            try {
                DB::table(self::DRAWS_TABLE)
                ->insert($draw);
            } catch (Illuminate\Database\QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode !== 1062) {
                    throw $e;
                }
            } finally {
                return  (array) $draw;
            }
        });
        return $lastKnownDraw;
    }
    

    public function get()
    {
        return  BaseCache::remember(self::VOLATILE_KEY, $this->expire, function () {
            
            // HACK: We know this means 'NOT EXISTS' for remember'
            $newValue = null;
            try {
                $fromApi = $this->proxy->fetch();
                $newValue = null;
                if (!empty($fromApi)) {
                    $newValue = $this->put($fromApi);
                }
            } catch (Throwable $t) {
                // TODO: Make some log
            }
            return $newValue;
        });
    }
}
