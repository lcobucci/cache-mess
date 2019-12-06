<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use function assert;

final class SingleCacheMiss extends CacheComparison
{
    public function benchPsr16Roave(): void
    {
        assert($this->psr16Roave->get('item-for-retrieval') === null);
    }

    public function benchPsr16Naive(): void
    {
        assert($this->psr16Naive->get('item-for-retrieval') === null);
    }

    public function benchPsr6Symfony(): void
    {
        assert($this->psr6Symfony->getItem('item-for-retrieval')->get() === null);
    }
}
