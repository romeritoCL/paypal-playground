<?php

namespace App\Controller\Hyperwallet;

use Hyperwallet\Exception\HyperwalletApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransfersController
 * @package App\Controller\Hyperwallet
 *
 * @Route("/hyperwallet/transfers", name="hyperwallet-transfers-")
 */
class TransfersController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('hyperwallet/transfers/index.html.twig');
    }

    /**
     * @Route("/create", name="create-get", methods={"GET"})
     *
     * @return Response
     */
    public function createTransferGet(): Response
    {
        return $this->render('hyperwallet/transfers/create.html.twig');
    }

    /**
     * @Route("/create", name="create-post", methods={"POST"})
     *
     * @return Response
     */
    public function createTransferPost(): Response
    {
        $request = Request::createFromGlobals();
        $transferDetails = $request->request->all();
        $transfer = $this->hyperwalletService->getTransferService()->create($transferDetails);
        return $this->render('default/dump-input-id.html.twig', [
            'raw_result' => false,
            'result' => $transfer,
            'result_id' => $transfer->getToken(),
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     *
     * @return Response
     * @throws HyperwalletApiException
     */
    public function searchTransfer(): Response
    {
        $transfers = $this->hyperwalletService->getTransferService()->list();
        return $this->render('hyperwallet/transfers/search.html.twig', [
            'transfers' => $transfers,
        ]);
    }

    /**
     * @Route("/{transferToken}", name="get", methods={"GET"})
     *
     * @param string $transferToken
     *
     * @return Response
     */
    public function getTransfer(string $transferToken): Response
    {
        $transfer = $this->hyperwalletService->getTransferService()->get($transferToken);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $transfer,
        ]);
    }

    /**
     * @Route("/{transferToken}/commit", name="commit", methods={"GET"})
     *
     * @param string $transferToken
     *
     * @return Response
     */
    public function commitTransfer(string $transferToken): Response
    {
        $transfer = $this->hyperwalletService->getTransferService()->commit($transferToken);
        return $this->render('default/dump.html.twig', [
            'raw_result' => false,
            'result' => $transfer,
        ]);
    }
}
