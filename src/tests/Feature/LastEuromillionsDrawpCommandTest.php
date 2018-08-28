<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class LastEuromillionsDrawpCommandTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testInspiringCommand(): void
    {
        Artisan::call('lastdraw');

        $this->assertContains('Results for date ', Artisan::output());
    }
}
