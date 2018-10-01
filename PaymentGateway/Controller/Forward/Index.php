<?php

namespace Cubepay\PaymentGateway\Controller\Forward;

use Magento\Framework\View\Asset\File\NotFoundException;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_objectManager;
    protected $_coreSession;
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_coreSession = $coreSession;
        $this->_messageManager = $messageManager;
        return parent::__construct($context);
    }

    public function execute()
    {
        $url = $this->_coreSession->getData('CUBEPAY_URL', true);
        if (!empty($url)) {
            header('Location: ' . $url);
        } else {
            $this->_messageManager->addError(__('cannot found payment url'));//
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
        exit;
    }
}