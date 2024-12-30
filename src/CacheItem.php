<?php

declare(strict_types=1);

namespace Gadget\Cache;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;

class CacheItem extends AbstractCacheItem implements CacheItemInterface
{
    private PsrCacheItemInterface $cacheItem;


    /**
     * @param CacheInterface $cache
     * @param PsrCacheItemInterface $cacheItem
     */
    public function __construct(
        private CacheInterface $cache,
        string|PsrCacheItemInterface $cacheItem
    ) {
        $this->cacheItem = $cacheItem instanceof PsrCacheItemInterface
            ? $cacheItem
            : $cache->getCacheItem($cacheItem);
    }


    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }


    /**
     * @return PsrCacheItemInterface
     */
    public function getCacheItem(): PsrCacheItemInterface
    {
        return $this->cacheItem;
    }


    /**
     * @template T
     * @param (callable(mixed $value):(T|null)) $toValue
     * @return T|null
     */
    public function getCacheValue(callable $toValue): mixed
    {
        return $toValue($this->get());
    }


    /**
     * @param mixed $value
     * @param int|\DateTimeInterface|null $expiresOn
     * @return static
     */
    public function setCacheValue(
        mixed $value,
        int|\DateTimeInterface|null $expiresOn = null
    ): static {
        return $this
            ->set($value)
            ->expiresAt(
                is_int($expiresOn)
                    ? (new \DateTime())->setTimestamp($expiresOn)
                    : $expiresOn
            );
    }


    /**
     * @param bool $deferred
     * @return bool
     */
    public function saveCacheItem(bool $deferred = false): bool
    {
        return $this->getCache()->saveCacheItem($this, $deferred);
    }
}
