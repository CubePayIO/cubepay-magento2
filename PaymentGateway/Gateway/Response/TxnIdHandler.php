<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cubepay\PaymentGateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

class TxnIdHandler implements HandlerInterface
{
    const TXN_ID = 'TXN_ID';
    const CUBEPAY_URL = 'CUBEPAY_URL';
    const CUBEPAY_TOKEN = 'CUBEPAY_TOKEN';
    protected $_coreSession;

    public function __construct(\Magento\Framework\Session\SessionManagerInterface $coreSession)
    {
        $this->_coreSession = $coreSession;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $payment = $paymentDO->getPayment();
        $payment->setTransactionId($response[self::TXN_ID]);
        $payment->setAdditionalInformation('CUBEPAY_TOKEN', $response[self::CUBEPAY_TOKEN]);
        $payment->setIsTransactionClosed(false);
        $this->_coreSession->setData('CUBEPAY_URL', $response[self::CUBEPAY_URL]);
    }
}
