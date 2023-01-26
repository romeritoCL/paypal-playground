<?php

namespace App\Controller\Braintree;

use App\Service\Braintree\WebhookService;
use App\Service\BraintreeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

/**
 * Class AbstractController
 * @package App\Controller\Braintree
 */
class AbstractController extends SymfonyAbstractController
{
    /**
     * @var BraintreeService
     */
    protected BraintreeService $braintreeService;

    /**
     * @var WebhookService
     */
    protected WebhookService $webhookService;

    /**
     * DefaultController constructor.
     * @param BraintreeService $braintreeService
     * @param WebhookService $webhookService
     */
    public function __construct(BraintreeService $braintreeService, WebhookService $webhookService)
    {
        $this->braintreeService = $braintreeService;
        $this->webhookService = $webhookService;
    }
}
