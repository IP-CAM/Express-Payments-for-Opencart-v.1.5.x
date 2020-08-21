<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
      <div class="heading">
        <h1><?php echo $heading_title; ?></h1>
        <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
      </div>
    <div class="content">
      <table class="form">
        <tr>
          <td><a target="_blank" href="https://express-pay.by"><img src="/admin/view/image/payment/erip_expresspay_big.png" width="270" height="91" alt="exspress-pay.by" title="express-pay.by"></a></td>
          <td><?php echo $text_about; ?></td>
        </tr>
      </table>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span><?php echo $token_label; ?></td>
            <td>
              <input required style="width: 50%;" type="text" name="erip_token" value="<?php echo $erip_token_value; ?>" />
  				    <div style='font-size:0.9em'><?php echo $token_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $handler_label; ?></td>
            <td><input style="width: 50%;" readonly="readonly" type="text" name="erip_handler_url" value="<?php echo $handler_url; ?>" /></td>
          </tr>

          <tr>
            <td><?php echo $sign_invoices_label; ?></td>
            <td>
              <input style="margin-left: 0;" <?php echo ( $erip_sign_invoices_value == 'on') ? 'checked' : ''; ?> type="checkbox" name="erip_sign_invoices" />
              <div style='font-size:0.9em'><?php echo $sign_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $secret_key_label; ?></td>
            <td>
              <input style="width: 50%;" type="text" name="erip_secret_key" value="<?php echo $erip_secret_key_value; ?>" />
  				    <div style='font-size:0.9em'><?php echo $secret_key_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $sign_notify_label; ?></td>
            <td>
              <input style="margin-left: 0;" <?php echo ( $erip_sign_notify_value == 'on') ? 'checked' : ''; ?> type="checkbox" name="erip_sign_notify" />
              <div style='font-size:0.9em'><?php echo $sign_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $secret_key_notify_label; ?></td>
            <td>
              <input style="width: 50%;" type="text" name="erip_secret_key_notify" value="<?php echo $erip_secret_key_notify_value; ?>" />
              <div style='font-size:0.9em'><?php echo $secret_key_notify_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $name_editable_label; ?></td>
            <td>
              <input style="margin-left: 0;" <?php echo ( $erip_name_editable_value == 'on') ? 'checked' : ''; ?> type="checkbox" name="erip_name_editable" />
              <div style='font-size:0.9em'><?php echo $name_editable_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $address_editable_label; ?></td>
            <td>
              <input style="margin-left: 0;" <?php echo ( $erip_address_editable_value == 'on') ? 'checked' : ''; ?> type="checkbox" name="erip_address_editable" />
              <div style='font-size:0.9em'><?php echo $address_editable_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $amount_editable_label; ?></td>
            <td>
              <input style="margin-left: 0;" <?php echo ( $erip_amount_editable_value == 'on') ? 'checked' : ''; ?> type="checkbox" name="erip_amount_editable" />
              <div style='font-size:0.9em'><?php echo $amount_editable_comment; ?></div>
            </td>
          </tr>

          <tr>
            <td><?php echo $test_mode_label; ?></td>
            <td><input style="margin-left: 0;" <?php echo ( $erip_test_mode_value == 'on') ? 'checked' : ''; ?> type="checkbox" name="erip_test_mode" /></td>
          </tr>

          <tr>
            <td><span class="required">*</span><?php echo $url_api_label; ?></td>
            <td><input required="required" style="width: 50%;" type="text" name="erip_url_api" value="<?php echo ( !empty($erip_url_api_value) ) ? $erip_url_api_value : 'https://api.express-pay.by'; ?>" />
            </td>
          </tr>

          <tr>
            <td><span class="required">*</span><?php echo $url_sandbox_api_label; ?></td>
            <td><input required="required" style="width: 50%;" type="text" name="erip_url_sandbox_api" value="<?php echo ( !is_null($erip_url_sandbox_api_value) ) ? $erip_url_sandbox_api_value : 'https://sandbox-api.express-pay.by'; ?>" />
            </td>
          </tr>

          <tr>
            <td><?php echo $message_success_label; ?></td>
            <td><textarea style="width: 50%; height: 120px; max-width: 50%;" name="erip_message_success"><?php echo $erip_message_success_value; ?></textarea>
            </td>
          </tr>

          <tr>
            <td style="font-weight: bold; font-size: 16px;"><?php echo $settings_module_label; ?></td>
            <td></td>
          </tr>
		    <tr>
            <td><?php echo $status_label; ?></td>
            <td><select name="erip_expresspay_status">
              <?php if ($erip_expresspay_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><?php echo $pending_status; ?></td>
            <td><select name="erip_pending_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $erip_pending_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $processing_status; ?></td>
            <td><select name="erip_processing_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $erip_processing_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
		      <tr>
            <td><?php echo $cancel_status; ?></td>
            <td><select name="erip_cancel_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $erip_cancel_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $sort_order_label; ?></td>
            <td><input type="text" name="erip_sort_order" value="<?php echo $erip_sort_order; ?>" size="1" /></td>
          </tr>
        </table>

        <div class="copyright" style="text-align: center;">
          &copy; Все права защищены | ООО «ТриИнком», 2013-<?php echo date('Y'); ?><br/>
          <?php echo $text_version . ERIP_EXPRESSPAY_VERSION ?>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>