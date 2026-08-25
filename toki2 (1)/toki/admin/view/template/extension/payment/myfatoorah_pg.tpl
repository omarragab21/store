<?php echo $header; ?><?php echo $column_left; ?> 
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-payment" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning): ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fa fa-exclamation-circle"></i>
                <?php echo $error_warning; ?> 
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-payment" class="form-horizontal">


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_status" id="input-status" class="form-control">
                                <?php if ($status): ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php else: ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group" required>
                        <label class="col-sm-2 control-label" for="input-countryMode">
                            <span data-toggle="tooltip" title="<?php echo $tooltip_countryMode; ?>"><?php echo $entry_countryMode; ?></span>
                        </label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_countryMode" id="input-countryMode" class="form-control">
                                <?php foreach ($mfCountries as $id => $obj) { ?>
                                    <?php if ($countryMode == $id): ?>
                                        <option value="<?php echo $id; ?>" selected="selected"><?php echo $obj[$nameIndex]; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $id; ?>"><?php echo $obj[$nameIndex]; ?></option>
                                    <?php endif; ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-test"><?php echo $entry_test; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_test" id="input-test" class="form-control">
                                <?php if ($test): ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php else: ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-apiKey"><?php echo $entry_apiKey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="<?php echo $ocCode; ?>_apiKey" value="<?php echo $apiKey; ?>" placeholder="<?php echo $entry_apiKey; ?>" id="input-apiKey" class="form-control"/>
                            <?php if ($error_apiKey): ?>
                                <div class="text-danger"><?php echo $error_apiKey; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-webhook_secret_key"><?php echo $entry_webhook_secret_key; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="<?php echo $ocCode; ?>_webhook_secret_key" value="<?php echo $webhook_secret_key; ?>" placeholder="<?php echo $entry_webhook_secret_key; ?>" id="input-webhook_secret_key" class="form-control"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="<?php echo $ocCode; ?>_sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-debug"><?php echo $entry_debug; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_debug" id="input-debug" class="form-control">
                                <?php if ($debug): ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php else: ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_initial_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_initial_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($initial_order_status_id == $order_status['order_status_id']): ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php endif; ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status_id == $order_status['order_status_id']): ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php endif; ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_failed_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_failed_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($failed_order_status_id == $order_status['order_status_id']): ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php endif; ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-payment_type"><?php echo $entry_payment_type; ?></label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_payment_type" id="input-payment_type" class="form-control">
                                <?php if ($payment_type != 'myfatoorah'): ?>
                                    <option value="multigateways" selected="selected"><?php echo $text_multigateways_page; ?></option>
                                    <option value="myfatoorah"><?php echo $text_myfatoorah_page; ?></option>
                                <?php else: ?>
                                    <option value="multigateways"><?php echo $text_multigateways_page; ?></option>
                                    <option value="myfatoorah" selected="selected"><?php echo $text_myfatoorah_page; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-saveCard">
                            <span data-toggle="tooltip" title="<?php echo $tooltip_saveCard; ?>"><?php echo $entry_saveCard; ?></span>
                        </label>
                        <div class="col-sm-10">
                            <select name="<?php echo $ocCode; ?>_saveCard" id="input-saveCard" class="form-control">
                                <?php if ($saveCard): ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php else: ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>