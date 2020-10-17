<?php

namespace App\Controller;

use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsController
 * @package App\Controller
 *
 * @Route("/settings", name="settings-")
 */
class SettingsController extends AbstractController
{
    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * DefaultController constructor.
     * @param SessionService $sessionService
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function settingsList()
    {
        return $this->render('settings/index.html.twig');
    }

    /**
     * @Route("/", name="save", methods={"POST"})
     *
         * @return Response
     */
    public function settingsSave()
    {
        $request = Request::createFromGlobals();
        $settings = $request->request->all();
        $this->sessionService->storeSettings($settings);
        return new Response();
    }
}
