<?php

namespace App\Service;

use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Environment;
use Adyen\Service\Checkout;
use Psr\Log\LoggerInterface;

/**
 * Class AdyenService
 * @package App\Service
 */
class AdyenService
{
    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var $merchantAccount
     */
    private $merchantAccount;

    /**
     * @var $clientKey
     */
    private $clientKey;

    /**
     * @var $paypalId
     */
    private $paypalId;

    /**
     * @var Client
     */
    protected $checkout;

    /**
     * AdyenService constructor.
     *
     * @param string $apiKey
     * @param string $merchantAccount
     * @param string $clientKey
     * @param string $paypalId
     * @param SettingsService $settingsService
     * @throws AdyenException
     */
    public function __construct(
        string $apiKey,
        string $merchantAccount,
        string $clientKey,
        string $paypalId,
        SettingsService $settingsService
    ) {
        $client = new Client();
        $client->setXApiKey($apiKey);
        $client->setEnvironment(Environment::TEST);

        $this->checkout = new Checkout($client);
        $this->merchantAccount = $merchantAccount;
        $this->clientKey = $clientKey;
        $this->settingsService = $settingsService;
        $this->paypalId = $paypalId;
    }

    /**
     * @return string
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * @return string
     */
    public function getPayPalId(): string
    {
        return $this->paypalId;
    }

    /**
     * @return mixed
     * @throws AdyenException
     */
    public function getPaymentMethods()
    {
        $params = [
            "allowedPaymentMethods" => ["paypal","card"],
            "merchantAccount" => $this->merchantAccount,
            "countryCode" => $this->settingsService->getSetting('settings-customer-country'),
            "shopperLocale" => $this->settingsService->getSetting('settings-customer-locale'),
            "amount" => [
                "currency" => $this->settingsService->getSetting('settings-customer-currency'),
                "value" => 100 * $this->settingsService->getSetting('settings-item-price'),
            ],
            "channel" => "Web"
        ];

        return $this->checkout->paymentMethods($params);
    }

    /**
     * @param $data
     * @return mixed
     * @throws AdyenException
     */
    public function makePayment($data)
    {
        $params = [
            "amount" => $data['amount'],
            "reference" => rand(1, 1000000),
            "paymentMethod" => $data['paymentMethod'],
            "merchantAccount" => $this->merchantAccount,
        ];

        return $this->checkout->payments($params);
    }

    /**
     * @param $data
     * @return mixed
     * @throws AdyenException
     */
    public function paymentDetails($data)
    {
        return $this->checkout->paymentsDetails($data);
    }
}
