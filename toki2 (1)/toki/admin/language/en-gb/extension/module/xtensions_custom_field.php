<?php
// Heading
$_['heading_title']         = 'Xtensions Custom Fields';

// Text
$_['text_success']          = 'Success: You have modified custom fields!';
$_['text_list']             = 'Xtensions Custom Field List';
$_['text_add']              = 'Add Xtensions Custom Field';
$_['text_edit']             = 'Edit Xtensions Custom Field';
$_['text_choose']           = 'Choose';
$_['text_select']           = 'Select';
$_['text_radio']            = 'Radio';
$_['text_checkbox']         = 'Checkbox';
$_['text_input']            = 'Input';
$_['text_text']             = 'Text';
$_['text_textarea']         = 'Textarea';
$_['text_file']             = 'File';
$_['text_date']             = 'Date';
$_['text_datetime']         = 'Date &amp; Time';
$_['text_time']             = 'Time';
$_['text_account']          = 'Account';
$_['text_address']          = 'Address';
$_['text_regex']			= 'Validation';
$_['button_custom_field_value_add'] = 'Add Option';
$_['button_add'] = 'Add';
$_['text_confirm'] = "Are you sure you want to delete. This operation can not be undone. Press ok to delete, cancel to stay.";

// Column
$_['column_name']           = 'Custom Field Name';
$_['column_location']       = 'Location';
$_['column_type']           = 'Type';
$_['column_sort_order']     = 'Sort Order';
$_['column_action']         = 'Action';

// Entry
$_['entry_name']            = 'Custom Field Name';
$_['entry_location']        = 'Location';
$_['entry_type']            = 'Type';
$_['entry_value']           = 'Default Value'; 
$_['entry_custom_value']    = 'Custom Field Value Name';
$_['entry_customer_group']  = 'Customer Group';
$_['entry_required']        = 'Required';
$_['entry_status']          = 'Status';
$_['entry_sort_order']      = 'Sort Order';
$_['entry_sort_order']      = 'Sort Order';
$_['entry_attributes']      = 'More Attributes';
$_['entry_visibility']      = "Custom Field's Visibility";
$_['entry_validation']      = 'Regex Validation';

$_['entry_isnumeric']      = 'Numeric Field';
$_['entry_identifier']      = 'Field Identifier';
$_['entry_identifier_help'] = "Identifier should be english alphabetic, non numeric, without space text and is used to retreive custom fields during checkout, if needed for payments and shipping API's. This field is mandatory";
$_['entry_mask']    		= 'Masking for field';
$_['entry_mask_help']	= 'For fields like cpf, cnpj etc you may need masking and you can use the put the mask pattern like (99.999-99) (Only for text type fields)';
$_['entry_minimum']  			= 'Min Length';
$_['entry_maximum']        		= 'Max Length';
$_['char_help1']  			= 'Leave fields blank if you use masking';
$_['char_help2']       		= 'For fixed length put same number in minimum and maximum';
$_['entry_history']         = 'Order History(Customer)';
$_['entry_list']      		= 'Address Lists';
$_['entry_email']      		= 'Order Emails';
$_['entry_invoice']      	= 'Order Invoices';
$_['entry_tips']      		= 'Tooltips(if any)';
$_['entry_error']      		= 'Error Message';
$_['entry_error_help']     	= 'If field is required for at least one customer group';
$_['entry_regional_validation']      	= 'Country based Field Validation';
$_['entry_regional_validation_help']    = 'Select validation to formulate if field is valid or not';
// Help
$_['help_sort_order']       = 'Use minus to count backwards from the last field in the set.';
$_['important_note']       = 'Important Notes';
$_['help_regex']            = 'Use regex. E.g: /[a-zA-Z0-9_-]/';
$_['address_message']       = 'To display address fields in address formats, define address_format in Settings for Best Checkout module using field identifier under tab Checkout Misc';
$_['fields_retreival1']      = "1) To retrieve account Custom field in payment or shipping methods modules during checkout, call this \$this->session->data['customer']['custom_field']['defined_identifier']";
$_['fields_retreival2']      = "2) To retrieve payment address Custom field in payment methods modules during checkout, call this \$this->session->data['payment']['custom_field']['defined_identifier']";
$_['fields_retreival3']      = "3) To retrieve shipping address Custom field in shipping methods modules during checkout, call this \$this->session->data['shipping']['custom_field']['defined_identifier']";

// Error
$_['xerror_permission']      = 'Warning: You do not have permission to modify custom fields!';
$_['xerror_name']            = 'Custom Field Name must be between 1 and 128 characters!';
$_['xerror_type']            = 'Warning: Custom Field Values required!';
$_['xerror_custom_value']    = 'Custom Value Name must be between 1 and 128 characters!';
$_['xerror_error']           = 'You must define an error message for a required field!';
$_['xerror_identifier']      = 'Identifier is required and must be english without spaces and between 1 and 30 characters!';
