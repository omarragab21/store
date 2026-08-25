<!-- MyFatoorah version 2.0.0.2-->
<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle">
            &nbsp;<?php echo $error; ?>
        </i> 
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
<?php else: ?>
    <form action="<?php echo $action; ?>" method="post" id="mfFormCheckout">
        <?php if ($displayTypes == 'multigateways'): ?>
            <?php if ((count($paymentMethods['cards']) == 1 && count($paymentMethods['form']) == 0)): ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        //card button clicked
                        $("#mfButtonCheckout").on('click', function () {
                            $('#mfButtonCheckout').button('loading');
                            $('#mfFormCheckout').append('<input type="hidden" name="mfCardData" value="<?php echo $paymentMethods['cards'][0]->PaymentMethodId; ?>">');
                        });
                    });
                </script>
            <?php else: ?>
                <?php foreach ($styles as $style) { ?> 
                    <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
                <?php } ?>


                <div class="mf-payment-methods-container ">
                    <p class="mf-grey-text"><?php echo $text_how_to_pay; ?></p>

                    <!--Start Card Section-->
                    <?php if (count($paymentMethods['cards']) > 0): ?>

                        <div class="mf-divider">
                            <span><?php echo $text_pay_with; ?></span>
                        </div>

                        <?php foreach ($paymentMethods['cards'] as $mfCard) { ?> 
                            <?php $mfPaymentTitle = (($language == 'ar') ? ($mfCard->PaymentMethodAr) : ($mfCard->PaymentMethodEn)); ?>
                            <button class="mf-card-container" mfCardId="<?php echo $mfCard->PaymentMethodId; ?>">
                                <div class="mf-row-container">
                                    <img class="mf-payment-logo" src="<?php echo $mfCard->ImageUrl; ?>" title="<?php echo $mfPaymentTitle; ?>" alt="<?php echo $mfPaymentTitle; ?>"/>
                                    <h5 class="mf-payment-text mf-card-title"><?php echo $mfPaymentTitle; ?></h5>
                                </div>
                                <h5 class="mf-payment-text">
                                    <?php echo $mfCard->GatewayData['GatewayTotalAmount']; ?> <?php echo $mfCard->GatewayData['GatewayCurrency']; ?>
                                </h5>
                            </button>
                        <?php } ?>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                //card button clicked
                                $("[mfCardId]").on('click', function (e) {
                                    $('#mfButtonCheckout').button('loading');
                                    $('#mfFormCheckout').append('<input type="hidden" name="mfCardData" value="' + $(this).attr('mfCardId') + '">');
                                });
                            });
                        </script>

                    <?php endif; ?>
                    <!--End Card Section-->
                    <!--Start Form Section-->
                    <?php if (count($paymentMethods['form']) > 0): ?>
                        <div class="mf-divider">
                            <span>
                                <?php if (count($paymentMethods['cards']) > 0): ?><?php echo $text_or; ?><?php endif; ?>
                                <?php echo $text_insert_card; ?>
                            </span>
                        </div>

                        <div id="mf-card-element"></div>
                        <script type="text/javascript">
                            $(document).ready(function () {

                                var mfConfig = {
                                    countryCode: "<?php echo $session->CountryCode; ?>",
                                    sessionId: "<?php echo $session->SessionId; ?>",
                                    cardViewId: "mf-card-element",
                                    // The following style is optional.
                                    style: {
                                        cardHeight: "<?php echo $height; ?>",
                                        direction: "<?php echo (($language == 'ar') ? ('rtl') : ('ltr')); ?>",
                                        input: {
                                            color: "black",
                                            fontSize: "13px",
                                            fontFamily: "sans-serif",
                                            inputHeight: "32px",
                                            inputMargin: "-1px",
                                            borderColor: "c7c7c7",
                                            borderWidth: "1px",
                                            borderRadius: "0px",
                                            boxShadow: "",
                                            placeHolder: {
                                                holderName: "<?php echo $text_holder_name; ?>",
                                                cardNumber: "<?php echo $text_card_number; ?>",
                                                expiryDate: "<?php echo $text_expire_date; ?>",
                                                securityCode: "<?php echo $text_cvv; ?>"
                                            }
                                        },
                                        label: {
                                            display: false,
                                            color: "black",
                                            fontSize: "13px",
                                            fontFamily: "sans-serif",
                                            text: {
                                                holderName: "Card Holder Name",
                                                cardNumber: "Card Number",
                                                expiryDate: "ExpiryDate",
                                                securityCode: "Security Code"
                                            }
                                        },
                                        error: {
                                            borderColor: "red",
                                            borderRadius: "8px",
                                            boxShadow: "0px"
                                        }
                                    }
                                };
                                myFatoorah.init(mfConfig);
                                window.addEventListener("message", myFatoorah.recievedMessage, false);
                                var fc = '#mfFormCheckout';
                                var pl = '#mfButtonCheckout';

                                //form button clicked
                                $(pl).on('click', function (e) {
                                    $('#mfErrorDiv').remove();
                                    e.preventDefault(); // Disable "Place Order" button

                                    $(pl).button('loading');
                                    myFatoorah.submit()
                                            .then(function (response) {
                                                // On success
                                                $(fc).append('<input type="hidden" name="mfFormData" value="' + response.SessionId + '">');
                                                $(fc).submit(); // Trigger submit

                                            }, function (error) { 
                                                // In case of errors
                                                $(pl).button('reset');
                                                $('#mf-card-element').before('<div id="mfErrorDiv" class="alert alert-danger"><i class="fa fa-exclamation-circle"> ' + error + '</i><button type="button" class="close" data-dismiss="alert">×</button></div>');
                                            });
                                });
                            });
                        </script>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <!--End Form Section-->

            
        <?php endif; ?>
        <?php if (($displayTypes == 'myfatoorah') || (count($paymentMethods['form']) > 0) || ((count($paymentMethods['cards']) == 1 && count($paymentMethods['form']) == 0))): ?>      
        <div class="buttons">
            <div class="pull-right">
                <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" id="mfButtonCheckout" data-loading-text="Loading..." />
            </div>
        </div>
        <?php endif; ?>    
    </form>
<?php endif; ?>
