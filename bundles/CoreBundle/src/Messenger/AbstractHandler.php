<?php
declare(strict_types=1);

namespace LoyaltyCorp\CoreBundle\Messenger;

use Closure;
use LoyaltyCorp\CoreBundle\Services\Lock\Interfaces\LockServiceInterface;
use LoyaltyCorp\SymfonyBundles\CoreBundle\Messenger\SafeHandlerTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

abstract class AbstractHandler implements MessageHandlerInterface
{
    /** @var \LoyaltyCorp\CoreBundle\Services\Lock\Interfaces\LockServiceInterface */
    private $lockService;

    /**
     * Set lock service.
     *
     * @param \LoyaltyCorp\CoreBundle\Services\Lock\Interfaces\LockServiceInterface $lockService
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
     * Get lock resource.
     *
     * @return string
     */
    abstract protected function getLockResource(): string;

    /**
     * Get lock ttl.
     *
     * @return null|float
     */
    protected function getLockTtl(): ?float
    {
        return null;
    }

    /**
     * Handle message with locking to be safe.
     *
     * @param \Closure $func
     *
     * @return void|mixed
     */
    protected function handleSafely(Closure $func)
    {
        @\trigger_error(\sprintf(
            'Extending "%s" is deprecated since 0.0.4, use "%s" instead.',
            AbstractHandler::class,
            SafeHandlerTrait::class
        ), E_USER_DEPRECATED);

        $lock = $this->lockService->createLock($this->getLockResource(), $this->getLockTtl());

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
