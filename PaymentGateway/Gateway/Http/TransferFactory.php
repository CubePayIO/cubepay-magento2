<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cubepay\PaymentGateway\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use \Cubepay\PaymentGateway\Helper\Config;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;
    protected $_helper;

    /**
     * TransferFactory constructor.
     * @param TransferBuilder $transferBuilder
     * @param Config $_helper
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        Config $_helper
    )
    {
        $this->transferBuilder = $transferBuilder;
        $this->_helper = $_helper;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        try {
            if (!$request['API_URL']) {
                $request['API_URL'] = 'http://api.cubepay.io';
            }
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $protocol . $_SERVER['HTTP_HOST'];

            $cubepayRequest = [
                'client_id' => $request['MERCHANT_ID'],
                'source_coin_id' => $request['AVAILABLE_CURRENCY'],
                'source_amount' => $request['AMOUNT'],
                'item_name' => @strip_tags($request['GOODS']),
                'merchant_transaction_id' => $request['INVOICE'],
                'return_url' => '',
                'ipn_url' => $domainName . '/paymentgateway/callback/index',
                'MERCHANT_SECRET' => $request['MERCHANT_SECRET']
            ];
            $result = $this->transferBuilder
                ->setBody($cubepayRequest)
                ->setMethod('POST')
                ->setUri($request['API_URL'] . '/payment')
                ->build();
            return $result;
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
