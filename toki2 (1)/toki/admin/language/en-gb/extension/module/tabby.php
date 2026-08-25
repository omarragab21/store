<?php

// Heading
$_['heading_title']    = 'Tabby Module';


// Text
$_['text_tabby']		 				= '<a target="_BLANK" href="https://www.tabby.ai/"><img src="view/image/payment/tabby.png" alt="Tabby" title="Tabby" style="max-width: 90px;" /></a>';
$_['text_extensions']     				= 'Extensions';
$_['text_module']     				    = 'Module';
$_['text_edit']          				= 'Edit Tabby';

$_['text_api']				 	 		= 'Tabby API';
$_['text_checkout']				 		= 'Checkout';
$_['text_installments']					= 'Installments';

$_['text_no_transaction']			    = 'No transaction information available';
$_['text_no_capture']                   = 'no_capture';

// Entry
$_['entry_status']		 				= 'Status';
$_['entry_countries']		 			= 'Allowed Countries';
$_['entry_sort_order']	 				= 'Sort Order';
$_['entry_public_key']					= 'Public Key';
$_['entry_secret_key']					= 'Secret Key';
$_['entry_checkout_card_status']		= 'Advanced Card';
$_['entry_promo']				 		= 'Tabby Product Promotions';
$_['entry_cart_promo']				 	= 'Tabby Cart Promotions';
$_['entry_promo_limit']				 	= 'Tabby Promo max price <br />(0 - unlimited)';
$_['entry_promo_theme']				 	= 'Tabby Promo theme <br />(blank for default)';
$_['entry_order_status']                = 'Order Status (AUTHORIZED)';
$_['entry_order_status_capture']        = 'Order Status (CAPTURED)';
$_['entry_order_status_refund']         = 'Order Status (REFUNDED)';
$_['entry_order_status_cancel']         = 'Order Status (CANCELLED)';
$_['entry_capture_on']                  = 'Capture On';
$_['entry_debug']				 		= 'Debug Logging';
$_['entry_transaction_id']              = 'Transaction #:';
$_['entry_transaction_date']            = 'Date:';
$_['entry_transaction_status']          = 'Status:';
$_['entry_transaction_amount']          = 'Amount:';
$_['entry_transaction_currency']        = 'Currency:';
$_['entry_transaction_reference_id']    = 'Reference #:';
$_['entry_captures']                    = 'Captures';
$_['entry_refunds']                     = 'Refunds';
$_['entry_parent_id']                   = 'Parent #';
$_['entry_deletion_timeout']            = 'Abandoned checkout order deletion timeout';
$_['entry_merchant_code']               = 'Change merchant code format to {country}_{currency}';
$_['entry_integration_type']            = 'Integration type<br />(Redirect to HPP or Popup)';
$_['text_it_redirect']                  = 'Redirect to HPP';
$_['text_it_popup']                     = 'Popup';

// Help

// Buttons
$_['text_button_capture']               = 'Capture payment';
$_['text_button_void']                  = 'Void payment';
$_['text_button_closed']                = 'Close payment';
$_['text_button_refund']                = 'Refund';
$_['help_deletion_timeout']             = 'Set timeout in minutes before an order is automatically deleted in case customer abandons checkout. Min 15, max 1440.';
// Success
$_['success_save']		 				= 'Success: You have modified Tabby configuration!';
$_['text_success']		 				= 'Success: You have modified Tabby configuration!';

// Error
$_['error_permission']	 				= 'Warning: You do not have permission to modify Tabby Module!';

$_['warning_not_configured']            = 'Please configure Tabby Module in order to use payment methods <a href="%s">here</a>.';
