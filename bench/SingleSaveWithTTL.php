<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use Lcobucci\CacheStuff\Psr16CacheEntry;
use PhpBench\Benchmark\Metadata\Annotations\AfterMethods;

/** @AfterMethods({"cleanup"}) */
final class SingleSaveWithTTL extends CacheComparison
{
    public function cleanup(): void
    {
        $this->psr16Roave->delete('save-with-ttl');
        $this->psr16Naive->delete('save-with-ttl');
        $this->psr6Symfony->deleteItem('save-with-ttl');
    }

    public function benchPsr16Roave(): void
    {
        $this->psr16Roave->set('save-with-ttl', new Psr16CacheEntry('a-simple-item'), 86400);
    }

    public function benchPsr16Naive(): void
    {
        $this->psr16Naive->set('save-with-ttl', new Psr16CacheEntry('a-simple-item'), 86400);
    }

    public function benchPsr6Symfony(): void
    {
        $item = $this->psr6SymfonyFactory->getItem('save-with-ttl')->set('a-simple-item')->expiresAfter(86400);

        $this->psr6Symfony->save($item);
    }
}
