<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use function assert;

/** @BeforeMethods({"populate"}) */
final class SingleRemove extends CacheComparison
{
    public function populate(): void
    {
        $this->init();

        $this->psr16Roave->set('item-for-removal', 'remove-me');
        $this->psr6Symfony->save($this->psr6SymfonyFactory->getItem('item-for-removal')->set('remove-me'));
    }

    public function benchPsr16Roave(): void
    {
        assert($this->psr16Roave->delete('item-for-removal') === true);
    }

    public function benchPsr6Symfony(): void
    {
        assert($this->psr6Symfony->deleteItem('item-for-retrieval') === true);
    }
}
