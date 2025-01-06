<?php

declare(strict_types=1);

namespace Gadget\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheItemPoolCache implements CacheInterface
{
    /**
     * @var string[] $namespace
     */
    private array $namespace;


    /**
     * @param CacheItemPoolInterface $cache
     * @param string|string[] $namespace
     */
    public function __construct(
        private CacheItemPoolInterface $cache,
        string|array $namespace = []
    ) {
        $this->namespace = $this->genNamespace($namespace);
    }


    /**
     * @return CacheItemPoolInterface
     */
    protected function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }


    /**
     * @param string|string[] $namespace
     * @param string[] $current
     * @return string[]
     */
    protected function genNamespace(
        string|array $namespace,
        array $current = []
    ): array {
        $namespace = is_string($namespace)
            ? explode("::", $namespace)
            : $namespace;
        return [...$current, ...$namespace];
    }


    /**
     * @return string[]
     */
    public function getNamespace(): array
    {
        return $this->namespace;
    }


    /**
     * @param string|string[] $namespace
     * @param bool $replace
     * @return self
     */
    public function withNamespace(
        string|array $namespace,
        bool $replace = false
    ): self {
        return new self(
            $this->cache,
            $replace
                ? $this->genNamespace($namespace)
                : $this->genNamespace($namespace, $this->getNamespace())
        );
    }


    /**
     * @param string $key
     * @return string
     */
    protected function getKey(string $key): string
    {
        return hash('SHA256', implode('::', [...$this->getNamespace(), $key]));
    }


    /**
     * @param string $key
     * @return CacheItemInterface
     */
    protected function getItem(string $key): CacheItemInterface
    {
        return $this->getCache()->getItem($this->getKey($key));
    }


    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }


    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        $item = $this->getItem($key);
        return $item->isHit()
            ? $item->get()
            : $default;
    }


    /**
     * @template T
     * @param string $key
     * @param (callable(mixed $v):(T|null)) $toT
     * @param mixed $default
     * @return T|null
     */
    public function getT(
        string $key,
        callable $toT,
        mixed $default = null
    ): mixed {
        return $toT($this->get($key, $default));
    }


    /**
     * @param string $key
     * @param mixed $value
     * @param null|int|\DateInterval $ttl
     * @return bool
     */
    public function set(
        string $key,
        mixed $value,
        null|int|\DateInterval $ttl = null
    ): bool {
        $item = $this->getItem($key)
            ->set($value)
            ->expiresAfter($ttl);

        return $this->getCache()->save($item);
    }


    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->cache->deleteItem($this->getItem($key)->getKey());
    }


    /**
     * @param iterable<string> $keys
     * @param mixed $default
     * @return iterable<string, mixed>
     */
    public function getMultiple(
        iterable $keys,
        mixed $default = null
    ): iterable {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }


    /**
     * @param iterable<mixed> $values
     * @param null|int|\DateInterval $ttl
     * @return bool
     */
    public function setMultiple(
        iterable $values,
        null|int|\DateInterval $ttl = null
    ): bool {
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $this->set($key, $value, $ttl);
            }
        }
        return true;
    }


    /**
     * @param iterable<string> $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }


    /**
     * @return bool
     */
    public function clear(): bool
    {
        return false;
    }
}
