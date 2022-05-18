<?php

namespace App\Controller\Paypal;

use App\Service\PaypalService;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

/**
 * Class AbstractController
 * @package App\Controller\PayPal
 */
class AbstractController extends SymfonyAbstractController
{
    /**
     * @var PaypalService
     */
    protected $paypalService;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * DefaultController constructor.
     * @param PaypalService $paypalService
     * @param SettingsService $settingsService
     */
    public function __construct(PaypalService $paypalService, SettingsService $settingsService)
    {
        $this->paypalService = $paypalService;
        $this->settingsService = $settingsService;
    }
}
