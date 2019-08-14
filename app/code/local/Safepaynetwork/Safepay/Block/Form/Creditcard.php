<?php
class Safepaynetwork_Safepay_Block_Form_Creditcard extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('safepay/form/creditcard.phtml');
    }
    protected function getCreditCardForm()
    {
          $safepay_args = array(
                'cardTypes'        => array(
                        'VISA'          => 'VISA',
                        'AMEX'          => 'AMEX',
                        'MasterCard'    => 'MasterCard',
                        'Discover'      => 'Discover',
                        'SM'            => 'Switch/Maestro', // Switch/Maestro
                        'SO'            => 'SOLO', // SOLO
                        'DINERS'    =>'DINERS CLUB'
                    ),
                'dates'             => array(
                        '1' => '01 - January',
                        '2' => '02 - February',
                        '3' => '03 - March',
                        '4' => '04 - April',
                        '5' => '05 - May',
                        '6' => '06 - June',
                        '7' => '07 - July',
                        '8' => '08 - August',
                        '9' => '09 - September',
                        '10' => '10 - October',
                        '11' => '11 - November',
                        '12' => '12 - December'
                    ),
                'years'             => array(
                        '2014' =>'2014',
                        '2015' =>'2015',
                        '2016' =>'2016',
                        '2017' =>'2017',
                        '2018' =>'2018',
                        '2019' =>'2019',
                        '2020' =>'2020',
                        '2021' =>'2021',
                        '2022' =>'2022',
                        '2023' =>'2023'            
                    )
                );
           $_code = 'safepaynetwork_creditcard';
           ob_start();
           require_once(dirname(__FILE__).'/payment_form.php');
           $form = ob_get_contents();
           ob_end_clean();
           return $form;
    }
}
