<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;

use LaravelZero\Framework\Commands\Command;

use Euromillions\Contracts\ICache;

class LastEuromillionsDrawpCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'lastdraw {name=Artisan}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display results of last Euromillions draw';

    /**
     * Create a new command instance.
     *
     * @param  Cache Handler  $drip
     * @return void
     */
    public function __construct(ICache $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }
    
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $results = $this->cache->get();
        if (empty($results)) {
            $this->error('Sorry, something unknow went wrong, try again later');
        } else {
            $this->info("Results for date ".$results['draw_date']->format('Y-m-d'));
            $this->showResults($results);
        }
    }

    protected function showResults($results)
    {
        $headers = $headers = ['Name', 'Result'];
        $body = [];
        foreach ($results as $name => $value) {
            if ($name !== 'draw_date') {
                $name = str_replace('_', ' ', title_case($name));
                $body[] = ['name' => $name,  'result'=>$value ];
            }
        }
        $this->table($headers, $body);
    }
    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
