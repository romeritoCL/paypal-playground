<?php

namespace App\EventListener;

use App\Service\AbstractSessionService;
use App\Service\SettingsService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class KernelRequest
 * @package App\EventListener
 */
class KernelRequest
{
    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * SettingsService constructor.
     *
     * @param SettingsService $settingsService
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->settingsService->isInitialized()) {
            $this->settingsService->clearSettings();
        }
    }
}
