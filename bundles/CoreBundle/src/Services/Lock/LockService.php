<?php
declare(strict_types=1);

namespace EonX\CoreBundle\Services\Lock;

use Doctrine\Common\Persistence\ManagerRegistry;
use EonX\CoreBundle\Services\Lock\Interfaces\LockServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\PdoStore;

final class LockService implements LockServiceInterface
{
    /** @var \Symfony\Component\Lock\LockFactory */
    private $factory;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Doctrine\Common\Persistence\ManagerRegistry */
    private $registry;

    /** @var \Symfony\Component\Lock\PersistingStoreInterface */
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
     * @param \Symfony\Component\Lock\PersistingStoreInterface $store
     *
     * @return \EonX\CoreBundle\Services\Lock\Interfaces\LockServiceInterface
     */
    public function setStore(PersistingStoreInterface $store): LockServiceInterface
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get lock factory.
     *
     * @return \Symfony\Component\Lock\LockFactory
     */
    private function getFactory(): LockFactory
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        $factory = new LockFactory($this->getStore());
        $factory->setLogger($this->logger);

        return $this->factory = $factory;
    }

    /**
     * Get store lock.
     *
     * @return \Symfony\Component\Lock\PersistingStoreInterface
     */
    private function getStore(): PersistingStoreInterface
    {
        return $this->store ?? new PdoStore($this->registry->getConnection());
    }
}
