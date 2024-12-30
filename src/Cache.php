<?php

declare(strict_types=1);

namespace Gadget\Cache;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;
use Psr\Cache\CacheItemPoolInterface as PsrCacheItemPoolInterface;

class Cache extends AbstractCache
{
    /**
     * @param PsrCacheItemPoolInterface $cache
     * @param string[] $namespace
     * @param bool $manageKeys
     */
    public function __construct(
        private PsrCacheItemPoolInterface $cache,
        private array $namespace = [],
        private bool $manageKeys = true
    ) {
    }


    /**
     * @return string[]
     */
    public function getNamespace(): array
    {
        return $this->namespace;
    }


    /**
     * @param string $namespace
     * @return self
     */
    public function withNamespace(string ...$namespace): self
    {
        return new self(
            $this->getCache(),
            [...$this->getNamespace(), ...$namespace],
            $this->shouldManageKeys()
        );
    }


    /**
     * @return bool
     */
    public function shouldManageKeys(): bool
    {
        return $this->manageKeys;
    }


    /**
     * @param bool $manageKeys
     * @return static
     */
    public function setManageKeys(bool $manageKeys): static
    {
        $this->manageKeys = $manageKeys;
        return $this;
    }


    /**
     * @return PsrCacheItemPoolInterface
     */
    public function getCache(): PsrCacheItemPoolInterface
    {
        return $this->cache;
    }


    /**
     * @param string|PsrCacheItemInterface $keyOrItem
     * @return string
     */
    public function getCacheKey(string|PsrCacheItemInterface $keyOrItem): string
    {
        return match (true) {
            $keyOrItem instanceof PsrCacheItemInterface => $keyOrItem->getKey(),
            $this->shouldManageKeys() => hash(
                'SHA256',
                implode('::', [...$this->getNamespace(), $keyOrItem])
            ),
            default => $keyOrItem
        };
    }


    /**
     * @param string|PsrCacheItemInterface $keyOrItem
     * @return CacheItemInterface
     */
    public function getCacheItem(string|PsrCacheItemInterface $keyOrItem): CacheItemInterface
    {
        return new CacheItem($this, $keyOrItem);
    }


    /**
     * @param (string|PsrCacheItemInterface)[] $keysOrItems
     * @return iterable<string,CacheItemInterface>
     */
    public function getCacheItems(array $keysOrItems = []): iterable
    {
        foreach ($keysOrItems as $keyOrItem) {
            $key = ($keyOrItem instanceof PsrCacheItemInterface) ? $keyOrItem->getKey() : $keyOrItem;
            yield $key => $this->getCacheItem($keyOrItem);
        }
    }


    /**
     * @param CacheItemInterface $item
     * @param bool $deferred
     * @return bool
     */
    public function saveCacheItem(
        CacheItemInterface $item,
        bool $deferred = false
    ): bool {
        return $deferred
            ? $this->saveDeferred($item->getCacheItem())
            : $this->save($item->getCacheItem());
    }
}
