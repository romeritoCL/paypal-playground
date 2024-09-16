<?php

namespace App\Service\Paypal;

use App\Service\SettingsService;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPalHttp\HttpRequest;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Class AbstractPaypalService
 * @package App\Service\Paypal
 */
abstract class AbstractPaypalService
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var ApiContext
     */
    protected $apiContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * PaypalService constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param LoggerInterface $logger
     * @param SessionService $sessionService
     * @param SettingsService $settingsService
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        LoggerInterface $logger,
        SessionService $sessionService,
        SettingsService $settingsService
    ) {
        $sessionClientId = $sessionService->session->get('PAYPAL_SDK_CLIENT_ID');
        $sessionClientSecret = $sessionService->session->get('PAYPAL_SDK_CLIENT_SECRET');
        $sessionSDKExtra = $sessionService->session->get('PAYPAL_SDK_EXTRA');
        $this->clientId = $sessionClientId ?? $clientId;
        $this->clientSecret = $sessionClientSecret ?? $clientSecret;
        $this->logger = $logger;
        $apiContext = new ApiContext(new OAuthTokenCredential($this->clientId, $this->clientSecret));
        $apiContext->setConfig(['mode' => 'sandbox']);
        $this->apiContext = $apiContext;
        $this->settingsService = $settingsService;
    }

    /**
     * @param $requestBody
     * @param string $accessToken
     * @param string $url
     * @param array $inputHeaders
     * @param string $verb
     * @return array|string|null
     */
    public function paypalApiCall(
        string $accessToken,
        $requestBody,
        string $url,
        array $inputHeaders = [],
        string $verb = 'POST'
    ) {
        try {
            $headers = array_merge($inputHeaders, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
            if ($requestBody !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::'.$url.' = ' . $e->getMessage());
            return null;
        }
        return ([
            'result' => $result,
            'statusCode' => $statusCode
        ]);
    }

    /**
     * Add Negative testing Settings header to the given request
     * @param HttpRequest $request
     * @return void
     */
    public function addNegativeTestingSetting(HttpRequest $request)
    {
        $negativeTesting = $this->settingsService->getSetting('settings-merchant-negative-testing');
        if ($negativeTesting !== null) {
            $request->headers['PayPal-Mock-Response'] = json_encode(
                ['mock_application_codes' => $negativeTesting]
            );
        }
    }
}
