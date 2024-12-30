<?php

declare(strict_types=1);

namespace Gadget\Cache;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;
use Psr\Cache\CacheItemPoolInterface as PsrCacheItemPoolInterface;

interface CacheInterface extends PsrCacheItemPoolInterface
{
    /**
     * @return string[]
     */
    public function getNamespace(): array;


    /**
     * @param string $namespace
     * @return self
     */
    public function withNamespace(string ...$namespace): self;


    /**
     * @return bool
     */
    public function shouldManageKeys(): bool;


    /**
     * @param bool $manageKeys
     * @return static
     */
    public function setManageKeys(bool $manageKeys): static;


    /**
     * @return PsrCacheItemPoolInterface
     */
    public function getCache(): PsrCacheItemPoolInterface;


    /**
     * @param string|PsrCacheItemInterface $keyOrItem
     * @return string
     */
    public function getCacheKey(string|PsrCacheItemInterface $keyOrItem): string;


    /**
     * @param string|PsrCacheItemInterface $keyOrItem
     * @return CacheItemInterface
     */
    public function getCacheItem(string|PsrCacheItemInterface $keyOrItem): CacheItemInterface;


    /**
     * @param (string|PsrCacheItemInterface)[] $keysOrItems
     * @return iterable<string,CacheItemInterface>
     */
    public function getCacheItems(array $keysOrItems = []): iterable;


    /**
     * @param CacheItemInterface $item
     * @param bool $deferred
     * @return bool
     */
    public function saveCacheItem(
        CacheItemInterface $item,
        bool $deferred = false
    ): bool;
}
