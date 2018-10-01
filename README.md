# CubePay payment gateway extension for Magento 2 
This extension can be only support magento version >= 2.2.

Make it easy for receiving cryptocurrency!

More information at http://cubepay.io.


## API Document

https://document.cubepay.io

## Installation
- download the zip file from release and unzip file to the <Magento ROOT>/app/code/ folder
- open command line mode and locate to the Magento ROOT folder and then type the command : 
```
bin/magento module:enable Cubepay_PaymentGateway
```
This will enable the Cubepay Payment Gateway extension
- Use the commands to install extension
```
php bin/magento setup:upgrade
php  bin/magento cache:flush
```  
## Setup
- open Magento Admin panel and login as Admin
- click Stores -> Configuration -> Sales -> Payment Methods
- setup your Merchant Gateway ID and Merchant Gateway Secret
- adjust your payment gateway Api Url as 'http://api.cubepay.io/payment'