<?php

namespace App\Service\Braintree;

use Exception;

/**
 * Class ForwardApiService
 * @package App\Service\Braintree
 */
class ForwardApiService extends AbstractBraintreeService
{
    /**
     * @var string
     */
    public string $ENDPOINT = "https://forwarding.sandbox.braintreegateway.com";

    /**
     * @param string $paymentNonce
     * @param string $deviceData
     * @return array|null
     */
    public function directTokenization(string $paymentNonce, string $deviceData): ?array
    {
        try {
            $body = [
                'debug_transformations' => true,
                'name' => 'default',
                'merchant_id' => $this->gateway->config->getMerchantId(),
                'payment_method_nonce' => $paymentNonce,
                'device_data' => $deviceData,
                'method' => 'POST',
                'data' => [
                    'card-id' => 'custom_card_id_1',
                    'open-id-token' => sha1(time()),
                ],
                'url' => 'https://test.com',
                'config' => [
                    'name' => 'default',
                    'url' => '^https://test.com',
                    'methods' => ['POST'],
                    'types' => ['NetworkTokenizedCard'],
                    'request_format' => [
                        'body' => 'json'
                    ],
                    'transformations' => [
                        [
                            'path' => '/body/card/pan',
                            'value' => '$number'
                        ],
                        [
                            'path' => '/body/card/card-cvv',
                            'value' => '$cvv'
                        ],
                        [
                            'path' => '/body/card/expiration-month',
                            'value' => '$expiration_month'
                        ],
                        [
                            'path' => '/body/card/expiration-year',
                            'value' => '$expiration_year'
                        ],
                        [
                            'path' => '/body/custom-fields/card-id',
                            'value' => '$card-id'
                        ],
                        [
                            'path' => '/header/Authorization',
                            'value' => '$open-id-token'
                        ]
                    ]

                ]
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->ENDPOINT);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt(
                $ch,
                CURLOPT_USERPWD,
                $this->gateway->config->getPublicKey() . ':' . $this->gateway->config->getPrivateKey()
            );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, true));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) {
                    return $len;
                }
                $headers[(trim($header[0]))] = trim($header[1]);
                return $len;
            });
            $result = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
        } catch (Exception $e) {
            $this->logger->error('Error on ForwardAPI::'.$this->ENDPOINT.' = ' . $e->getMessage());
            return null;
        }
        return ([
            'request' => $body,
            'response' => [
                'headers' => (object) $headers,
                'body' => (object) json_decode($result),
                'statusCode' => $statusCode
            ]
        ]);
    }
}
