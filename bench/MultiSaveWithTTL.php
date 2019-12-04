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
final class MultiSaveWithTTL extends CacheComparison
{
    /** @var string[] */
    private array $items = [];

    public function populate(): void
    {
        for ($i = 0; $i < 1000; ++$i) {
            $this->items['save-with-ttl-' . $i] = 'a-simple-item';
        }
    }

    public function cleanup(): void
    {
        $keys = array_keys($this->items);

        $this->psr16Roave->deleteMultiple($keys);
        $this->psr6Symfony->deleteItems($keys);
    }

    public function benchPsr16Roave(): void
    {
        assert($this->psr16Roave->setMultiple($this->items, 86400) === true);
    }

    public function benchPsr6Symfony(): void
    {
        foreach ($this->items as $key => $value) {
            $this->psr6Symfony->saveDeferred($this->psr6Symfony->getItem($key)->set($value)->expiresAfter(86400));
        }

        $this->psr6Symfony->commit();
    }
}
