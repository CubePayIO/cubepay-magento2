/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Cubepay_PaymentGateway/js/action/set-payment-method-action'
    ],
    function (ko, $, Component, setPaymentMethodAction) {
        'use strict';
        return Component.extend({

            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'Cubepay_PaymentGateway/payment/form',
                transactionResult: '',
                cubepayUrl:''
            },

            afterPlaceOrder: function () {
                setPaymentMethodAction(this.messageContainer);
                return false;
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'transactionResult',
                        'cubepayUrl'
                    ]);
                return this;
            },
            //回傳付款方式名稱
            getCode: function () {
                return 'cubepay_gateway';
            },
            //回傳payment物件
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult(),
                        'cubepay_url': this.cubepayUrl()
                    }
                };
            },

            getTransactionResults: function () {
                return _.map(window.checkoutConfig.payment.cubepay_gateway.transactionResults, function (value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
        });
    }
);