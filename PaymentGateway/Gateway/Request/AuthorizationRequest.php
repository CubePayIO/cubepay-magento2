<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cubepay\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    )
    {
        $this->config = $config;
    }

    /**
     * 組裝資料提供給TransferFactory
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $payment */
        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();
        $address = $order->getShippingAddress();
        $items = $order->getItems();
        $goods = "";
        foreach ($items as $_item) {
            $goods .= $_item->getName() . ",";
        }
        return [
            'TXN_TYPE' => 'A',
            'INVOICE' => $order->getOrderIncrementId(),
            'AMOUNT' => $order->getGrandTotalAmount(),
            'CURRENCY' => $order->getCurrencyCode(),
            'GOODS' => $goods,
            'EMAIL' => $address->getEmail(),
            'MERCHANT_SECRET' => $this->config->getValue(
                'merchant_gateway_secret',
                $order->getStoreId()
            ),
            'MERCHANT_ID' => $this->config->getValue(
                'merchant_gateway_id',
                $order->getStoreId()
            ),
            'API_URL' => $this->config->getValue('api_url')
        ];
    }
}
