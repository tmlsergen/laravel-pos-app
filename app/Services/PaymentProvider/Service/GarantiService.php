<?php

namespace App\Services\PaymentProvider\Service;

use App\Enums\ProviderCurrency\GarantiCurrencyEnum;
use App\Exceptions\PaymentProvider\PaymentClientException;
use App\Exceptions\PaymentProvider\PaymentServiceException;
use App\Helpers\AppHelper;
use App\Services\PaymentProvider\Client\GarantiClient;
use App\Services\PaymentProvider\Client\PaymentClientInterface;

class GarantiService implements PaymentProviderServiceInterface
{
    private PaymentClientInterface $client;

    public function __construct(GarantiClient $client)
    {
        $this->client = $client;
    }

    private function generateSecurityData($terminalId, $password): string
    {
        $data = [
            $password,
            str_pad((int) $terminalId, 9, 0, STR_PAD_LEFT),
        ];

        $shaData = sha1(implode('', $data));

        return strtoupper($shaData);
    }

    public function generateHashData(array $params): string
    {
        $orderId = $params['order_id'];
        $terminalId = $params['terminal_id'];
        $cardNumber = $params['card_number'];
        $amount = $params['amount'];
        $currencyCode = $params['currency_code'];

        $hashedPassword = $this->generateSecurityData($terminalId, $params['password']);

        $data = [
            $orderId, $terminalId, $cardNumber, $amount, $currencyCode, $hashedPassword,
        ];

        $shaData = strtoupper(hash('sha512', implode('', $data)));

        return strtoupper($shaData);
    }

    public function generateHashDataFor3d(array $params): string
    {
        $orderId = $params['order_id'];
        $terminalId = $params['terminal_id'];
        $amount = $params['amount'];
        $currencyCode = $params['currency_code'];
        $storeKey = $params['store_key'];
        $installmentCount = $params['installment_count'];
        $successUrl = $params['success_url'];
        $errorUrl = $params['error_url'];
        $type = 'sales';

        $hashedPassword = $this->generateSecurityData($terminalId, $params['password']);

        $data = [
            $terminalId, $orderId, $amount, $currencyCode, $successUrl, $errorUrl, $type, $installmentCount, $storeKey, $hashedPassword,
        ];

        return strtoupper(hash('sha512', implode('', $data)));
    }

    /**
     * @throws PaymentServiceException
     */
    public function makePayment(array $data): array
    {
        $orderId = $data['order_id'];
        $cardHolder = $data['card_holder'];
        $cardNumber = $data['card_number'];
        $expirationYear = $data['expiration_year'];
        $expirationMonth = $data['expiration_month'];
        $cvv = $data['cvv'];
        $amount = (int) ($data['amount'] * 100);
        $currencies = GarantiCurrencyEnum::toArray();
        $currency = $currencies[$data['currency']];

        // Generate hash data
        $hashParams = [
            'order_id' => $data['order_id'],
            'terminal_id' => $data['terminal_id'],
            'card_number' => $data['card_number'],
            'amount' => $amount,
            'currency_code' => $currency,
            'password' => $data['provision_password'],
        ];
        $hash = $this->generateHashData($hashParams);

        // Generate request params
        $reqParams = $this->generatePaymentRequestParams([
            'hash' => $hash,
            'order_id' => $orderId,
            'email' => $data['email'],
            'card_holder' => $cardHolder,
            'card_number' => $cardNumber,
            'expiration_year' => $expirationYear,
            'expiration_month' => $expirationMonth,
            'cvv' => $cvv,
            'amount' => $amount,
            'currency' => $currency,
            'provider_user_id' => $data['2d_provider_user_id'],
            'terminal_id' => $data['terminal_id'],
            'merchant_id' => $data['merchant_id'],
        ]);

        try {
            // Make payment
            return $this->client->makePayment($reqParams);
        } catch (PaymentClientException $e) {
            throw new PaymentServiceException($e->getMessage());
        }
    }

    public function generatePaymentRequestParams(array $params): array
    {
        return [
            'Mode' => 'TEST',
            'Version' => '512',
            'Terminal' => [
                'ProvUserID' => $params['provider_user_id'],
                'HashData' => $params['hash'],
                'UserID' => $params['provider_user_id'],
                'ID' => $params['terminal_id'],
                'MerchantID' => $params['merchant_id'],
            ],
            'Customer' => [
                'IPAddress' => AppHelper::getClientIp(),
                'EmailAddress' => $params['email'],
            ],
            'Card' => [
                'Number' => $params['card_number'],
                'ExpireDate' => $params['expiration_month'].$params['expiration_year'],
                'CVV2' => $params['cvv'],
            ],
            'Order' => [
                'OrderID' => $params['order_id'],
                'GroupID' => '',
            ],
            'Transaction' => [
                'Type' => 'sales',
                'Amount' => $params['amount'],
                'CurrencyCode' => $params['currency'],
                'CardholderPresentCode' => '0',
                'MotoInd' => 'N',
            ],
        ];
    }

    /**
     * @throws PaymentServiceException
     */
    public function make3dSecurePayment(array $data): array
    {
        $successUrl = config('payment_provider.providers.garanti.callback_url');
        $errorUrl = config('payment_provider.providers.garanti.callback_url');

        $orderId = $data['order_id'];
        $cardHolder = $data['card_holder'];
        $cardNumber = $data['card_number'];
        $expirationYear = $data['expiration_year'];
        $expirationMonth = $data['expiration_month'];
        $storeKey = $data['store_key'];
        $cvv = $data['cvv'];
        $amount = (int) ($data['amount'] * 100);
        $currencies = GarantiCurrencyEnum::toArray();
        $currency = $currencies[$data['currency']];
        $provisionUserId = $data['3d_provider_user_id'];
        $userId = $data['3d_user_id'];

        $successUrl = $successUrl.'?orderId='.$orderId.'&status=success&provider=garanti';
        $errorUrl = $errorUrl.'?orderId='.$orderId.'&status=error&provider=garanti';

        // Generate hash data
        $hashParams = [
            'order_id' => $data['order_id'],
            'terminal_id' => $data['terminal_id'],
            'card_number' => $cardNumber,
            'amount' => $amount,
            'currency_code' => $currency,
            'password' => $data['provision_password'],
            'store_key' => $storeKey,
            'installment_count' => 0,
            'success_url' => $successUrl,
            'error_url' => $errorUrl,
        ];
        $hash = $this->generateHashDataFor3d($hashParams);

        // Generate request params
        $reqParams = $this->generatePayment3dRequestParams([
            'hash' => $hash,
            'order_id' => $orderId,
            'email' => $data['email'],
            'amount' => $amount,
            'currency' => $currency,
            'provider_user_id' => $provisionUserId,
            'user_id' => $userId,
            'terminal_id' => $data['terminal_id'],
            'merchant_id' => $data['merchant_id'],
            'success_url' => $successUrl,
            'error_url' => $errorUrl,
            'card_number' => $cardNumber,
            'expiration_month' => $expirationMonth,
            'expiration_year' => $expirationYear,
            'cvv' => $cvv,
            'card_holder' => $cardHolder,
        ]);

        try {
            // Make 3D secure payment
            $result = $this->client->make3dSecurePayment($reqParams);
        } catch (PaymentClientException $e) {
            throw new PaymentServiceException($e->getMessage());
        }

        return [
            'response_page' => $result['response_page'], // 3D secure form content
            'form' => $this->parse3dFormContent($result['response_page']) // Parse 3D secure form content
        ];
    }

    private function parse3dFormContent(string $htmlContent): array
    {
        $dom = new \DOMDocument();
        // Handle potential HTML5 elements and special characters
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $form = $dom->getElementsByTagName('form')->item(0);
        $inputs = $form->getElementsByTagName('input');

        $result = [
            'action' => $form->getAttribute('action'),
            'fields' => []
        ];

        foreach ($inputs as $input) {
            $name = $input->getAttribute('name');
            $value = $input->getAttribute('value');
            if ($name) {
                $result['fields'][$name] = $value;
            }
        }

        return $result;
    }

    public function generatePayment3dRequestParams(array $params): array
    {
        return [
            'mode' => 'TEST',
            'apiversion' => '512',
            'secure3dsecuritylevel' => '3D',
            'terminalprovuserid' => $params['provider_user_id'],
            'terminaluserid' => $params['user_id'],
            'terminalmerchantid' => $params['merchant_id'],
            'terminalid' => $params['terminal_id'],
            'orderid' => $params['order_id'],
            'successurl' => $params['success_url'],
            'errorurl' => $params['error_url'],
            'customeremailaddress' => $params['email'],
            'customeripaddress' => AppHelper::getClientIp(),
            'companyname' => 'Test Company',
            'lang' => 'tr',
            'txntimestamp' => gmdate('Y-m-d\TH:i:s.000\Z'),
            'refreshtime' => '1',
            'secure3dhash' => $params['hash'],
            'txnamount' => $params['amount'],
            'txntype' => 'sales',
            'txncurrencycode' => $params['currency'],
            'txninstallmentcount' => 0,
            'cardnumber' => $params['card_number'],
            'cardexpiredatemonth' => $params['expiration_month'],
            'cardexpiredateyear' => $params['expiration_year'],
            'cardcvv2' => $params['cvv'],
        ];
    }

    /**
     * @throws PaymentServiceException
     */
    public function callback(array $params): bool
    {
        $mdStatusMessages = config('payment_provider.md_status_messages');

        $mdStatus = $params['mdstatus'];
        if ($mdStatus !== '1') {
            throw new PaymentServiceException($mdStatusMessages[$mdStatus]);
        }

        $hashParams = $params['hashparams'];

        $hashParams = explode(':', $hashParams);

        $hashInputs = '';
        foreach ($hashParams as $hashParam) {
            $hashInputs .= $params[$hashParam] != null ? $params[$hashParam] : '';
        }

        $hashInputs.= $params['store_key'];

        $hash = strtoupper(hash('sha512', $hashInputs));
        if ($hash !== $params['hash']) {
            throw new PaymentServiceException('Hash data is not valid');
        }

        return true;
    }
}
