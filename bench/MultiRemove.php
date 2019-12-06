<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use function assert;

/** @BeforeMethods({"populate"}) */
final class MultiRemove extends CacheComparison
{
    /** @var string[] */
    private array $keys = [];

    public function populate(): void
    {
        $this->init();

        for ($i = 0; $i < 1000; ++$i) {
            $key          = 'item-for-retrieval-' . $i;
            $this->keys[] = $key;

            $this->psr16Roave->set($key, 'retrieve-me');
            $this->psr16Naive->set($key, 'retrieve-me');
            $this->psr6Symfony->save($this->psr6SymfonyFactory->getItem($key)->set('retrieve-me'));
        }
    }

    public function benchPsr16Roave(): void
    {
        assert($this->psr16Roave->deleteMultiple($this->keys) === true);
    }

    public function benchPsr16Naive(): void
    {
        assert($this->psr16Naive->deleteMultiple($this->keys) === true);
    }

    public function benchPsr6Symfony(): void
    {
        assert($this->psr6Symfony->deleteItems($this->keys) === true);
    }
}
