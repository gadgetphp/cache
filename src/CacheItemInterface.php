<?php

declare(strict_types=1);

namespace Gadget\Cache;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;

interface CacheItemInterface extends PsrCacheItemInterface
{
    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface;


    /**
     * @return PsrCacheItemInterface
     */
    public function getCacheItem(): PsrCacheItemInterface;


    /**
     * @template T
     * @param (callable(mixed $value):(T|null)) $toValue
     * @return T|null
     */
    public function getCacheValue(callable $toValue): mixed;


    /**
     * @param mixed $value
     * @param int|\DateTimeInterface|null $expiresOn
     * @return static
     */
    public function setCacheValue(
        mixed $value,
        int|\DateTimeInterface|null $expiresOn = null
    ): static;


    /**
     * @param bool $deferred
     * @return bool
     */
    public function saveCacheItem(bool $deferred = false): bool;
}
