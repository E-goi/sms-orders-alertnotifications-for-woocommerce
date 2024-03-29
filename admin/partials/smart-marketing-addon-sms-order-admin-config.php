<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.e-goi.com
 * @since      1.0.0
 *
 * @package    Smart_Marketing_Addon_Sms_Order
 * @subpackage Smart_Marketing_Addon_Sms_Order/admin/partials
 */

$this->smsonw_woocommerce_dependency_notice();

$form_id = ! empty( $_POST['form_id'] ) ? sanitize_key( $_POST['form_id'] ) : null;

if ( ! empty( $form_id ) ) {
	$result = $this->smsonw_process_config_form( $_POST );
}

$sender_option       = json_decode( get_option( 'egoi_sms_order_sender' ), true );
$recipients          = json_decode( get_option( 'egoi_sms_order_recipients' ), true );
$texts               = json_decode( get_option( 'egoi_sms_order_texts' ), true );
$payment_texts       = json_decode( get_option( 'egoi_sms_order_payment_texts' ), true );
$payment_email_texts = json_decode( get_option( 'egoi_email_order_payment_texts' ), true );
$follow_price        = json_decode( get_option( 'egoi_sms_follow_price' ), true );
$abandoned_cart_obj  = json_decode( get_option( 'egoi_sms_abandoned_cart' ), true );
$transactional_email = get_option( 'egoi_transactional_email' );

if ( empty( $abandoned_cart_obj ) ) {
	$abandoned_cart_obj = array(
		'message'            => __( 'Olá relembramos que deixou produtos no seu carrinho, clique aqui para recuperar %link%', 'smart-marketing-addon-sms-order' ),
		'title_pop'          => __( 'Olá deixe aqui o seu contacto para conseguirmos entrar em contacto consigo.', 'smart-marketing-addon-sms-order' ),
		'button_name'        => __( 'Enviar', 'smart-marketing-addon-sms-order' ),
		'button_cancel_name' => __( 'Cancelar', 'smart-marketing-addon-sms-order' ),
		'enable'             => '',
		'shortener'          => 'on',
		'background_color'   => '#000000',
		'text_color'         => '#ffffff',
		'button_color'       => '#a19fa1',
		'button_text_color'  => '#000000',
	);
	update_option( 'egoi_sms_abandoned_cart', wp_json_encode( $abandoned_cart_obj ) );
}

if ( empty( $follow_price ) ) {
	$follow_price = array(
		'follow_price_message'     => __( 'Olá, alteramos o preço de %product%, clique aqui para ver as alterações %link%', 'smart-marketing-addon-sms-order' ),
		'follow_title_pop'         => __( 'Olá deixe aqui o seu contacto para conseguirmos entrar em contacto consigo.', 'smart-marketing-addon-sms-order' ),
		'follow_price_button_name' => __( 'Enviar', 'smart-marketing-addon-sms-order' ),
		'enable'                   => '',
		'shortener'                => 'on',
		'background_color'         => '#000000',
		'text_color'               => '#ffffff',
		'button_color'             => '#a19fa1',
		'button_text_color'        => '#000000',
	);
	update_option( 'egoi_sms_follow_price', wp_json_encode( $follow_price ) );
}

if ( empty( $follow_price['follow_background_color'] ) ) {
	$follow_price['follow_background_color']  = '#000000';
	$follow_price['follow_text_color']        = '#ffffff';
	$follow_price['follow_button_color']      = '#a19fa1';
	$follow_price['follow_button_text_color'] = '#000000';
}


$senders        = $this->helper->smsonw_get_senders();
$soap_error     = $this->helper->smsonw_get_soap_error();
$balance        = $this->helper->smsonw_get_balance();
$reminder_times = array( '1', '12', '24', '36', '48', '72' );

?>
<br>
<br>
<span id="form_info" data-form-id="<?php echo esc_attr( $form_id ); ?>" data-form-lang="<?php echo ! empty( $_POST['sms_text_language'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['sms_text_language'] ) ) ) : ''; ?>" data-form-method="<?php echo ! empty( $_POST['sms_payment_method'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['sms_payment_method'] ) ) ) : ''; ?>"></span>
<!-- head -->
<h1 class="logo">Smart Marketing - <?php esc_html_e( 'SMS Notifications', 'smart-marketing-addon-sms-order' ); ?></h1>
<p class="breadcrumbs">
	<span class="prefix"><?php esc_html_e( 'You are here: ', 'smart-marketing-addon-sms-order' ); ?></span>
	<strong>Smart Marketing</a> &rsaquo;
		<span class="current-crumb"><?php esc_html_e( 'SMS Notifications', 'smart-marketing-addon-sms-order' ); ?></strong></span>
</p>
<hr/>

<p class="nav-tab-wrapper">
	<a class="nav-tab nav-tab-addon nav-tab-active" id="nav-tab-sms-senders">
		<?php esc_html_e( 'General settings', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-texts">
		<?php esc_html_e( 'SMS Messages', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-payment-texts">
		<?php esc_html_e( 'Payments SMS', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-emailpayment-texts">
		<?php esc_html_e( 'Payments Email', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-tracking-texts">
		<?php esc_html_e( 'SMS URL Tracking', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-follow-price">
		<?php esc_html_e( 'SMS Follow Price', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-abandoned-cart">
		<?php esc_html_e( 'Abandoned Cart', 'smart-marketing-addon-sms-order' ); ?>
	</a>

	<a class="nav-tab nav-tab-addon" id="nav-tab-sms-help">
		<?php esc_html_e( 'Help', 'smart-marketing-addon-sms-order' ); ?>
	</a>
</p>

<!-- wrap SMS Senders/Recipients -->
<div class="wrap tab wrap-addon" id="tab-sms-senders">
	<div class="wrap egoi4wp-settings">
		<div class="row">

			<?php
			if ( isset( $form_id ) && 'form-sms-order-senders' == $form_id ) {
				if ( $result ) {
					$this->helper->smsonw_admin_notice_success();
				} else {
					$this->helper->smsonw_admin_notice_error();
				}
			}
			?>

			<?php if ( empty( $senders ) && ! $soap_error ) { ?>

				<div class="notice notice-error" style="max-width: 800px;">
					<p>
						<?php esc_html_e( 'To use this plugin you need to add a sender and activate the SMS transactional within your E-goi account.', 'smart-marketing-addon-sms-order' ); ?>
						<br>
						<a id="button_view_help" target="_blank"><?php esc_html_e( 'View help', 'smart-marketing-addon-sms-order' ); ?></a>
					</p>
				</div>

			<?php } ?>

			<?php

			if ( $soap_error ) {
					$senders = null;

				?>

			<div class="notice notice-error" style="max-width: 800px;">
				<p>
					<?php esc_html_e( 'We are having some technical difficulties reaching the endpoint. Please try again latter.', 'smart-marketing-addon-sms-order' ); ?>
				</p>
			</div>

			<?php } ?>

			<div class="main-content col col-12" style="margin:0 0 20px;<?php echo empty( $senders ) ? 'display: none;' : null; ?>">

				<table style="max-width: 900px;">
					<tr>
						<td width="50%" valign="top" style="padding-right: 20px;">
							<form action="#" method="post" class="form-sms-order-config" id="form-sms-order-senders">
								<?php wp_nonce_field( 'form-sms-order-senders' ); ?>
								<input name="form_id" type="hidden" value="form-sms-order-senders" />

								<p class="label_text" style="margin-top: 8px;"><?php esc_html_e( 'E-goi SMS Sender', 'smart-marketing-addon-sms-order' ); ?></p>

								<select class="e-goi-option-select-admin-forms" style="width: 100%;" name="sender_hash" id="sender_hash" required>
									<option value="off" selected>
										<?php esc_html_e( 'Disable', 'smart-marketing-addon-sms-order' ); ?>
									</option>
									<?php
									if ( isset( $senders ) && count( $senders ) > 0 ) {
										foreach ( $senders as $sender ) {
											?>
											<option value="<?php echo esc_attr( $sender->sender_id ); ?>" <?php selected( $sender->sender_id, $sender_option['sender_hash'] ); ?> >
												<?php echo esc_attr( $sender->cellphone ); ?>
											</option>
											<?php
										}
									}
									?>
								</select>

								<p class="label_text"><?php esc_html_e( 'Admin Cellphone', 'smart-marketing-addon-sms-order' ); ?></p>

								<select name="admin_prefix" class="e-goi-option-select-admin-forms" style="width: 49%; float: left;" required >
									<?php
									foreach ( unserialize( EFWP_COUNTRY_CODES ) as $key => $value ) {
										$string = ucwords( strtolower( $value['country_pt'] ) ) . ' (+' . $value['prefix'] . ')';
										?>
										<option value="<?php echo esc_attr( $value['prefix'] ); ?>" <?php selected( $value['prefix'], $sender_option['admin_prefix'] ); ?> ><?php echo esc_html( $string ); ?></option>
										<?php
									}
									?>
								</select>
								<input type="text" id="admin_phone" name="admin_phone" class="regular-text" style="width: 50%; height: 38px;"
									   value="<?php echo isset( $sender_option['admin_phone'] ) ? esc_attr( $sender_option['admin_phone'] ) : null; ?>"
								/>


								<p class="label_text">
									<?php esc_html_e( 'Order SMS notifications', 'smart-marketing-addon-sms-order' ); ?>
								</p>
								<p class="label_text_mini">
									<?php esc_html_e( 'Select to which order status SMS will be sent', 'smart-marketing-addon-sms-order' ); ?>
								</p>

								<table border='0' class="widefat striped" style="width: 100%;">
									<thead>
									<tr>
										<th><?php esc_html_e( 'Order Status', 'smart-marketing-addon-sms-order' ); ?></th>
										<th style="text-align: center;"><?php esc_html_e( 'Customer', 'smart-marketing-addon-sms-order' ); ?></th>
										<th style="text-align: center;"><?php esc_html_e( 'Admin', 'smart-marketing-addon-sms-order' ); ?></th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ( $this->helper->smsonw_get_order_statuses() as $cod => $name ) { ?>
										<tr>
											<td><?php echo esc_html( $name ); ?></td>
											<td align="center">
												<input class="input-checkbox" type="checkbox" name="egoi_sms_order_customer_<?php echo esc_attr( $cod ); ?>" value="1"
													<?php checked( $recipients[ 'egoi_sms_order_customer_' . $cod ], 1 ); ?>
												/>
											</td>
											<td align="center">
												<input class="input-checkbox admin-order-status" type="checkbox" name="egoi_sms_order_admin_<?php echo esc_attr( $cod ); ?>" value="1"
													<?php checked( $recipients[ 'egoi_sms_order_admin_' . $cod ], 1 ); ?>
												/>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>

								<p class="label_text"><?php esc_html_e( 'Send SMS notifications to customers', 'smart-marketing-addon-sms-order' ); ?></p>

								<p class="label_text_mini">
									<input type="radio" name="notification_option" id="notification_option_0" value="0" required
										<?php echo ! isset( $recipients['notification_option'] ) || $recipients['notification_option'] == 0 ? 'checked' : null; ?>
									/>
									<label class="label_text_mini" for="notification_option_0"><?php esc_html_e( 'All customers', 'smart-marketing-addon-sms-order' ); ?></label>
								</p>
								<p class="label_text_mini" style="margin-bottom: 20px;">
									<input type="radio" name="notification_option" id="notification_option_1" value="1" <?php checked( $recipients['notification_option'], 1 ); ?> />
									<label class="label_text_mini" for="notification_option_1"><?php esc_html_e( 'Only customers who ask for it in checkout', 'smart-marketing-addon-sms-order' ); ?></label>
								</p>

								<hr>

								<p class="label_text"><?php esc_html_e( 'Configurations Multibanco (Portuguese payment method)', 'smart-marketing-addon-sms-order' ); ?></p>

								<p class="label_text_mini">
									<input type="checkbox" name="egoi_payment_info" id="egoi_payment_info" value="1"
										<?php echo ! isset( $recipients['egoi_payment_info'] ) || $recipients['egoi_payment_info'] == 1 ? 'checked' : null; ?>
									/>
									<label for="egoi_payment_info"><?php esc_html_e( 'Send SMS to your customers with Multibanco payment information', 'smart-marketing-addon-sms-order' ); ?></label>
								</p>
								<p class="label_text_mini" >

								<?php if ( constant( 'ALTERNATE_WP_CRON' ) !== false ) { ?>
									<input type="checkbox" name="egoi_reminders" id="egoi_reminders" value="1"
										<?php checked( $recipients['egoi_reminders'], 1 ); ?>
									/>
									<label for="egoi_reminders"><?php esc_html_e( 'Send SMS to remind the information for payment Multibanco', 'smart-marketing-addon-sms-order' ); ?></label>

									<?php if ( $transactional_email['check_transactional_email'] !== 0 ) { ?>
										<p>
										<input type="checkbox" name="egoi_email_reminders" id="egoi_email_reminders" value="1"
											<?php checked( $recipients['egoi_email_reminders'], 1 ); ?>
										/>
										<label for="egoi_email_reminders"><?php esc_html_e( 'Send Email to remind the information for payment Multibanco', 'smart-marketing-addon-sms-order' ); ?></label>
										</p>
									<?php } else { ?>
										<p>
										<input type="checkbox" disabled />
										<?php esc_html_e( 'Send Email to remind the information for payment Multibanco', 'smart-marketing-addon-sms-order' ); ?>
										</p>
										<div style="width: 100%; background-color: white; text-align: center; border: 1px solid #dddddd; margin-top: 10px;">
											<p class="label_text_mini"><?php esc_html_e( 'You need to enable Egoi Transacional Email.', 'smart-marketing-addon-sms-order' ); ?>
											<a href="<?php echo esc_attr( admin_url( 'admin.php?page=egoi-4-wp-transactional-email' ) ); ?>">
												<?php esc_html_e( 'Enable here.', 'smart-marketing-addon-sms-order' ); ?>
											</a>
											</p>
										</div>
									<?php } ?>


								<?php } else { ?>
									<p>
									<input type="checkbox" disabled />
									<?php esc_html_e( 'Send SMS to remind the information for payment Multibanco', 'smart-marketing-addon-sms-order' ); ?>
									</p>
									<p>
									<input type="checkbox" disabled />
									<?php esc_html_e( 'Send Email to remind the information for payment Multibanco', 'smart-marketing-addon-sms-order' ); ?>
									</p>
									<div style="width: 100%; background-color: white; text-align: center; border: 1px solid #dddddd; margin-top: 10px;">
										<p class="label_text_mini"><?php esc_html_e( 'You need to enable wp_cron in wp-config, use:', 'smart-marketing-addon-sms-order' ); ?></p>
										<pre>define ('ALTERNATE_WP_CRON', true);</pre>
									</div>
								<?php } ?>
								</p>


								<hr>

								<p class="label_text"><?php esc_html_e( 'Configurations Pagseguro (Brazilian Gateway)', 'smart-marketing-addon-sms-order' ); ?></p>

								<p class="label_text_mini">
									<input type="checkbox" name="egoi_payment_info_billet" id="egoi_payment_info_billet" value="1"
										<?php echo ! isset( $recipients['egoi_payment_info_billet'] ) || $recipients['egoi_payment_info_billet'] == 1 ? 'checked' : null; ?>
									/>
									<label for="egoi_payment_info_billet"><?php esc_html_e( 'Send SMS to your customers with PagSeguro payment information', 'smart-marketing-addon-sms-order' ); ?></label>
								</p>
								<p class="label_text_mini">
								<?php if ( constant( 'ALTERNATE_WP_CRON' ) !== false ) { ?>
									<input type="checkbox" name="egoi_reminders_billet" id="egoi_reminders_billet" value="1"
										<?php checked( $recipients['egoi_reminders_billet'], 1 ); ?>
									/>
									<label for="egoi_reminders_billet"><?php esc_html_e( 'Send SMS to remind the payment information of the PagSeguro', 'smart-marketing-addon-sms-order' ); ?></label>

									<?php if ( $transactional_email['check_transactional_email'] !== 0 ) { ?>
										<p>
										<input type="checkbox" name="egoi_email_reminders_billet" id="egoi_email_reminders_billet" value="1"
											<?php checked( $recipients['egoi_email_reminders_billet'], 1 ); ?>
										/>
										<label for="egoi_email_reminders_billet"><?php esc_html_e( 'Send Email to remind the payment information of the PagSeguro', 'smart-marketing-addon-sms-order' ); ?></label>
										</p>
									<?php } else { ?>
										<p>
										<input type="checkbox" disabled />
										<?php esc_html_e( 'Send Email to remind the payment information of the PagSeguro', 'smart-marketing-addon-sms-order' ); ?>
										</p>
										<div style="width: 100%; background-color: white; text-align: center; border: 1px solid #dddddd; margin-top: 10px;">
											<p class="label_text_mini"><?php esc_html_e( 'You need to enable Egoi Transacional Email.', 'smart-marketing-addon-sms-order' ); ?>
											<a href="<?php echo esc_attr( admin_url( 'admin.php?page=egoi-4-wp-transactional-email' ) ); ?>">
												<?php esc_html_e( 'Enable here.', 'smart-marketing-addon-sms-order' ); ?>
											</a>
											</p>
										</div>
									<?php } ?>

								<?php } else { ?>
									<p>
									<input type="checkbox" disabled />
									<?php esc_html_e( 'Send SMS to remind the information for PagSeguro payment', 'smart-marketing-addon-sms-order' ); ?>
									</p>
									<p>
									<input type="checkbox" disabled />
									<?php esc_html_e( 'Send Email to remind the information for PagSeguro payment', 'smart-marketing-addon-sms-order' ); ?>
									</p>
									<div style="width: 100%; background-color: white; text-align: center; border: 1px solid #dddddd; margin-top: 10px;">
										<p class="label_text_mini"><?php esc_html_e( 'You need to enable wp_cron in wp-config, use:', 'smart-marketing-addon-sms-order' ); ?></p>
										<pre>define ('ALTERNATE_WP_CRON', true);</pre>
									</div>
								<?php } ?>
								</p>

								<hr>
								<p class="label_text"><?php esc_html_e( 'Choose the amount of time to send the reminder.', 'smart-marketing-addon-sms-order' ); ?></p>

								<select name="egoi_reminders_time" id="egoi_reminders_time" class="e-goi-option-select-admin-forms" style="width: 49%;">
									<?php $recipients['egoi_reminders_time'] = empty( $recipients['egoi_reminders_time'] ) ? 48 : $recipients['egoi_reminders_time']; ?>
									<?php foreach ( $reminder_times as $value ) { ?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $recipients['egoi_reminders_time'] ); ?>><?php echo esc_html( $value ); ?>h</option>
									<?php } ?>
								</select>

								<hr>
								<p class="label_text"><?php esc_html_e( 'Choose the amount of time to send the email reminder.', 'smart-marketing-addon-sms-order' ); ?></p>

								<select name="egoi_email_reminders_time" id="egoi_email_reminders_time" class="e-goi-option-select-admin-forms" style="width: 49%;">
									<?php $recipients['egoi_reminders_time'] = empty( $recipients['egoi_email_reminders_time'] ) ? 48 : $recipients['egoi_email_reminders_time']; ?>
									<?php foreach ( $reminder_times as $value ) { ?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $recipients['egoi_email_reminders_time'] ); ?>><?php echo esc_html( $value ); ?>h</option>
									<?php } ?>
								</select>


								<?php submit_button(); ?>
							</form>
						</td>
						<td valign="top">
							<div id="egoi-account-info" style="background-color: #ffffff; padding: 1px 20px 15px 20px; border: 1px solid #dddddd">
								<p class="label_text_mini" style="font-size: 16px;">
									<?php esc_html_e( 'E-goi account information', 'smart-marketing-addon-sms-order' ); ?>
								</p>
								<p class="label_text_mini">
									<?php esc_html_e( 'Current balance of your E-goi account', 'smart-marketing-addon-sms-order' ) . ': '; ?>
									<span style="background-color: #00aeda; color: white; padding: 5px; border-radius: 2px; margin-left: 5px; font-size: 14px;"><?php echo esc_html( $balance ); ?></span>
								</p>
							</div>
							<div id="test-sms">
								<form action="#" method="post">
									<?php
									if ( $sender_option['sender_hash'] ) {
										$disabled = null;
									} else {
										$disabled = array( 'disabled' => 1 );
									}
									?>
									<?php wp_nonce_field( 'form-sms-order-tests' ); ?>
									<input name="form_id" type="hidden" value="form-sms-order-tests" />

									<p class="label_text">
										<?php esc_html_e( 'Send a test SMS', 'smart-marketing-addon-sms-order' ); ?>
									</p>
									<p class="label_text_mini">
										<?php esc_html_e( 'Send a test SMS to verify that your service is active. This test will have the cost of a normal SMS. You need to have balance on your E-goi account to perform this test. ', 'smart-marketing-addon-sms-order' ); ?>
									</p>

									<select name="recipient_prefix" class="e-goi-option-select-admin-forms" style="width: 49%; float: left;" required <?php echo $disabled ? 'disabled' : null; ?> >
										<?php
										foreach ( unserialize( EFWP_COUNTRY_CODES ) as $key => $value ) {
											$string = ucwords( strtolower( $value['country_pt'] ) ) . ' (+' . $value['prefix'] . ')';
											?>
											<option value="<?php echo esc_attr( $value['prefix'] ); ?>" ><?php echo esc_html( $string ); ?></option>
											<?php
										}
										?>
									</select>
									<input type="text" name="recipient_phone" class="regular-text" style="width: 50%; height: 38px;" required
										<?php echo ! empty( $disabled ) ? 'disabled' : null; ?>
									/>

									<br>

									<textarea name="message" style="width: 100%;" rows="5" required <?php echo ! empty( $disabled ) ? 'disabled' : null; ?>></textarea>

									<button type="submit" class="button send-sms-button"><?php esc_html_e( 'Send SMS', 'smart-marketing-addon-sms-order' ); ?></button>
								</form>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>



<!-- wrap SMS Texts -->
<div class="wrap tab wrap-addon" id="tab-sms-texts">
	<div class="wrap egoi4wp-settings">
		<div class="row">

			<?php
			if ( isset( $_POST['form_id'] ) && $_POST['form_id'] == 'form-sms-order-texts' ) {
				if ( $result ) {
					$this->helper->smsonw_admin_notice_success();
				} else {
					$this->helper->smsonw_admin_notice_error();
				}
			}
			?>

			<div class="main-content col col-12" style="margin:0 0 20px;">

				<p class="label_text"><?php esc_html_e( 'Select the language', 'smart-marketing-addon-sms-order' ); ?></p>

				<form action="#" method="post" class="form-sms-order-config" id="form-sms-order-texts">
					<?php wp_nonce_field( 'form-sms-order-texts' ); ?>
					<input name="form_id" type="hidden" value="form-sms-order-texts" />
					<div id="sms_texts_select_lang">
						<select class="e-goi-option-select-admin-forms" style="width: 400px;" name="sms_text_language" id="sms_text_language">
							<option value="" disabled selected>
								<?php esc_html_e( 'Selected the language', 'smart-marketing-addon-sms-order' ); ?>
							</option>
							<?php foreach ( $this->helper->smsonw_get_languages() as $code => $language ) { ?>
								<option value="<?php echo esc_attr( $code ); ?>" <?php ! empty( $_POST['sms_text_language'] ) ? selected( sanitize_text_field( wp_unslash( $_POST['sms_text_language'] ) ), $code ) : ''; ?>>
									<?php echo esc_html( $language ); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div id="sms_texts_tags">
						<p class="label_text" style="margin-bottom: 20px;">
							<?php esc_html_e( 'You can edit the SMS messages of each order status', 'smart-marketing-addon-sms-order' ); ?>
							<br>
							<span style="font-size: 13px;"><?php esc_html_e( 'If you want to include custom information in your SMS, use the following tags', 'smart-marketing-addon-sms-order' ); ?></span>
						</p>
						<?php foreach ( $this->helper->sms_text_tags as $tag_name => $tag_cod ) { ?>
							<button type="button" class="button button-default sms_texts_tags_button" data-text-cod="<?php echo esc_attr( $tag_cod ); ?>">
								<?php echo esc_html( ucwords( str_replace( '_', ' ', $tag_name ) ) ); ?>
							</button>
						<?php } ?>
					</div>

					<br>

					<?php foreach ( $this->helper->smsonw_get_languages() as $code => $lang ) { ?>
						<div id="sms_order_texts_<?php echo esc_attr( $code ); ?>">
							<table border="0" class="widefat striped" style="max-width: 900px;">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Order Status', 'smart-marketing-addon-sms-order' ); ?></th>
										<th><?php esc_html_e( 'Customer', 'smart-marketing-addon-sms-order' ); ?></th>
										<th><?php esc_html_e( 'Admin', 'smart-marketing-addon-sms-order' ); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ( $this->helper->smsonw_get_order_statuses() as $cod => $name ) { ?>
									<tr>
										<td><?php esc_html_e( $name, 'smart-marketing-addon-sms-order' ); ?></td>
										<td>
											<?php
											$text = '';
											$textAdmin = '';
											if ( isset( $texts[ $code ][ 'egoi_sms_order_text_customer_' . $cod ] ) && trim( $texts[ $code ][ 'egoi_sms_order_text_customer_' . $cod ] ) != '' ) {
												$text = trim($texts[ $code ][ 'egoi_sms_order_text_customer_' . $cod ]);
											} else {
												$text = trim($this->helper->sms_text_new_status[ $code ][ 'egoi_sms_order_text_customer_' . $cod ]);
											}
											if (isset($texts[$code]['egoi_sms_order_text_admin_' . $cod]) && $texts[$code]['egoi_sms_order_text_admin_' . $cod] !== null) {
												$textAdmin =  trim($texts[$code]['egoi_sms_order_text_admin_' . $cod]);
											}
											?>
											<textarea name="egoi_sms_order_text_customer_<?php echo esc_attr( $cod ); ?>" cols="40" rows="4" id="egoi_sms_order_text_customer_<?php echo esc_attr( $cod ); ?>"><?php echo esc_html( $text ); ?></textarea>
										</td>
										<td>
											<textarea name="egoi_sms_order_text_admin_<?php echo esc_attr( $cod ); ?>" cols="40" rows="4" id="egoi_sms_order_text_admin_<?php echo esc_attr( $cod ); ?>"><?php echo esc_html( $textAdmin ); ?></textarea>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
							<?php submit_button(); ?>
						</div>
					<?php } ?>
				</form>
			</div>
		</div>
	</div>
</div>




<!-- wrap SMS Payment Info Texts -->
<div class="wrap tab wrap-addon" id="tab-sms-payment-texts">
	<div class="wrap egoi4wp-settings">
		<div class="row">

			<?php
			if ( isset( $_POST['form_id'] ) && sanitize_key( $_POST['form_id'] ) == 'form-sms-order-payment-texts' ) {
				if ( $result ) {
					$this->helper->smsonw_admin_notice_success();
				} else {
					$this->helper->smsonw_admin_notice_error();
				}
			}
			?>

			<div class="main-content col col-12" style="margin:0 0 20px;">

				<p class="label_text"><?php esc_html_e( 'This plugin is integrated with Multibanco (euPago, Ifthenpay, easypay, Hipay Compra Facil, Sibs, LusoPay), WooCommerce PagSeguro', 'smart-marketing-addon-sms-order' ); ?></p>

				<p class="label_text"><?php esc_html_e( 'Select payment method', 'smart-marketing-addon-sms-order' ); ?></p>

				<form action="#" method="post" class="form-sms-order-config" id="form-sms-order-payment-texts">
					<?php wp_nonce_field( 'form-sms-order-payment-texts' ); ?>
					<input name="form_id" type="hidden" value="form-sms-order-payment-texts" />
					<div id="sms_select_payment_method">
						<select class="e-goi-option-select-admin-forms" style="width: 400px;" name="sms_payment_method" id="sms_payment_method">
							<option value="" disabled selected>
								<?php esc_html_e( 'Seclect payment method', 'smart-marketing-addon-sms-order' ); ?>
							</option>
							<?php foreach ( $this->helper->smsonw_get_payment_methods() as $code => $method ) { ?>
								<option value="<?php echo esc_attr( $code ); ?>" <?php ! empty( $_POST['sms_payment_method'] ) ? selected( sanitize_text_field( wp_unslash( $_POST['sms_payment_method'] ) ), $code ) : ''; ?>>
									<?php echo esc_html( $method ); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div id="sms_payment_texts_tags">
						<p class="label_text" style="margin-bottom: 20px;">
							<?php esc_html_e( 'You can edit the SMS messages sent by each payment method', 'smart-marketing-addon-sms-order' ); ?>
							<br>
							<span style="font-size: 13px;"><?php esc_html_e( 'If you want to include custom information in your SMS, use the following tags', 'smart-marketing-addon-sms-order' ); ?></span>
						</p>
						<?php foreach ( $this->helper->sms_text_tags as $tag_name => $tag_cod ) { ?>
							<button type="button" class="button button-default sms_texts_tags_button" data-text-cod="<?php echo esc_attr( $tag_cod ); ?>">
								<?php echo esc_html( ucwords( str_replace( '_', ' ', $tag_name ) ) ); ?>
							</button>
						<?php } ?>
					</div>
					<br>

					<?php foreach ( $this->helper->smsonw_get_payment_methods() as $method_code => $method ) { ?>
					<div id="sms_order_payment_texts_<?php echo esc_attr( $method_code ); ?>">
						<table border="0" class="widefat striped" style="max-width: 900px;">
							<thead>
							<tr>
								<th><?php esc_html_e( 'Language', 'smart-marketing-addon-sms-order' ); ?></th>
								<th><?php esc_html_e( 'First Message', 'smart-marketing-addon-sms-order' ); ?></th>
								<th><?php esc_html_e( 'Reminder', 'smart-marketing-addon-sms-order' ); ?></th>
							</tr>
							</thead>
							<tbody>

							<?php foreach ( $this->helper->smsonw_get_languages() as $lang_code => $lang ) { ?>
								<tr>
									<td><?php esc_html_e( $lang, 'smart-marketing-addon-sms-order' ); ?></td>
									<td>
										<?php
										$text = '';
										if (isset($payment_texts[$method_code]['egoi_sms_order_payment_text_' . $lang_code]) && trim($payment_texts[$method_code]['egoi_sms_order_payment_text_' . $lang_code]) != '') {
											$text = $payment_texts[$method_code]['egoi_sms_order_payment_text_' . $lang_code];
										} elseif (isset($this->helper->sms_payment_info[$method_code]['first'][$lang_code])) {
											$text = $this->helper->sms_payment_info[$method_code]['first'][$lang_code];
										}
										?>
										<textarea name="egoi_sms_order_payment_text_<?php echo esc_attr( $lang_code ); ?>" cols="40" rows="4" id="egoi_sms_order_payment_text_<?php echo esc_attr( $lang_code ); ?>"><?php echo esc_html( $text ); ?></textarea>
									</td>
									<td>
										<?php
										$text = '';
										if ( isset( $payment_texts[ $method_code ][ 'egoi_sms_order_reminder_text_' . $lang_code ] ) && trim( $payment_texts[ $method_code ][ 'egoi_sms_order_reminder_text_' . $lang_code ] ) != '' ) {
											$text = $payment_texts[ $method_code ][ 'egoi_sms_order_reminder_text_' . $lang_code ];
										} elseif( isset( $this->helper->sms_payment_info[ $method_code ]['reminder'][ $lang_code ] ) ) {
											$text = $this->helper->sms_payment_info[ $method_code ]['reminder'][ $lang_code ];
										}
										?>
										<textarea name="egoi_sms_order_reminder_text_<?php echo esc_attr( $lang_code ); ?>" cols="40" rows="4" id="egoi_sms_order_reminder_text_<?php echo esc_attr( $lang_code ); ?>"><?php echo esc_html( $text ); ?></textarea>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						<?php submit_button(); ?>
					</div>
					<?php } ?>

				</form>
			</div>
		</div>
	</div>
</div>


<!-- wrap Email Payment Info Texts -->
<div class="wrap tab wrap-addon" id="tab-sms-emailpayment-texts">
	<div class="wrap egoi4wp-settings">
		<div class="row">
			<?php
			if ( isset( $_POST['form_id'] ) && sanitize_key( $_POST['form_id'] ) == 'form-email-order-payment-texts' ) {
				if ( $result ) {
					$this->helper->smsonw_admin_notice_success();
				} else {
					$this->helper->smsonw_admin_notice_error();
				}
			}
			?>
			<div class="main-content col col-12" style="margin:0 0 20px;">

				<p class="label_text"><?php esc_html_e( 'This plugin is integrated with Multibanco (euPago, Ifthenpay, easypay, Hipay Compra Facil, Sibs, LusoPay), WooCommerce PagSeguro', 'smart-marketing-addon-sms-order' ); ?></p>

				<p class="label_text"><?php esc_html_e( 'Select payment method', 'smart-marketing-addon-sms-order' ); ?></p>

				<form action="#" method="post" class="form-sms-order-config" id="form-email-order-payment-texts">
					<?php wp_nonce_field( 'form-email-order-payment-texts' ); ?>
					<input name="form_id" type="hidden" value="form-email-order-payment-texts" />
					<div id="sms_select_payment_method">
						<select class="e-goi-option-select-admin-forms" style="width: 400px;" name="email_payment_method" id="email_payment_method">
							<option value="" disabled selected>
								<?php esc_html_e( 'Seclect payment method', 'smart-marketing-addon-sms-order' ); ?>
							</option>
							<?php foreach ( $this->helper->smsonw_get_payment_methods() as $code => $method_email ) { ?>
								<option value="<?php echo esc_attr( $code ); ?>" <?php ! empty( $_POST['sms_payment_method'] ) ? selected( sanitize_text_field( wp_unslash( $_POST['sms_payment_method'] ) ), $code ) : ''; ?>>
									<?php echo esc_html( $method_email ); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div id="email_payment_texts_tags" >
						<p class="label_text" style="margin-bottom: 20px;">
							<?php esc_html_e( 'You can edit the Email message sent by each payment method', 'smart-marketing-addon-sms-order' ); ?>
							<br>
							<span style="font-size: 13px;"><?php esc_html_e( 'If you want to include custom information in your Email, use the following tags', 'smart-marketing-addon-sms-order' ); ?></span>
						</p>
						<?php foreach ( $this->helper->email_text_tags as $tag_name_email => $tag_cod ) { ?>
							<button type="button" class="button button-default sms_texts_tags_button" data-text-cod="<?php echo esc_attr( $tag_cod ); ?>">
								<?php echo esc_html( ucwords( str_replace( '_', ' ', $tag_name_email ) ) ); ?>
							</button>
						<?php } ?>
					</div>
					<br>

					<?php foreach ( $this->helper->smsonw_get_payment_methods() as $method_code_email => $method_email ) { ?>
					<div id="email_order_payment_texts_<?php echo esc_attr( $method_code_email ); ?>">
						<table border="0" class="widefat striped" style="max-width: 900px;">
							<thead>
							<tr>
								<th><?php esc_html_e( 'Language', 'smart-marketing-addon-sms-order' ); ?></th>
								<th><?php esc_html_e( 'Reminder', 'smart-marketing-addon-sms-order' ); ?></th>
							</tr>
							</thead>
							<tbody>

							<?php foreach ( $this->helper->smsonw_get_languages() as $lang_code_email => $lang ) { ?>
								<tr>
									<td><?php esc_html_e( $lang, 'smart-marketing-addon-sms-order' ); ?></td>
									<td>
										<?php
										$text = '';
										if ( isset( $payment_email_texts[ $method_code_email ][ 'egoi_sms_order_reminder_email_text_' . $lang_code_email ] ) && trim( $payment_email_texts[ $method_code_email ][ 'egoi_sms_order_reminder_email_text_' . $lang_code_email ] ) != '' ) {
											$text = $payment_email_texts[ $method_code_email ][ 'egoi_sms_order_reminder_email_text_' . $lang_code_email ];
										} elseif ( isset( $this->helper->email_payment_info[ $method_code_email ]['reminder'][ $lang_code_email ] ) ) {
											$text = $this->helper->email_payment_info[ $method_code_email ]['reminder'][ $lang_code_email ];
										}
										?>
										<textarea name="egoi_sms_order_reminder_email_text_<?php echo esc_attr( $lang_code_email ); ?>" cols="40" rows="8" id="egoi_sms_order_reminder_email_text_<?php echo esc_attr( $lang_code_email ); ?>" style="width: 720px;"><?php echo esc_html( $text ); ?></textarea>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						<?php submit_button(); ?>
					</div>
					<?php } ?>

				</form>

			</div>
		
		</div>
	</div>
</div>


<!-- wrap SMS FOLLOW PRICES -->
<div class="wrap tab wrap-addon" id="tab-sms-follow-price">
	<div class="wrap egoi4wp-settings" id="tab-forms">
		<?php
		if ( isset( $_POST['form_id'] ) && sanitize_key( $_POST['form_id'] ) == 'form-sms-follow-price' ) {
			if ( $result ) {
				$this->helper->smsonw_admin_notice_success();
			} else {
				$this->helper->smsonw_admin_notice_error();
			}
		}
		?>
		<div class="main-content col col-12" style="margin:0 0 20px;">
			<p class="label_text"><?php esc_html_e( 'Use this plugin to send Follow price SMS.', 'smart-marketing-addon-sms-order' ); ?></p>
			<br>
			<form action="#" method="post" class="form-sms-follow-price" id="form-sms-follow-price">
				<?php wp_nonce_field( 'form-sms-follow-price' ); ?>
				<input name="form_id" type="hidden" value="form-sms-follow-price" />

				<div id="sms_follow_price">
					<table border="0" class="widefat striped" style="max-width: 900px;">
						 <thead>
							<tr>
								<th><?php esc_html_e( 'Configurations', 'smart-marketing-addon-sms-order' ); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><span><?php esc_html_e( 'Positions', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<select name="follow_price_position" id="egoi_sms_follow_price_position" style="min-width: 400px;width: 100%;">
										<?php
										foreach ( $this->helper->smsonw_admin_follow_price_positions() as $k => $v ) {
											?>
											<option <?php ! empty( $follow_price['follow_price_position'] ) ? selected( $follow_price['follow_price_position'], $k ) : ''; ?>  value="<?php echo esc_attr( $k ); ?>" >
												<?php esc_html_e( $v, 'smart-marketing-addon-sms-order' ); ?>
											</option>
											<?php
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td><span><?php esc_html_e( 'Message', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<textarea name="follow_price_message" id="egoi_sms_follow_price_message" style="min-width: 400px;width: 100%;"><?php echo ( ! empty( $follow_price['follow_price_message'] ) && trim( $follow_price['follow_price_message'] ) != '' ) ? esc_html( $follow_price['follow_price_message'] ) : '';?></textarea>
									<p>
										<?php esc_html_e( 'Use %link% to choose the position of the link otherwise the link will be placed at the end', 'smart-marketing-addon-sms-order' ); ?><br>
										<?php esc_html_e( 'Use %shop_name% for shop name display.', 'smart-marketing-addon-sms-order' ); ?><br>
										<?php esc_html_e( 'Use %product%for product name.', 'smart-marketing-addon-sms-order' ); ?>
									</p>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Title Pop', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="text" id="follow_title_pop" name="follow_title_pop" style="width: 100%;"
											   value="<?php
												echo ( isset( $follow_price['follow_title_pop'] ) ) ? esc_attr( $follow_price['follow_title_pop'] ) : '';
												?>"
										>
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Text on button', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="text" id="follow_price_button_name" name="follow_price_button_name" style="width: 100%;"
											   value="<?php
												echo ( isset( $follow_price['follow_price_button_name'] ) ) ? esc_attr( $follow_price['follow_price_button_name'] ) : '';
												?>"
										>
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Enabled', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
									  <input type="checkbox" id="follow_price_enable" name="follow_price_enable"
									   <?php
									   if (isset($follow_price['follow_price_enable'])) {
											checked( $follow_price['follow_price_enable'], 'on' );
									   }
										?>
									  >
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Shortener', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="checkbox" id="follow_price_shortener" name="follow_price_shortener"
											<?php
											if ( isset( $follow_price['follow_price_shortener'] )){
												checked( $follow_price['follow_price_shortener'], 'on' );
											}
											?>
										>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<b><?php esc_html_e( 'Styles for dialog', 'smart-marketing-addon-sms-order' ); ?></b>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Background Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $follow_price['follow_background_color'] ); ?>" class="view" ></div>
												<input id="follow_background_color" type="text" name="follow_background_color" value="<?php echo esc_attr( $follow_price['follow_background_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Text Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $follow_price['follow_text_color'] ); ?>" class="view" ></div>
												<input id="follow_text_color" type="text" name="follow_text_color" value="<?php echo esc_attr( $follow_price['follow_text_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Button Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $follow_price['follow_button_color'] ); ?>" class="view" ></div>
												<input id="follow_button_color" type="text" name="follow_button_color" value="<?php echo esc_attr( $follow_price['follow_button_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Button Text Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $follow_price['follow_button_text_color'] ); ?>" class="view" ></div>
												<input id="follow_button_text_color" type="text" name="follow_button_text_color" value="<?php echo esc_attr( $follow_price['follow_button_text_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>


						</tbody>
					</table>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>
</div>


<!-- wrap SMS Help -->
<div class="wrap tab wrap-addon" id="tab-sms-help">
	<div class="wrap egoi4wp-settings" id="tab-forms">
		<div class="row">
			<div class="main-content col col-12" style="margin:0 0 20px;">

				<p class="help-title"><?php esc_html_e( 'First steps', 'smart-marketing-addon-sms-order' ); ?></p>
				<p><?php esc_html_e( 'Help information to start sending SMS notifications to your customers.', 'smart-marketing-addon-sms-order' ); ?></p>
				<br>

				<p class="help-title"><?php esc_html_e( '1. Transactional Activation', 'smart-marketing-addon-sms-order' ); ?></p>
				<p><?php esc_html_e( 'In order to send SMS notifications, you must first activate Transactional in your E-goi account.', 'smart-marketing-addon-sms-order' ); ?>
					<br>
					<?php esc_html_e( 'To activate Transactional in your E-goi account, go to the Apps menu and choose to activate Slingshot.', 'smart-marketing-addon-sms-order' ); ?>
					<a href="<?php esc_attr_e( 'https://helpdesk.e-goi.com/index.php?type=page&urlcode=708772&title=Sending-transactional-messages-with-E-gois-Slingshot', 'smart-marketing-addon-sms-order' ); ?>" target="_blank">
						<?php esc_html_e( 'See more help', 'smart-marketing-addon-sms-order' ); ?>
					</a>
				</p>
				<br>

				<p class="help-title"><?php esc_html_e( '2. Adding a sender', 'smart-marketing-addon-sms-order' ); ?></p>
				<p>
					<?php esc_html_e( 'In your E-goi account you must add a Sender to send sms,', 'smart-marketing-addon-sms-order' ); ?>
					<a href="<?php esc_attr_e( 'https://helpdesk.e-goi.com/index.php?type=page&urlcode=694373&title=Adding-a-sender', 'smart-marketing-addon-sms-order' ); ?>" target="_blank">
						<?php esc_html_e( 'See how!', 'smart-marketing-addon-sms-order' ); ?>
					</a>
					<br>
					<?php
						esc_html_e(
							'(You can add a sender with the number that will appear in the sms sent and allows you to receive replies or text,
                            such as the name of your company or website, 
                            but not allowing you to receive replies)',
							'smart-marketing-addon-sms-order'
						);
						?>
				</p>
				<br>

				<p class="help-title"><?php esc_html_e( '3. Add balance', 'smart-marketing-addon-sms-order' ); ?></p>
				<p>
					<?php
						esc_html_e(
							'All free E-goi Plans have included 10 SMS and all Starter Plans have 50 included SMS that you can use to test the plugin. If you do not have enough balance available, log in to your E-goi Account, go to the menu related to your account (top, right) 
                            and the Balance information choose Top Up.',
							'smart-marketing-addon-sms-order'
						);
						?>
				</p>
				<br>

				<p class="help-title"><?php esc_html_e( '4. Select SMS Sender E-goi', 'smart-marketing-addon-sms-order' ); ?></p>
				<p>
					<?php
						esc_html_e(
							'If you have already done the above steps, you can go back to the SMS plugin to select the E-goi SMS 
                            Sender previously entered into the E-goi account and complete the setup process. 
                            This sender will be the one used to send your SMS notifications.',
							'smart-marketing-addon-sms-order'
						);
						?>
				</p>
				<br>

				<p class="help-title"><?php esc_html_e( 'SMS Tracking', 'smart-marketing-addon-sms-order' ); ?></p>
				<p>
					<?php
					esc_html_e( 'To send SMS with the shipping info, you must configure the carriers you want to use and their tracking or website URLs. You need to include the customization codes (tracking_code, tracking_url, tracking_name) in the order statuses and messages you want to send. The plugin will add a widget on the order page where you can choose which carrier will be used for the shipment and its tracking code. This data will be used in SMS.', 'smart-marketing-addon-sms-order' );
					?>
				</p>
				<br>

				<p class="help-title"><?php esc_html_e( 'More information', 'smart-marketing-addon-sms-order' ); ?></p>
				<p>
					<?php
					esc_html_e(
						'The option of sending SMS notification with the Multibanco (Entity, Reference, Value) 
                            for instant cash payment is only compatible with Eupago, 
                            Ifthenpay and easypay portuguese payment gateways.',
						'smart-marketing-addon-sms-order'
					);
					?>
					<br>
					<?php
					esc_html_e(
						'Option of sending SMS with the data of Pagseguro is only 
                            compatible with the plugin WooCommerce PagSeguro',
						'smart-marketing-addon-sms-order'
					);
					?>
					<br>
					<?php
					esc_html_e(
						'The balance amount displayed in the plugin may temporarily show differences, 
                            relative to the balance within your E-goi account.',
						'smart-marketing-addon-sms-order'
					);
					?>
				</p>
				<p>
					<?php esc_html_e( 'You can consult the article of our blog about this plugin.', 'smart-marketing-addon-sms-order' ); ?>
					<a href="<?php esc_attr_e( 'https://blog.e-goi.com/alerts-by-sms-for-your-ecommerce-orders/', 'smart-marketing-addon-sms-order' ); ?>" target="_blank">
						<?php esc_html_e( 'View blog post', 'smart-marketing-addon-sms-order' ); ?>
					</a>
					<br>
					<?php esc_html_e( 'You can check at this link the cost of each sms and E-goi account plans.', 'smart-marketing-addon-sms-order' ); ?>
					<a href="<?php esc_attr_e( 'https://www.e-goi.com/pricing/', 'smart-marketing-addon-sms-order' ); ?>" target="_blank">
						<?php esc_html_e( 'Check Rates', 'smart-marketing-addon-sms-order' ); ?>
					</a>
				</p>


			</div>
		</div>
	</div>
</div>


<!-- wrap SMS Payment Info Texts -->
<div class="wrap tab wrap-addon" id="tab-sms-tracking-texts">
	<div class="wrap egoi4wp-settings" id="tab-forms">
		<div class="row">

			<div id="tracking_texts_message">
				<?php
				if ( isset( $_POST['form_id'] ) && sanitize_key( $_POST['form_id'] ) == 'form-sms-order-tracking-texts' ) {
					if ( $result ) {
						$this->helper->smsonw_admin_notice_success();
					} else {
						$this->helper->smsonw_admin_notice_error();
					}
				}
				?>
			</div>

			<div class="main-content col col-12" style="margin:0 0 20px;">

				<p class="label_text"><?php esc_html_e( 'Use this plugin to send SMS with information about shipping orders. You can use the carriers already configured in your WooCommerce or insert the ones you want.', 'smart-marketing-addon-sms-order' ); ?></p>

				<p class="label_text"><?php esc_html_e( 'Carriers already configured in your WooCommerce. Add tracking url or website.', 'smart-marketing-addon-sms-order' ); ?></p>
				<br/>
				<form action="#" method="post" class="form-sms-order-config" id="form-sms-order-tracking-texts">
					<?php wp_nonce_field( 'form-sms-order-tracking-texts' ); ?>
					<input name="form_id" type="hidden" value="form-sms-order-tracking-texts" />

					<div id="sms_order_tracking_urls">
						<table border="0" class="widefat striped" style="max-width: 900px;">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Carriers Name', 'smart-marketing-addon-sms-order' ); ?></th>
									<th><?php esc_html_e( 'Carriers URL', 'smart-marketing-addon-sms-order' ); ?>&nbsp;&nbsp;<a href="#" class="egoi_tooltip" id="help_carrier_url"><i class="qtip tip-right" data-tip=" <?php esc_attr_e( 'This url will be replacing the %tracking_url% shortcode.', 'smart-marketing-addon-sms-order' ); ?>">?</i></a></th>

								</tr>
							</thead>
							<tbody>

							<?php
							$urls = $this->helper->smsonw_get_tracking_carriers_urls();
							?>

							<?php foreach ( $this->helper->smsonw_get_tracking_carriers() as $tracking_code_name => $tracking_name ) { ?>
								<tr>
									<td><span><?php echo esc_html( $tracking_name ); ?></span></td>
									<td>
										<input name="<?php echo esc_html( $tracking_code_name ); ?>" id="egoi_sms_order_tracking_<?php echo esc_attr( $tracking_code_name ); ?>" style="min-width: 400px;width: 100%;" value="<?php echo isset( $urls[ $tracking_code_name ] ) ? esc_attr( $urls[ $tracking_code_name ] ) : ''; ?>"/>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						<br/>
						<p class="label_text"><?php esc_html_e( 'Need more Carriers? Please add it here. Insert tracking url or website.', 'smart-marketing-addon-sms-order' ); ?></p>
						<br/>
						<div class="" id="egoi_custom_carrier">
							<table border="0" class="widefat striped" style="max-width: 900px;">
								<thead>
								<tr>
									<th><?php esc_html_e( 'Carriers Name', 'smart-marketing-addon-sms-order' ); ?></th>
									<th><?php esc_html_e( 'Carriers URL', 'smart-marketing-addon-sms-order' ); ?>&nbsp;&nbsp;<a href="#" class="egoi_tooltip" id="help_carrier_url"><i class="qtip tip-right" data-tip=" <?php esc_attr_e( 'This url will be replacing the %tracking_url% shortcode.', 'smart-marketing-addon-sms-order' ); ?>">?</i></a></th>
									<th></th>
								</tr>
								</thead>
								<tbody id = "custom_carriers_rows" >
								<?php
									$custom_carriers = $this->helper->smsonw_get_custom_tracking_carriers();
								?>
								<tr>
									<td>
										<input name="add_egoi_sms_order_tracking_name" id="add_egoi_sms_order_tracking_name" style="width: 100%;" value=""/>
									</td>
									<td>
										<input name="add_egoi_sms_order_tracking_url" id="add_egoi_sms_order_tracking_url" style="min-width: 400px;width: 100%;" value=""/>
									</td>
									<td>
										<div class="button" id="add_egoi_sms_order_tracking_button">+</div>
									</td>
								</tr>

								<?php foreach ( $custom_carriers as $carrier ) { ?>

									<tr id = "custom_carrier_<?php echo esc_attr( $carrier['carrier'] ); ?>">
										<td>
											<span style="width: 100%;" value=""><?php echo esc_html( $carrier['carrier'] ); ?></span>
										</td>
										<td>
											<span style="min-width: 400px;width: 100%;" value=""><?php echo esc_attr( $carrier['url'] ); ?></span>
										</td>
										<td>
											<div class="button remove_carrier remove-button" id="remove_custom_carrier_<?php echo esc_attr( $carrier['carrier'] ); ?>">x</div>
										</td>
									</tr>

								<?php } ?>
								</tbody>
							</table>
						</div>

						<?php submit_button(); ?>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>

<?php require_once plugin_dir_path( __FILE__ ) . 'smart-marketing-addon-sms-order-admin-config-abandonned-cart.php'; ?>
