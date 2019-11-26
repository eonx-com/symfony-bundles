<?php
declare(strict_types=1);

namespace EonX\CoreBundle\Messenger;

use Closure;
use EonX\CoreBundle\Services\Lock\Interfaces\LockServiceInterface;

trait SafeHandlerTrait
{
    /** @var \EonX\CoreBundle\Services\Lock\Interfaces\LockServiceInterface */
    private $lockService;

    /**
     * Set lock service.
     *
     * @param \EonX\CoreBundle\Services\Lock\Interfaces\LockServiceInterface $lockService
     *
     * @return void
     *
     * @required
     */
    public function setLockService(LockServiceInterface $lockService): void
    {
        $this->lockService = $lockService;
    }

    /**
     * Handle message with locking to be safe.
     *
     * @param string $resource The resource to lock
     * @param \Closure $func The logic to handle
     * @param null|float $ttl Maximum expected lock duration in seconds
     *
     * @return void|mixed
     */
    protected function handleSafely(string $resource, Closure $func, ?float $ttl = null)
    {
        $lock = $this->lockService->createLock($resource, $ttl);

        if ($lock->acquire() === false) {
            return;
        }

        try {
            return $func();
        } finally {
            $lock->release();
        }
    }
}
