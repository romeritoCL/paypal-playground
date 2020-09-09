<?php

namespace App\Controller\Braintree;

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
    protected $braintreeService;

    /**
     * DefaultController constructor.
     * @param BraintreeService $braintreeService
     */
    public function __construct(BraintreeService $braintreeService)
    {
        $this->braintreeService = $braintreeService;
    }
}
