<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cubepay\PaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Cubepay\PaymentGateway\Gateway\Http\Client;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'cubepay_gateway';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        Client::SUCCESS => __('Success'),
                        Client::FAILURE => __('Failure')
                    ]
                ]
            ]
        ];
    }
}
