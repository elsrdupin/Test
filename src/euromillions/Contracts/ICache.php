<?php
declare(strict_types=1);

namespace Euromillions\Contracts;

interface ICache
{
    public function put(string $json);
    public function get();
}
