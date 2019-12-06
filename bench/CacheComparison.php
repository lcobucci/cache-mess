<?php
declare(strict_types=1);

namespace Lcobucci\CacheBench;

use Doctrine\Common\Cache\RedisCache as DoctrineRedis;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use Psr\Cache\CacheItemPoolInterface as PSR6Cache;
use Psr\SimpleCache\CacheInterface as PSR16Cache;
use Redis;
use Roave\DoctrineSimpleCache\SimpleCacheAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter as ItemFactory;
use Symfony\Component\Cache\Adapter\RedisAdapter as PSR6Redis;
use function getenv;

/** @BeforeMethods({"init"}) */
abstract class CacheComparison
{
    protected PSR16Cache $psr16Roave;
    protected PSR6Cache $psr6Symfony;
    protected PSR6Cache $psr6SymfonyFactory;

    public function init(): void
    {
        $redis = new Redis();
        $redis->connect(getenv('REDIS_HOST'));

        $this->psr6SymfonyFactory = new ItemFactory();
        $this->psr6Symfony        = new PSR6Redis($redis, 'psr6Symfony');
        $this->psr6Symfony->enableVersioning();

        $provider = new DoctrineRedis();
        $provider->setNamespace('psr16Roave');
        $provider->setRedis($redis);

        $this->psr16Roave = new SimpleCacheAdapter($provider);
    }
}
