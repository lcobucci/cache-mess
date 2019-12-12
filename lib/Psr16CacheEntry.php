<?php

declare(strict_types=1);

namespace Lcobucci\CacheStuff;

/**
 * This class is necessary for PSR-16 to correctly handle null values, otherwise there is no way to differentiate
 * saved null value from cache miss.
 */
final class Psr16CacheEntry
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
