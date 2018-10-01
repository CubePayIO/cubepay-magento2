<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cubepay\PaymentGateway\Gateway\Http;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

class Client implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $token = "";

    /**
     * @var Logger
     */
    private $logger;
    private $clientFactory;

    /**
     * Client constructor.
     * @param ZendClientFactory $clientFactory
     * @param Logger $logger
     */
    public function __construct(ZendClientFactory $clientFactory, Logger $logger)
    {
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }

    /**
     * 取回API資料並轉址
     * @param TransferInterface $transferObject
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Zend_Http_Client_Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $response = $this->generateResponseForCode(
            $this->getResultCode(
                $transferObject
            )
        );

        $this->logger->debug(
            [
                'request' => $transferObject->getBody(),
                'response' => $response
            ]
        );

        return $response;
    }

    /**
     * Generates response
     * @param $resultCode
     * @return array
     */
    protected function generateResponseForCode($resultCode)
    {
        return array_merge(
            [
                'RESULT_CODE' => $resultCode['status'],
                'TXN_ID' => $this->generateTxnId(),
                'CUBEPAY_URL' => $resultCode['data'],
                'CUBEPAY_TOKEN' => $this->token
            ],
            $this->getFieldsBasedOnResponseType($resultCode)
        );
    }

    /**
     * @return string
     */
    protected function generateTxnId()
    {
        return md5(mt_rand(0, 1000));
    }

    /**
     * @param TransferInterface $transferObject
     * @return string
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Zend_Http_Client_Exception
     */
    private function getResultCode(TransferInterface $transferObject)
    {
        $log = [];
        $this->token = md5(time() . mt_rand(0, 1000));
        $request_body = $transferObject->getBody();
        //驗證 sign
        $merchant_secret = $request_body['MERCHANT_SECRET'];
        unset($request_body['MERCHANT_SECRET']);
        $request_body['other'] = $this->token;
        ksort($request_body);
        $data_string = urldecode(http_build_query($request_body)) . "&client_secret=" . $merchant_secret;
        $sign = strtoupper(hash("sha256", $data_string));
        $request_body['sign'] = $sign;

        $result = json_encode('');
        $client = $this->clientFactory->create();
        $client->setMethod($transferObject->getMethod());
        $client->setParameterPost($request_body);
        $client->setUrlEncodeBody(true);
        $client->setUri($transferObject->getUri());
        try {
            $response = $client->request();
            $result = $response->getBody();
            $log['response'] = $result;
        } catch (\Zend_Http_Client_Exception $e) {
            throw new \Magento\Payment\Gateway\Http\ClientException(__($e->getMessage()));
        } finally {
            $this->logger->debug($log);
        }
        return json_decode($result, true);
    }

    /**
     * Returns response fields for result code
     * @param int $resultCode
     * @return array
     */
    private function getFieldsBasedOnResponseType($resultCode)
    {
        switch ($resultCode['status']) {
            case self::FAILURE:
                return [
                    'FRAUD_MSG_LIST' => [
                        'Stolen card',
                        'Customer location differs'
                    ]
                ];
        }

        return [];
    }
}
