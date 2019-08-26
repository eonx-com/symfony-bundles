<?php
declare(strict_types=1);

namespace LoyaltyCorp\CoreBundle\Services\Lock;

use Doctrine\Common\Persistence\ManagerRegistry;
use LoyaltyCorp\CoreBundle\Services\Lock\Interfaces\LockServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\PdoStore;
use Symfony\Component\Lock\StoreInterface;

final class LockService implements LockServiceInterface
{
    /** @var \Symfony\Component\Lock\Factory */
    private $factory;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Doctrine\Common\Persistence\ManagerRegistry */
    private $registry;

    /** @var \Symfony\Component\Lock\StoreInterface */
    private $store;

    /**
     * LockService constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function __construct(LoggerInterface $logger, ManagerRegistry $registry)
    {
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * Create lock for given resource.
     *
     * @param string $resource
     * @param null|float $ttl
     *
     * @return \Symfony\Component\Lock\LockInterface
     */
    public function createLock(string $resource, ?float $ttl = null): LockInterface
    {
        return $this->getFactory()->createLock($resource, $ttl ?? 300.0);
    }

    /**
     * Set lock store.
     *
     * @param \Symfony\Component\Lock\StoreInterface $store
     *
     * @return \LoyaltyCorp\CoreBundle\Services\Lock\Interfaces\LockServiceInterface
     */
    public function setStore(StoreInterface $store): LockServiceInterface
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get lock factory.
     *
     * @return \Symfony\Component\Lock\Factory
     */
    private function getFactory(): Factory
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        $factory = new Factory($this->getStore());
        $factory->setLogger($this->logger);

        return $this->factory = $factory;
    }

    /**
     * Get store lock.
     *
     * @return \Symfony\Component\Lock\StoreInterface
     */
    private function getStore(): StoreInterface
    {
        return $this->store ?? new PdoStore($this->registry->getConnection());
    }
}
