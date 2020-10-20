<?php

namespace App\Controller;

use App\Service\SettingsService;
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
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * DefaultController constructor.
     * @param SettingsService $settingsService
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
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
        $this->settingsService->storeSettings($settings);
        return new Response();
    }

    /**
     * @Route("/", name="clear", methods={"DELETE"})
     *
     * @return Response
     */
    public function settingsClear()
    {
        $this->settingsService->clearSettings();
        return new Response();
    }
}
