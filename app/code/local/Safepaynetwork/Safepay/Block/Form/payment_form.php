<div  id="payment_form_<?php echo $_code ?>" style="display:none;">
    <div id="safepay-wrapper">
        <div id="sp-form">
            <div class="left-safepay">
                <a target="_blank" href="https://www.usesafepay.com"><img style="margin: 0px 10px 5px 0px;" alt="SafePay payment" src="<?php echo $this->getSkinUrl('images/safepay/'); ?>safepay.jpg"/></a>
            </div>
            <div class="right-safepay">
                <table>
                    <tbody><tr>
                        <td><label for="nameoncard" class="label">Name On Card <em>*</em> </label>
                        <br />
                        <input type="text" value="" name="payment[nameoncard]" id="nameoncard" class="text ajax-field input-text required-entry" />
                    </td>
                    </tr><tr>
                        <td>
                        <label for="creditcardtype" class="label">Credit Card Type  <em>*</em></label><br />
                        <select name="payment[creditcardtype]" id="creditcardtype" class="select ajax-field">
                            <?php foreach($safepay_args['cardTypes'] as $k => $v) : ?>
                                <option value="<?php echo $k ?>"> <?php echo $v ?></option>
                            <?php endforeach; ?>
                        </select>
                        </td>
                    </tr>         
                    <tr>
                        <td><label for="creditcardnumber" class="label">Credit Card Number <em>*</em></label><br/>
                        <input type="text" value="" name="payment[creditcardnumber]" id="creditcardnumber" class="text ajax-field required-entry input-text validate-cc-number validate-cc-type"/></td>
                    </tr>
                    <tr>
                        <td>                        
                            <label style="width: 100%; display: inline-block; text-align: left;" for="expirationdate" class="label">Expiration date  <em>*</em></label><br/>
                            <div class="safepay_select_wrapper" style="width: 175px;">
                                <select style="width: 165px;" name="payment[expirationdate]" id="expirationdate" class="select50 ajax-field required-entry">
                                    <option value="">Month</option>
                                    <?php foreach($safepay_args['dates'] as $k => $v) : ?>
                                        <option value="<?php echo $k ?>"> <?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="safepay_select_wrapper" style="150px;">
                                <select  style="width: 80px;"  name="payment[exy]" id="exy" class="select50 ajax-field required-entry">
                                        <option value="">Year</option>
                                        <?php foreach($safepay_args['years'] as $k => $v) : ?>
                                            <option value="<?php echo $k ?>"> <?php echo $v ?></option>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="verification" class="label">Card Verification Number<em>*</em></label><br/>
                            <input type="text" value="" name="payment[verification]" id="verification" class="text ajax-field input-text cvv required-entry validate-cc-cvn" style="width: 40px;"/> <span id="wit" class="cvv-what-is-this" style="margin-left: 5px; color: #1E7EC8;text-decoration: underline;">What is this?</span>
                        </td>
                    </tr>
                </tbody></table>
            </div>
            <div id="sp-guide">
                <div class="sp-guide-inner">
                    <table>
                        <tbody><tr>
                            <td>
                                <span class="sp-container">
                                    <span class="sp-close"><img alt="Close" src="<?php echo $this->getSkinUrl('images/safepay/'); ?>btn_window_close.gif"></span>
                                    <img alt="Safe Pay" src="<?php echo $this->getSkinUrl('images/safepay/'); ?>guide.gif"/>
                                </span>
                            </td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
        </div>
          
    </div>   
    
    <div id="sp-temp-load">
        <div id="sp-loading">
            <div id="sp-loading-inner">
                <img src="<?php echo $this->getSkinUrl('images/safepay/'); ?>loading-message.png" class="loading-message" />
                <img src="<?php echo $this->getSkinUrl('images/safepay/'); ?>preloader.gif" class="reloader" />
            </div>
        </div>
    </div>
</div>