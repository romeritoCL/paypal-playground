<?php

namespace App\Controller\Hyperwallet;

use App\Service\HyperwalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

/**
 * Class AbstractController
 * @package App\Controller\Hyperwallet
 */
class AbstractController extends SymfonyAbstractController
{
    /**
     * @var HyperwalletService
     */
    protected $hyperwalletService;

    /**
     * DefaultController constructor.
     * @param HyperwalletService $hyperwalletService
     */
    public function __construct(HyperwalletService $hyperwalletService)
    {
        $this->hyperwalletService = $hyperwalletService;
    }
}
