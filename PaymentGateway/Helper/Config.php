<?php

namespace Cubepay\PaymentGateway\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends AbstractHelper
{
    const XML_MERCHANT_SECRET = 'payment/cubepay_gateway/merchant_gateway_secret';
    const XML_MERCHANT_ID = 'payment/cubepay_gateway/merchant_gateway_id';
    const API_URL = 'payment/cubepay_gateway/api_url';

    protected $encryptor;
    protected $_config;

    public function __construct(
        EncryptorInterface $encryptor,
        ScopeConfigInterface $config
    )
    {
        $this->encryptor = $encryptor;
        $this->_config = $config;
    }

    public function getPassword()
    {
        $password = $this->_config->getValue(self::XML_MERCHANT_SECRET, 'default');
        return $password;
    }

    public function getId()
    {
        $password = $this->_config->getValue(self::XML_MERCHANT_ID, 'default');
        return $password;
    }

    public function getApiUrl()
    {
        $url = $this->_config->getValue(self::API_URL, 'default');
        return $url;
    }

}