<?php

namespace App\Controller\Paypal;

use App\Service\PaypalService;
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
     * DefaultController constructor.
     * @param PaypalService $paypalService
     */
    public function __construct(PaypalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }
}
