<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\AfterMethods;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use function assert;

/**
 * @BeforeMethods({"populate"})
 * @AfterMethods({"cleanup"})
 */
final class SingleCacheHit extends CacheComparison
{
    public function populate(): void
    {
        $this->init();

        $this->psr16Roave->set('item-for-retrieval', 'retrieve-me');
        $this->psr16Naive->set('item-for-retrieval', 'retrieve-me');
        $this->psr6Symfony->save($this->psr6SymfonyFactory->getItem('item-for-retrieval')->set('retrieve-me'));
    }

    public function cleanup(): void
    {
        $this->psr16Roave->delete('item-for-retrieval');
        $this->psr16Naive->delete('item-for-retrieval');
        $this->psr6Symfony->delete('item-for-retrieval');
    }

    public function benchPsr16Roave(): void
    {
        assert($this->psr16Roave->get('item-for-retrieval') === 'retrieve-me');
    }

    public function benchPsr16Naive(): void
    {
        assert($this->psr16Naive->get('item-for-retrieval') === 'retrieve-me');
    }

    public function benchPsr6Symfony(): void
    {
        assert($this->psr6Symfony->getItem('item-for-retrieval')->get() === 'retrieve-me');
    }
}
