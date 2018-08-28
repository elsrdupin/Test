<?php
declare(strict_types=1);

namespace Euromillions\Contracts;

interface IResultApi
{
    public function fetch():string;
}
