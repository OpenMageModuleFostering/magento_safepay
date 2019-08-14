<?php
/**
 * Button.php
 * 
 */
class Safepaynetwork_Safepay_Block_Onepage_Button extends Mage_Checkout_Block_Onepage_Review_Info
{
    protected $safePayStatus = 'ERROR';
    protected function _construct()
    {
        if($this->getQuote()->getPayment()->getMethod()=='safepaynetwork_creditcard')
        {
            $creditCardNumber = $this->getQuote()->getPayment()->getCreditcardnumber();
            $creditCardFirstSix = substr($creditCardNumber, 0, 6);                
            $creditCardLastSix = substr($creditCardNumber, strlen($creditCardNumber)-6, 6);   
            $creditCardType = $this->getQuote()->getPayment()->getCreditcardtype();
            
            $safePayRequest = array(
                'amount'                    => trim($this->getQuote()->getGrandTotal()),
                'billingcity'               => trim($this->getQuote()->getBillingAddress()->getCity()),
                'billingcountry'            => trim($this->getQuote()->getBillingAddress()->getCountry()),
                'billingaddress1'           => trim($this->getQuote()->getBillingAddress()->getStreetFull()),
                'billingaddress2'           => '',
                'billingpostal'             => trim($this->getQuote()->getBillingAddress()->getPostcode()),
                'billingfirstname'          => trim($this->getQuote()->getBillingAddress()->getFirstname()),
                'billinglastname'           => trim($this->getQuote()->getBillingAddress()->getLastname()),
                'creditcardfirstsix'        => trim($creditCardFirstSix),
                'creditcardlastsix'         => trim($creditCardLastSix),
                'creditcardtype'            => trim($creditCardType),
                'customeremail'             => trim($this->getQuote()->getCustomerEmail()),
                'merchanttransactionid'     => time() . trim($this->getQuote()->getId())
            );
                        
            $auth = array(
                'login' => trim(Mage::getStoreConfig('payment/safepaynetwork_creditcard/username')),
                'password' => Mage::getStoreConfig('payment/safepaynetwork_creditcard/password')
            );
                        
            $proxy = new SafePayProxy();
            $proxy->init($auth);        
            $safepayResponse = $proxy->authorize_trans($safePayRequest);
            
            //$safepayResponse = 'TIMEDOUT';
            
            $timeoutAction = Mage::getStoreConfig('payment/safepaynetwork_creditcard/timeout_action');
            $threadhold = Mage::getStoreConfig('payment/safepaynetwork_creditcard/threshold');  
            
            if( $safepayResponse == 'TIMEDOUT' && ($timeoutAction=='allow' || $timeoutAction=='threshold' && (float)$this->getQuote()->getGrandTotal() <= (float)$threadhold))
            {
                return;
            }
            
            $this->safePayStatus = $safepayResponse;
            
        }
        else
            return;
    }    
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }
}
require_once(dirname(__FILE__).'/safepayproxy.php');