<?php
declare(strict_types=1);

namespace EonX\CoreBundle\Services\Lock\Interfaces;

use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\StoreInterface;

interface LockServiceInterface
{
    /**
     * Create lock for given resource.
     *
     * @param string $resource
     * @param null|float $ttl
     *
     * @return \Symfony\Component\Lock\LockInterface
     */
    public function createLock(string $resource, ?float $ttl = null): LockInterface;

    /**
     * Set lock store.
     *
     * @param \Symfony\Component\Lock\StoreInterface $store
     *
     * @return \EonX\CoreBundle\Services\Lock\Interfaces\LockServiceInterface
     */
    public function setStore(StoreInterface $store): self;
}
