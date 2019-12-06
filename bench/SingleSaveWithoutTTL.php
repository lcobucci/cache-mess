<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\AfterMethods;

/** @AfterMethods({"cleanup"}) */
final class SingleSaveWithoutTTL extends CacheComparison
{
    public function cleanup(): void
    {
        $this->psr16Roave->delete('save-without-ttl');
        $this->psr16Naive->delete('save-without-ttl');
        $this->psr6Symfony->deleteItem('save-without-ttl');
    }

    public function benchPsr16Roave(): void
    {
        $this->psr16Roave->set('save-without-ttl', 'a-simple-item');
    }

    public function benchPsr16Naive(): void
    {
        $this->psr16Naive->set('save-without-ttl', 'a-simple-item');
    }

    public function benchPsr6Symfony(): void
    {
        $this->psr6Symfony->save($this->psr6SymfonyFactory->getItem('save-without-ttl')->set('a-simple-item'));
    }
}
