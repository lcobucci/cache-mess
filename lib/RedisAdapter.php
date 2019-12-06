<?php
declare(strict_types=1);

namespace Lcobucci\CacheStuff;

use Psr\SimpleCache\CacheInterface;
use Redis;
use Traversable;
use function array_filter;
use function array_keys;
use function array_map;
use function count;
use function defined;
use function extension_loaded;
use function is_string;
use function iterator_to_array;
use function strpbrk;

final class RedisAdapter implements CacheInterface
{
    private const NAMESPACE_CACHEKEY = 'NamespaceCacheKey:%s';
    private const FALSE_VALUE = '~_b:0;';

    private Redis $redis;
    private string $namespace;
    private int $namespaceVersion;

    public function __construct(Redis $redis, string $namespace = '')
    {
        $this->redis = $redis;
        $this->namespace = $namespace;

        $redis->setOption(Redis::OPT_SERIALIZER, self::getSerializerValue());
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $id = $this->getNamespacedId($key);
        $value = $this->redis->get($id);

        if ($value === false) {
            return $default;
        }

        return $value === self::FALSE_VALUE ? false : $value;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $id = $this->getNamespacedId($key);

        if ($value === false) {
            $value = self::FALSE_VALUE;
        }

        if ($ttl === null) {
            return $this->redis->set($id, $value);
        }

        if ($ttl <= 0) {
            return $this->redis->del($key);
        }

        return $this->redis->setex($id, $ttl, $value);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        $this->redis->del($this->getNamespacedId($key));

        return true;
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        static $namespaceCacheKey;
        $namespaceCacheKey = $namespaceCacheKey ?? self::NAMESPACE_CACHEKEY . $this->namespace;
        $namespaceVersion = $this->getNamespaceVersion() + 1;

        if ($this->redis->set($namespaceCacheKey, $namespaceVersion)) {
            $this->namespaceVersion = $namespaceVersion;

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        if ($keys === []) {
            return;
        }

        if ($keys instanceof Traversable) {
            $keys = iterator_to_array($keys, false);
        }

        $values = $this->redis->mget($this->getNamespacedIds($keys));

        foreach ($keys as $i => $key) {
            $value = $values[$i];

            if ($value === false) {
                yield $key => $default;

                continue;
            }

            yield $key => self::convertFromCache($value);
        }
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        if ($ttl === null) {
            $namespacedKeysAndValues = [];

            foreach ($values as $key => $value) {
                $namespacedKeysAndValues[$this->getNamespacedId($key)] = self::convertToCache($value);
            }

            return $this->redis->mset($namespacedKeysAndValues);
        }

        if ($ttl <= 0) {
            if ($values instanceof Traversable) {
                $values = iterator_to_array($values);
            }

            return $this->deleteMultiple(array_keys($values));
        }

        $this->getNamespaceVersion();

        $multi = $this->redis->multi(Redis::PIPELINE);

        foreach ($values as $key => $value) {
            $multi->setex($this->getNamespacedId($key), $ttl, self::convertToCache($value));
        }

        $succeeded = array_filter($multi->exec());

        return count($succeeded) === count($values);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        if ($keys === []) {
            return false;
        }

        if ($keys instanceof Traversable) {
            $keys = iterator_to_array($keys, false);
        }

        $this->redis->del($this->getNamespacedIds($keys));

        return true;
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return (bool) $this->redis->exists($this->getNamespacedId($key));
    }

    private function getNamespacedIds(array $ids): array
    {
        $prefix = $this->namespace . ':' . $this->getNamespaceVersion() . ':';

        return array_map(
            static function (string $id) use ($prefix) : string {
                static::validateKey($id);

                return $prefix . $id;
            },
            $ids
        );
    }

    private function getNamespacedId(string $id) : string
    {
        self::validateKey($id);

        return $this->namespace . ':' . $this->getNamespaceVersion() . ':' . $id;
    }

    private function getNamespaceVersion() : int
    {
        if (isset($this->namespaceVersion)) {
            return $this->namespaceVersion;
        }

        static $namespaceCacheKey;
        $namespaceCacheKey = $namespaceCacheKey ?? self::NAMESPACE_CACHEKEY . $this->namespace;

        $version = $this->redis->get($namespaceCacheKey);

        return $this->namespaceVersion = $version ?: 1;
    }

    private static function convertToCache($value)
    {
        return $value === false ? self::FALSE_VALUE : $value;
    }

    private static function convertFromCache($value)
    {
        return $value === self::FALSE_VALUE ? false : $value;
    }

    private static function getSerializerValue() : int
    {
        if (defined('Redis::SERIALIZER_IGBINARY') && extension_loaded('igbinary')) {
            return Redis::SERIALIZER_IGBINARY;
        }

        return Redis::SERIALIZER_PHP;
    }

    private static function validateKey($key) : void
    {
        if (! is_string($key) || '' === $key || strpbrk($key, '{}()/\@:') !== false) {
            throw new InvalidArgumentException('Invalid key given');
        }
    }
}
