<?php

namespace App\Service\Paypal;

use Exception;
use PayPal\Api\BillingInfo;
use PayPal\Api\Cost;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;
use PayPal\Api\Phone;
use PayPal\Api\ShippingInfo;
use PayPal\Api\Tax;

/**
 * Class InvoiceService
 * @package App\Service\Paypal
 */
class InvoiceService extends AbstractPaypalService
{
    /**
     * @return Invoice|null
     */
    public function createInvoice(): ?Invoice
    {
        $invoice = new Invoice();
        $invoice
            ->setMerchantInfo(new MerchantInfo())
            ->setBillingInfo([(new BillingInfo())])
            ->setNote("Medical Invoice 16 Jul, 2013 PST")
            ->setPaymentTerm(new PaymentTerm())
            ->setShippingInfo(new ShippingInfo());

        $invoice->getMerchantInfo()
            ->setEmail("jaypatel512-facilitator@hotmail.com")
            ->setFirstName("Dennis")
            ->setLastName("Doctor")
            ->setbusinessName("Medical Professionals, LLC")
            ->setPhone(new Phone())
            ->setAddress(new InvoiceAddress());

        $invoice->getMerchantInfo()->getPhone()
            ->setCountryCode("001")
            ->setNationalNumber("5032141716");

        $invoice->getMerchantInfo()->getAddress()
            ->setLine1("1234 Main St.")
            ->setCity("Portland")
            ->setState("OR")
            ->setPostalCode("97217")
            ->setCountryCode("US");

        $billing = $invoice->getBillingInfo();
        $billing[0]
            ->setEmail("example@example.com");

        $billing[0]->setBusinessName("Jay Inc")
            ->setAdditionalInfo("This is the billing Info")
            ->setAddress(new InvoiceAddress());

        $billing[0]->getAddress()
            ->setLine1("1234 Main St.")
            ->setCity("Portland")
            ->setState("OR")
            ->setPostalCode("97217")
            ->setCountryCode("US");

        $items = [];
        $items[0] = new InvoiceItem();
        $items[0]
            ->setName("Sutures")
            ->setQuantity(100)
            ->setUnitPrice(new Currency());

        $items[0]->getUnitPrice()
            ->setCurrency("USD")
            ->setValue(5);

        $tax = new Tax();
        $tax->setPercent(1)->setName("Local Tax on Sutures");
        $items[0]->setTax($tax);

        $items[1] = new InvoiceItem();
        $item1discount = new Cost();
        $item1discount->setPercent("3");
        $items[1]
            ->setName("Injection")
            ->setQuantity(5)
            ->setDiscount($item1discount)
            ->setUnitPrice(new Currency());

        $items[1]->getUnitPrice()
            ->setCurrency("USD")
            ->setValue(5);

        $tax2 = new Tax();
        $tax2->setPercent(3)->setName("Local Tax on Injection");
        $items[1]->setTax($tax2);

        $invoice->setItems($items);

        $cost = new Cost();
        $cost->setPercent("2");
        $invoice->setDiscount($cost);

        $invoice->getPaymentTerm()
            ->setTermType("NET_45");

        $invoice->getShippingInfo()
            ->setFirstName("Sally")
            ->setLastName("Patient")
            ->setBusinessName("Not applicable")
            ->setPhone(new Phone())
            ->setAddress(new InvoiceAddress());

        $invoice->getShippingInfo()->getPhone()
            ->setCountryCode("001")
            ->setNationalNumber("5039871234");

        $invoice->getShippingInfo()->getAddress()
            ->setLine1("1234 Main St.")
            ->setCity("Portland")
            ->setState("OR")
            ->setPostalCode("97217")
            ->setCountryCode("US");

        $invoice->setLogoUrl('https://www.paypalobjects.com/webstatic/i/logo/rebrand/ppcom.svg');

        try {
            $invoice->create($this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::createInvoice = ' . $e->getMessage());
            return null;
        }

        return $invoice;
    }

    /**
     * @param string $invoiceId
     * @return Invoice|null
     */
    public function getInvoice(string $invoiceId): ?Invoice
    {
        try {
            $invoice = Invoice::get($invoiceId, $this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::getInvoice = ' . $e->getMessage());
            return null;
        }

        return $invoice;
    }

    /**
     * @param Invoice $invoice
     * @return string
     */
    public function getInvoiceQRHTML(Invoice $invoice): string
    {
        $image = Invoice::qrCode($invoice->getId(), ['height' => '300', 'width' => '300'], $this->apiContext);
        return '<img src="data:image/png;base64,'. $image->getImage() . '" alt="Invoice QR Code" />';
    }

    /**
     * @param Invoice $invoice
     * @return Invoice|null
     */
    public function sendInvoice(Invoice $invoice): ?Invoice
    {
        try {
            $invoice->send($this->apiContext);
        } catch (Exception $e) {
            $this->logger->error('Error on PayPal::sendInvoice = ' . $e->getMessage());
            return null;
        }

        return $invoice;
    }
}
