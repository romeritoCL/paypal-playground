<?php

namespace App\Service\Paypal;

use Exception;
use PayPal\Api\BillingInfo;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\PaymentTerm;
use PayPal\Api\ShippingInfo;
use PayPal\Api\Tax;

/**
 * Class InvoiceService
 * @package App\Service\Paypal
 */
class InvoiceService extends AbstractPaypalService
{
    /**
     * @param array $invoiceForm
     * @return Invoice|null
     */
    public function createInvoice(array $invoiceForm): ?Invoice
    {
        $invoice = new Invoice();
        $invoice
            ->setMerchantInfo(new MerchantInfo())
            ->setBillingInfo([(new BillingInfo())])
            ->setNote($invoiceForm['note'])
            ->setPaymentTerm(new PaymentTerm())
            ->setShippingInfo(new ShippingInfo());

        $invoice->getMerchantInfo()
            ->setEmail($invoiceForm['merchant_email'])
            ->setbusinessName($invoiceForm['merchant_business_name'])
            ->setAddress(new InvoiceAddress());

        $invoice->getMerchantInfo()->getAddress()
            ->setLine1($invoiceForm['merchant_address'])
            ->setCity($invoiceForm['merchant_city'])
            ->setState($invoiceForm['merchant_state'])
            ->setPostalCode($invoiceForm['merchant_zip_code'])
            ->setCountryCode($invoiceForm['merchant_country_code']);

        $billing = $invoice->getBillingInfo();
        $billing[0]->setLanguage('en_US')
            ->setEmail(null);
        $items = [];
        $items[0] = new InvoiceItem();
        $items[0]
            ->setName($invoiceForm['item_name'])
            ->setDescription($invoiceForm['item_description'])
            ->setQuantity(1)
            ->setUnitPrice(new Currency());
        $items[0]->getUnitPrice()
            ->setCurrency('EUR')
            ->setValue($invoiceForm['item_amount']);

        $tax = new Tax();
        $tax->setPercent($invoiceForm['item_tax_percent'])
            ->setName($invoiceForm['item_tax_name']);
        $items[0]->setTax($tax);
        $invoice->setItems($items);

        $invoice->getPaymentTerm()
            ->setTermType("NET_10");
        $invoice->setTaxInclusive(true);
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
        $image = Invoice::qrCode($invoice->getId(), ['height' => '400', 'width' => '400'], $this->apiContext);
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
