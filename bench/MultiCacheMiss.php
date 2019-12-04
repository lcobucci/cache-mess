<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use function assert;
use function count;

/** @BeforeMethods({"populate"}) */
final class MultiCacheMiss extends CacheComparison
{
    /** @var string[] */
    private array $keys = [];

    public function populate(): void
    {
        $this->init();

        for ($i = 0; $i < 1000; ++$i) {
            $this->keys[] = 'item-for-retrieval-' . $i;
        }
    }

    public function benchPsr16Roave(): void
    {
        $items = $this->psr16Roave->getMultiple($this->keys);
        $count = 0;

        foreach ($items as $item) {
            ++$count;
            assert($item === null);
        }

        assert($count === count($this->keys));
    }

    public function benchPsr6Symfony(): void
    {
        $items = $this->psr6Symfony->getItems($this->keys);
        $count = 0;

        foreach ($items as $item) {
            ++$count;
            assert($item->get() === null);
            assert(! $item->isHit());
        }

        assert($count === count($this->keys));
    }
}
