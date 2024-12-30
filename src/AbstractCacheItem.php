<?php

declare(strict_types=1);

namespace Gadget\Cache;

abstract class AbstractCacheItem implements CacheItemInterface
{
    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getCacheItem()->getKey();
    }


    /**
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->getCacheItem()->get();
    }


    /**
     * @return bool
     */
    public function isHit(): bool
    {
        return $this->getCacheItem()->isHit();
    }


    /**
     * @param mixed $value
     * @return static
     */
    public function set(mixed $value): static
    {
        $this->getCacheItem()->set($value);
        return $this;
    }


    /**
     * @param \DateTimeInterface|null $expiration
     * @return static
     */
    public function expiresAt(\DateTimeInterface|null $expiration): static
    {
        $this->getCacheItem()->expiresAt($expiration);
        return $this;
    }


    /**
     * @param int|\DateInterval|null $time
     * @return static
     */
    public function expiresAfter(int|\DateInterval|null $time): static
    {
        $this->getCacheItem()->expiresAfter($time);
        return $this;
    }
}
