<?php

namespace App\Service\Braintree;

use Braintree\Result\Error;
use Braintree\Result\Successful;
use Exception;

/**
 * Class CustomerService
 * @package App\Service\Braintree
 */
class CustomerService extends AbstractBraintreeService
{
    /**
     * @return array
     */
    public function listCustomers()
    {
        $customers = [];
        $customersList = $this->gateway->customer()->search([

        ]);
        foreach ($customersList as $customer) {
            $customer = [
                'id' => $customer->id,
                'full_name' => $customer->firstName . ' ' . $customer->lastName,
                'email' => $customer->email,
                'available_payment_methods' => count($customer->paymentMethods)
            ];

            if ($customer['full_name'] && $customer['email']) {
                array_unshift($customers, $customer);
            }
        }
        return $customers;
    }

    /**
     * @param array $customerData
     * @return Error|Successful|Exception
     */
    public function createCustomer(array $customerData)
    {
        $requiredFieldKeys = ['firstName', 'lastName', 'email', 'company', 'website', 'phone'];
        foreach ($requiredFieldKeys as $key) {
            if (!array_key_exists($key, $customerData) || empty($customerData[$key])) {
                return new Exception('Unable to create customer, missing key: ' . $key);
            }
        }
        return $this->gateway->customer()->create($customerData);
    }
}
