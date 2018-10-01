<?php

namespace Cubepay\PaymentGateway\Controller\Callback;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_helper;
    protected $_objectManager;
    protected $order;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Cubepay\PaymentGateway\Helper\Config $_helper,
        \Magento\Sales\Api\Data\OrderInterface $order
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_helper = $_helper;
        $this->order = $order;
        return parent::__construct($context);
    }

    public function execute()
    {
        $client_id = $this->getRequest()->getParam('client_id');
        $merchant_transaction_id = $this->getRequest()->getParam('merchant_transaction_id');
        $token = $this->getRequest()->getParam('other');
        $source_amount = (int)$this->getRequest()->getParam('source_amount');
        try {
            $order = $this->order->loadByIncrementId($merchant_transaction_id);
            $payment = $order->getPayment();
            $additionalInfo = $payment->getAdditionalInformation();
            if ($additionalInfo['CUBEPAY_TOKEN'] && $additionalInfo['CUBEPAY_TOKEN'] == trim($token)) {
                // $payment->setAmountPaid();
                $payment->setAdditionalInformation(
                    'transaction_result',
                    1
                );
                $payment->save();
                $order->setTotalPaid($source_amount);
                $order->save();
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
        echo "success";
        exit;
    }
}
