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
final class MultiCacheHit extends CacheComparison
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
            $this->psr6Symfony->save($this->psr6ItemFactory->getItem($key)->set('retrieve-me'));
        }
    }

    public function cleanup(): void
    {
        $this->psr16Roave->deleteMultiple($this->keys);
        $this->psr6Symfony->deleteItems($this->keys);
    }

    public function benchPsr16Roave(): void
    {
        foreach ($this->psr16Roave->getMultiple($this->keys) as $item) {
            assert($item === 'retrieve-me');
        }
    }

    public function benchPsr6Symfony(): void
    {
        foreach ($this->psr6Symfony->getItems($this->keys) as $item) {
            assert($item->get() === 'retrieve-me');
        }
    }
}
