<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\AfterMethods;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use Psr\Cache\CacheItemInterface;
use function assert;
use function count;
use function strrpos;
use function substr;

/**
 * @BeforeMethods({"populate"})
 * @AfterMethods({"cleanup"})
 */
final class MultiCacheHitAndMiss extends CacheComparison
{
    /** @var string[] */
    private array $keys = [];

    public function populate(): void
    {
        $this->init();

        for ($i = 0; $i < 1000; ++$i) {
            $key = 'item-for-retrieval-' . $i;

            $this->keys[] = $key;

            if ($i % 15 === 0) {
                continue;
            }

            $value = $i % 10 === 0 ? false : 'retrieve-me';

            $this->psr16Roave->set($key, $value);
            $this->psr6Symfony->save($this->psr6ItemFactory->getItem($key)->set($value));
        }
    }

    public function cleanup(): void
    {
        $this->psr16Roave->deleteMultiple($this->keys);
        $this->psr6Symfony->deleteItems($this->keys);
    }

    public function benchPsr16Roave(): void
    {
        $items = $this->psr16Roave->getMultiple($this->keys);
        $count = 0;

        foreach ($items as $key => $item) {
            ++$count;

            $expectedValue = $this->getExpectedValue($key);
            assert($item === $expectedValue);
        }

        assert($count === count($this->keys));
    }

    public function benchPsr6Symfony(): void
    {
        $items = $this->psr6Symfony->getItems($this->keys);
        $count = 0;

        foreach ($items as $key => $item) {
            assert($item instanceof CacheItemInterface);

            ++$count;

            $expectedValue = $this->getExpectedValue($key);
            assert($item->get() === $expectedValue);
            assert($item->isHit() === ($expectedValue !== null));
        }

        assert($count === count($this->keys));
    }

    /** @return bool|string|null */
    private function getExpectedValue(string $key)
    {
        $i = (int) substr($key, strrpos($key, '-') + 1);

        if ($i % 15 === 0) {
            return null;
        }

        return $i % 10 === 0 ? false : 'retrieve-me';
    }
}
