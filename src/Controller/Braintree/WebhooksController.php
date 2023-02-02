<?php

namespace App\Controller\Braintree;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Class WebhooksController
 *
 * @package App\Controller\Braintree
 *
 * @Route("/braintree/webhooks", name="braintree-webhooks-")
 */
class WebhooksController extends AbstractController
{
    /**ff
     * @Route("/", name="index", methods={"POST"})
     *
     * @return Response
     */
    public function index(): Response
    {
        $request = Request::createFromGlobals();
        $parameters = $request->request->all();
        $btSignature = $parameters['bt_signature'] ?? null;
        $btPayload = $parameters['bt_payload'] ?? null;

        return $this->processWebhook($btSignature, $btPayload);
    }

    /**
     * @Route(
     *     "/test/local-payment-method",
     *     name="local-payment-method",
     *     methods={"GET"}, defaults={"action" = "LOCAL_PAYMENT_COMPLETED"})
     * @Route(
     *     "/test/payment-revoked",
     *     name="payment-revoked",
     *     methods={"GET"}, defaults={"action" = "PAYMENT_METHOD_REVOKED_BY_CUSTOMER"})
     *
     * @param string $action
     *
     * @return Response
     */
    public function test(string $action): Response
    {
        $testNotification = $this->webhookService->generateTestNotification($action);
        $btSignature = $testNotification['bt_signature'];
        $btPayload = $testNotification['bt_payload'];

        return $this->processWebhook($btSignature, $btPayload);
    }

    /**
     * @param $btSignature
     * @param $btPayload
     * @return Response
     */
    protected function processWebhook($btSignature, $btPayload): Response
    {
        try {
            $result = $this->webhookService->processWebhook($btSignature, $btPayload);
            return $result ? new Response('OK', 201) : new Response('Error processing the webhook', 422);
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return new Response('Unable to process webhook', 422);
        }
    }
}
