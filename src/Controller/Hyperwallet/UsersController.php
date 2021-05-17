<?php

namespace App\Controller\Hyperwallet;

use Hyperwallet\Exception\HyperwalletApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UsersController
 * @package App\Controller\Paypal
 *
 * @Route("/hyperwallet/users", name="hyperwallet-users-")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('hyperwallet/users/index.html.twig');
    }

    /**
     * @Route("/create", name="create-get", methods={"GET"})
     *
     * @return Response
     */
    public function createUserGet(): Response
    {
        return $this->render('hyperwallet/users/create.html.twig');
    }

    /**
     * @Route("/create", name="create-post", methods={"POST"})
     *
     * @return Response
     */
    public function createUserPost(): Response
    {
        $request = Request::createFromGlobals();
        $userData = $request->request->all();
        $user = $this->hyperwalletService->getUserService()->create($userData);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $user,
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     *
     * @return Response
     * @throws HyperwalletApiException
     */
    public function searchUser(): Response
    {
        $users = $this->hyperwalletService->getUserService()->list();
        return $this->render('hyperwallet/users/search.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/get/{userToken}", name="read", methods={"GET"})
     *
     * @param string $userToken
     *
     * @return JsonResponse
     */
    public function readUser(string $userToken): JsonResponse
    {
        $user = $this->hyperwalletService->getUserService()->get($userToken);
        return new JsonResponse($user->getProperties());
    }

    /**
     * @Route("/edit/{userToken}", name="edit-get", methods={"GET"})
     *
     * @param string $userToken
     *
     * @return Response
     */
    public function editUserGet(string $userToken): Response
    {
        $userJson = $this->hyperwalletService->getUserService()->get($userToken)->getProperties();
        return $this->render('hyperwallet/users/edit.html.twig', [
            'userJson' => json_encode($userJson)
        ]);
    }

    /**
     * @Route("/edit", name="edit-post", methods={"POST"})
     *
     * @return Response
     */
    public function editUserPost(): Response
    {
        $request = Request::createFromGlobals();
        $userData = $request->request->all();
        $user = $this->hyperwalletService->getUserService()->update($userData);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $user,
        ]);
    }

    /**
     * @Route("{userToken}/transfer-methods", name="transfer-methods-list", methods={"GET"})
     *
     * @param string $userToken
     *
     * @return Response
     */
    public function transferMethodsList(string $userToken): Response
    {
        $transferMethods = $this->hyperwalletService->getUserService()->listTransferMethods($userToken);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $transferMethods,
        ]);
    }

    /**
     * @Route("{userToken}/transfer-methods/add", name="transfer-methods-add", methods={"GET"})
     *
     * @param string $userToken
     *
     * @return Response
     */
    public function transferMethodAdd(string $userToken): Response
    {
        $userJson = $this->hyperwalletService->getUserService()->get($userToken)->getProperties();
        return $this->render('hyperwallet/users/transferMethods/create.html.twig', [
            'userJson' => json_encode($userJson)
        ]);
    }

    /**
     * @Route("{userToken}/verify", name="verify", methods={"GET"})
     *
     * @param string $userToken
     *
     * @return Response
     */
    public function verify(string $userToken): Response
    {
        $userJson = $this->hyperwalletService->getUserService()->get($userToken)->getProperties();
        return $this->render('hyperwallet/users/verify.html.twig', [
            'userJson' => json_encode($userJson)
        ]);
    }
}
