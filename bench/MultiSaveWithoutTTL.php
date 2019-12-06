<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\AfterMethods;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use function array_keys;
use function assert;

/**
 * @BeforeMethods({"populate"}, extend=true)
 * @AfterMethods({"cleanup"})
 */
final class MultiSaveWithoutTTL extends CacheComparison
{
    /** @var string[] */
    private array $items = [];

    public function populate(): void
    {
        for ($i = 0; $i < 1000; ++$i) {
            $this->items['save-without-ttl-' . $i] = 'a-simple-item';
        }
    }

    public function cleanup(): void
    {
        $keys = array_keys($this->items);

        $this->psr16Roave->deleteMultiple($keys);
        $this->psr16Naive->deleteMultiple($keys);
        $this->psr6Symfony->deleteItems($keys);
    }

    public function benchPsr16Roave(): void
    {
        assert($this->psr16Roave->setMultiple($this->items) === true);
    }

    public function benchPsr16Naive(): void
    {
        assert($this->psr16Naive->setMultiple($this->items) === true);
    }

    public function benchPsr6Symfony(): void
    {
        foreach ($this->items as $key => $value) {
            $this->psr6Symfony->saveDeferred($this->psr6SymfonyFactory->getItem($key)->set($value));
        }

        $this->psr6Symfony->commit();
    }
}
