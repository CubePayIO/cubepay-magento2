<?php

namespace Cubepay\PaymentGateway\Model;

use Magento\Framework\HTTP\ZendClientFactory;

class AvailableCurrency implements \Magento\Framework\Option\ArrayInterface
{
    protected $_helper;
    private $clientFactory;

    public function __construct(
        ZendClientFactory $clientFactory,
        \Cubepay\PaymentGateway\Helper\Config $_helper
    )
    {
        $this->clientFactory = $clientFactory;
        $this->_helper = $_helper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAvailableCoin();
    }

    private function getAvailableCoin()
    {
        $request_body = [
            'client_id' => $this->_helper->getId()
        ];
        //驗證 sign
        $merchant_secret = $this->_helper->getPassword();
        ksort($request_body);
        $data_string = urldecode(http_build_query($request_body)) . "&client_secret=" . $merchant_secret;
        $sign = strtoupper(hash("sha256", $data_string));
        $request_body['sign'] = $sign;

        $currency = [];
        $client = $this->clientFactory->create();
        $client->setMethod('POST');
        $client->setParameterPost($request_body);
        $client->setUrlEncodeBody(true);
        $client->setUri($this->_helper->getApiUrl() . '/currency/fiat');
        try {
            $response = $client->request();
            $result = $response->getBody();
            $resultArray = json_decode($result, true);
            usort($resultArray['data'], array($this, 'usortTest'));
            foreach ($resultArray['data'] as $data) {
                $currency[] = array(
                    'value' => $data['id'],
                    'label' => $data['name']
                );
            }
        } catch (\Zend_Http_Client_Exception $e) {
            error_log("get currency error : " . $e->getMessage());
        }
        return $currency;
    }

    private static function usortTest($a, $b)
    {
        return strnatcmp($a['name'], $b['name']);
    }

}