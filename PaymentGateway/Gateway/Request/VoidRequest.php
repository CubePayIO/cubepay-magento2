<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cubepay\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class VoidRequest implements BuilderInterface
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
     * Builds ENV request
     *
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

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        $order = $paymentDO->getOrder();
        $address = $order->getShippingAddress();
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }
        $items = $order->getItems();
        $goodsArray = [];
        foreach ($items as $_item) {
            $goodsArray[] = $_item->getName();
        }
        $goods = implode(",", $goodsArray);
        return [
            'TXN_TYPE' => 'V',
            'TXN_ID' => $payment->getLastTransId(),
            'MERCHANT_SECRET' => $this->config->getValue(
                'merchant_gateway_secret',
                $order->getStoreId()
            ),
            'MERCHANT_ID' => $this->config->getValue(
                'merchant_gateway_id',
                $order->getStoreId()
            ),
            'INVOICE' => $order->getOrderIncrementId(),
            'AMOUNT' => $order->getGrandTotalAmount(),
            'CURRENCY' => $order->getCurrencyCode(),
            'GOODS' => $goods,
            'EMAIL' => $address->getEmail(),
            'API_URL' => $this->config->getValue('api_url')
        ];
    }
}
