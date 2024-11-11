<?php

namespace App\Services\PaymentProvider\Client;

use App\Exceptions\PaymentProvider\PaymentClientException;
use App\Helpers\CardHelper;
use App\Helpers\XmlHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class GarantiClient implements PaymentClientInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function make3dSecurePayment(array $data): array
    {
        $baseUrl = config('payment_provider.providers.garanti.service_url');
        $url = $baseUrl.'/servlet/gt3dengine';

        try {
            $response = $this->client->post($url, [
                'form_params' => $data,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
            ]);

            $log = $data;
            $log['cardnumber'] = CardHelper::cardMask($log['cardnumber']);
            $log['cardcvv2'] = 'XXX';
            $log['cardexpiredatemonth'] = 'XX/XX';
            $log['cardexpiredateyear'] = 'XX/XX';

            $respRaw = $response->getBody()->getContents();
            Log::info('Garanti Client Send 2D payment request', [
                'request' => $log,
                'response' => $respRaw,
            ]);
        } catch (GuzzleException $e) {
            $data['cardnumber'] = CardHelper::cardMask($data['cardnumber']);
            $data['cardcvv2'] = 'XXX';
            $data['cardexpiredatemonth'] = 'XX/XX';
            $data['cardexpiredateyear'] = 'XX/XX';

            Log::error('Guzzle Error on sending payment request'.$e->getMessage(), [
                'response' => $e->getResponse()->getBody()->getContents(),
                'request' => $data,
            ]);

            throw new PaymentClientException('Error on sending payment request');
        } catch (\Exception $e) {
            $data['cardnumber'] = CardHelper::cardMask($data['cardnumber']);
            $data['cardcvv2'] = 'XXX';
            $data['cardexpiredatemonth'] = 'XX/XX';
            $data['cardexpiredateyear'] = 'XX/XX';

            Log::error('Error on sending payment request'.$e->getMessage(), [
                'request' => $data,
            ]);

            throw new PaymentClientException('Error on sending payment request');
        }

        return [
            'success' => true,
            'response_page' => $respRaw,
        ];
    }

    /**
     * @throws PaymentClientException
     */
    public function makePayment(array $data): array
    {
        $baseUrl = config('payment_provider.providers.garanti.service_url');
        $url = $baseUrl.'/VPServlet';

        $requestParam = XmlHelper::toXML($data, 'GVPSRequest', 'iso-8859-9');
        try {
            $response = $this->client->post($url, [
                'body' => $requestParam,
                'headers' => [
                    'Content-Type' => 'text/xml',
                ],
            ]);

            $log = $data;
            $log['Card']['Number'] = CardHelper::cardMask($data['Card']['Number']);
            $log['Card']['Cvv'] = 'XXX';
            $log['Card']['ExpireDate'] = 'XX/XX';

            $respRaw = $response->getBody()->getContents();
            Log::info('Garanti Client Send 2D payment request', [
                'request' => $log,
                'response' => $respRaw,
            ]);

            $response = XmlHelper::toArray($respRaw);
        } catch (GuzzleException $e) {
            $data['Card']['Number'] = CardHelper::cardMask($data['Card']['Number']);
            $data['Card']['Cvv'] = 'XXX';
            $data['Card']['ExpireDate'] = 'XX/XX';

            Log::error('Guzzle Error on sending payment request'.$e->getMessage(), [
                'response' => $e->getResponse()->getBody()->getContents(),
                'request' => $data,
            ]);

            throw new PaymentClientException('Error on sending payment request');
        } catch (\Exception $e) {
            $data['Card']['Number'] = CardHelper::cardMask($data['Card']['Number']);
            $data['Card']['Cvv'] = 'XXX';
            $data['Card']['ExpireDate'] = 'XX/XX';

            Log::error('Error on sending payment request'.$e->getMessage(), [
                'request' => $data,
            ]);

            throw new PaymentClientException('Error on sending payment request');
        }

        if ($response['Transaction']['Response']['ReasonCode'] != '00') {
            Log::warning('Garanti Client 2D payment request failed', [
                'response' => $response,
            ]);

            throw new PaymentClientException($response['Transaction']['Response']['ErrorMsg'].' - ('.$response['Transaction']['Response']['ReasonCode'].')');
        }

        Log::info('Garanti Client 2D payment request success', [
            'response' => $response,
        ]);

        return $response;
    }
}
