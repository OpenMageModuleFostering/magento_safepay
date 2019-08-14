<?php 

class Safepaynetwork_Safepay_Model_Adminhtml_System_Source_Timeout
{
    public function toOptionArray()
    {        
        return
         array(
            array(
                'value' => 'allow',
                'label' => 'Allow Timeouts To Proceed',
            ),
            array(
                'value' => 'stop',
                'label' => 'Force Safepay Validation',
            ),
            array(
                'value' => 'threshold',
                'label' => 'Proceed Below Threshold Value',
            ),
        );
    }
}
