<?php
class Safepaynetwork_Safepay_Model_Method_Creditcard extends Mage_Payment_Model_Method_Abstract {
    protected $_code = 'safepaynetwork_creditcard';

    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = true;
    protected $_canUseForMultishipping  = false;
    protected $_formBlockType = 'safepay/form_creditcard';
}
