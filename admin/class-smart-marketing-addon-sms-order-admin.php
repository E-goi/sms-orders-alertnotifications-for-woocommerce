<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.e-goi.com
 * @since      1.0.0
 *
 * @package    Smart_Marketing_Addon_Sms_Order
 * @subpackage Smart_Marketing_Addon_Sms_Order/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smart_Marketing_Addon_Sms_Order
 * @subpackage Smart_Marketing_Addon_Sms_Order/admin
 * @author     E-goi <egoi@egoi.com>
 */
class Smart_Marketing_Addon_Sms_Order_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The ID of parent of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $parent_plugin_name    The ID of parent of this plugin.
     */
    private $parent_plugin_name = 'egoi-for-wp';

    private $apikey;

    protected $helper;

    protected $egoi_api_client;

	/**
	 * @var array List of sms languages
	 */
    protected $languages = array( "en", "es", "pt", "pt_BR");

	/**
	 * @var array List of SMS text tags
	 */
    protected $sms_text_tags = array(
        "order_id" => '%order_id%',
        "order_status" => '%order_status%',
        "total" => '%total%',
        "currency" => '%currency%',
        "payment_method" => '%payment_method%',
        "reference" => '%ref%',
        "entity" => '%ent%',
        "shop_name" => '%shop_name%',
        "billing_name" => '%billing_name%'
    );

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

	    $this->helper = new Smart_Marketing_Addon_Sms_Order_Helper();

        $this->egoi_api_client = new SoapClient('http://api.e-goi.com/v2/soap.php?wsdl');

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$apikey = get_option('egoi_api_key');
		$this->apikey = $apikey['api_key'];

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smart-marketing-addon-sms-order-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smart-marketing-addon-sms-order-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'js/order_action_sms_meta_box.js', array('jquery') );
		wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

    /**
     * Add an options page to smart marketing menu
     *
     * @since  1.0.0
     */
    public function add_options_page() {
        $this->plugin_screen_hook_suffix = add_submenu_page(
            $this->parent_plugin_name,
            __( 'SMS Notifications', 'smart-marketing-addon-sms-order' ),
            __( 'SMS Notifications', 'smart-marketing-addon-sms-order' ),
            'manage_options',
            'smart-marketing-addon-sms-order-config',
            array( $this, 'display_plugin_sms_order_config' )
        );
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_plugin_sms_order_config() {
        include_once 'partials/smart-marketing-addon-sms-order-admin-config.php';
    }

	/**
	 * Save sms order configs in wordpress options
	 *
	 * @param $post
	 *
	 * @return bool
	 */
    public function process_config_form($post) {

        try {
            unset($post['submit']);

            if (isset($post['form_id']) && $post['form_id'] == 'form-sms-order-senders') {

                $sender_atributes = array ('sender_hash', 'admin_prefix', 'admin_phone');

	            foreach ($post as $name => $input) {
		            if (in_array($name, $sender_atributes)) {
		                $sender[$name] = $input;
                    } else {
		                $recipients[$name] = $input;
                    }
                }

                if (!isset($post['egoi_payment_info'])) {
                    $recipients['egoi_payment_info'] = 0;
                }

                if (!isset($post['egoi_reminders'])) {
                    $recipients['egoi_reminders'] = 0;
                }

                update_option('egoi_sms_order_sender', json_encode($sender));
                update_option('egoi_sms_order_recipients', json_encode($recipients));

            } else if (isset($post['form_id']) && $post['form_id'] == 'form-sms-order-texts') {

                $texts = json_decode(get_option('egoi_sms_order_texts'), true);
                $texts[$post['sms_text_language']] = $post;
                update_option('egoi_sms_order_texts', json_encode($texts));

            } else if (isset($post['form_id']) && $post['form_id'] == 'form-sms-order-tests') {

	            $recipient = $this->helper->get_valid_recipient($post['recipient_phone'], null, $post['recipient_prefix']);
                $response = $this->helper->send_sms($recipient, $post['message'], 'test', 0);

                $response = json_decode($response);
                if (isset($response->errorCode)) {
                    return false;
                }
            }
	        return true;
        } catch (Exception $e) {
            $this->helper->save_logs('process_config_form: ' . $e->getMessage());
        }

    }

	/**
	 * Process SMS reminders
	 */
    public function sms_order_reminder() {
        try {

            global $wpdb;

            $table_name = $wpdb->prefix. 'egoi_sms_order_reminders';

            $sql = " SELECT DISTINCT order_id FROM $table_name ";
            $order_ids = $wpdb->get_col($sql);

            $orders = $this->helper->get_not_paid_orders();

            if (isset($orders)) {

	            $sender = json_decode(get_option('egoi_sms_order_sender'), true);
	            $recipient_options = json_decode(get_option('egoi_sms_order_recipients'), true);
                $count = 0;

                foreach ($orders as $order) {

                    if ($count >= 20) {
                        break;
                    }

                    $order_data = $order->get_data();

                    if ($recipient_options['notification_option']) {
	                    $sms_notification = (bool) get_post_meta( $order->get_id(), 'egoi_notification_option' )[0];
                    } else {
                        $sms_notification = 1;
                    }

                    if (!$order->is_paid() && !in_array($order->get_id(), $order_ids) && $sms_notification && $recipient_options['egoi_reminders']  && array_key_exists($order_data['payment_method'], $this->helper->payment_map)) {

                        $message = __('Hello, we remind you that your order at', 'smart-marketing-addon-sms-order').' ';
                        $message .= '%shop_name% ';
                        $message .= __('is waiting for MB. Use', 'smart-marketing-addon-sms-order').' ';
                        $message .= $this->helper->get_payment_data($order_data, 'ent') ? 'Ent. %ent% ' : null;
                        $message .= $this->helper->get_payment_data($order_data, 'ref') ? 'Ref. %ref% ' : null;
                        $message .= $this->helper->get_payment_data($order_data, 'val') ? __('Value', 'smart-marketing-addon-sms-order').' %total%%currency% ' : null;
                        $message .= __('Thank you', 'smart-marketing-addon-sms-order');
                        $message = $this->helper->get_tags_content($order_data, $message);

                        $recipient = $this->helper->get_valid_recipient($order->billing_phone, $order->billing_country);
                        $this->helper->send_sms($recipient, $message, $order->get_status(), $order->get_id(), true);
                        $count++;

	                    $wpdb->insert($table_name, array(
		                    "time" => current_time('mysql'),
		                    "order_id" => $order->get_id()
	                    ));
                    }
                }
            }

        } catch (Exception $e) {
	        $this->helper->save_logs('sms_order_reminder: ' . $e->getMessage());
        }
    }

	/**
     * Send SMS when order status changed
     *
	 * @param $order_id
	 */
	public function order_send_sms_new_status($order_id) {

		$recipient_options = json_decode(get_option('egoi_sms_order_recipients'), true);

		if ($recipient_options['notification_option']) {
			$sms_notification = (bool) get_post_meta($order_id, 'egoi_notification_option')[0];
		} else {
			$sms_notification = 1;
		}

		if ($sms_notification) {

			$sender = json_decode(get_option('egoi_sms_order_sender'), true);
            $order = wc_get_order($order_id)->get_data();
            $types = array(
                'customer' => $order['billing']['phone'],
                'admin' => $sender['admin_prefix'].'-'.$sender['admin_phone']
            );
            foreach ($types as $type => $phone) {
                $message = $this->helper->get_sms_order_message($type, $order);
                if ($message !== false) {
                    $recipient = $type == 'customer' ? $this->helper->get_valid_recipient($phone, $order['billing']['country']) : $phone;
                    $this->helper->send_sms($recipient, $message, $order['status'], $order['id']);
                }
            }
		}
	}

	/**
     * Send SMS with payment instructions when order is closed
     *
	 * @param $order_id
	 */
    public function order_send_sms_payment_data($order_id) {

	    $recipient_options = json_decode(get_option('egoi_sms_order_recipients'), true);

	    $order = wc_get_order($order_id)->get_data();

	    if ($recipient_options['notification_option']) {
		    $sms_notification = (bool) get_post_meta($order_id, 'egoi_notification_option')[0];
	    } else {
		    $sms_notification = 1;
	    }

        if ($sms_notification && $recipient_options['egoi_payment_info'] && array_key_exists($order['payment_method'], $this->helper->payment_map)) {
            $message = __('Hello, your order at', 'smart-marketing-addon-sms-order').' ';
            $message .= '%shop_name% ';
            $message .= __('is waiting for MB payment. Use', 'smart-marketing-addon-sms-order').' ';
            $message .= $this->helper->get_payment_data($order, 'ent') ? 'Ent. %ent% ' : null;
            $message .= $this->helper->get_payment_data($order, 'ref') ? 'Ref. %ref% ' : null;
            $message .= $this->helper->get_payment_data($order, 'val') ? __('Value', 'smart-marketing-addon-sms-order').' %total%%currency% ' : null;
            $message .= __('Thank you', 'smart-marketing-addon-sms-order');
            $message = $this->helper->get_tags_content($order, $message);

            if (array_key_exists($order['payment_method'], $this->helper->payment_map)) {
	            $recipient = $this->helper->get_valid_recipient($order['billing']['phone'], $order['billing']['country']);
                $this->helper->send_sms($recipient, $message,'order', $order_id, true);
            }
        }

    }

	/**
	 * Add SMS meta box to order admin page
	 */
	public function order_add_sms_meta_box() {
		add_meta_box(
			'woocommerce-order-my-custom',
			__('Send SMS to buyer', 'smart-marketing-addon-sms-order'),
			array( $this, 'order_display_sms_meta_box' ),
			'shop_order',
			'side',
			'core'
		);
	}

	/**
	 * The meta box content
	 *
	 * @param $post
	 */
	public function order_display_sms_meta_box($post) {

		$recipient_options = json_decode(get_option('egoi_sms_order_recipients'), true);
		$order = wc_get_order($post->ID)->get_data();

		if ($recipient_options['notification_option']) {
			$sms_notification = (bool) get_post_meta($post->ID, 'egoi_notification_option')[0];
		} else {
			$sms_notification = 1;
		}

		if ($sms_notification) {
			$recipient = $order['billing']['phone'];
			?>
            <div id="egoi_send_order_sms">
                <input type="hidden" name="egoi_sms_order_id" id="egoi_send_order_sms_order_id"
                       value="<?php echo $order['id']; ?>"/>
                <input type="hidden" name="egoi_sms_order_country" id="egoi_send_order_sms_order_country"
                       value="<?php echo $order['billing']['country']; ?>"/>
                <input type="hidden" name="egoi_sms_recipient" id="egoi_send_order_sms_recipient"
                       value="<?= $recipient ?>"/>
                <p>
                    <label for="egoi_send_order_sms_message"><?php _e('Message', 'smart-marketing-addon-sms-order');?></label><br>
                    <textarea name="egoi_sms_message" id="egoi_send_order_sms_message" style="width: 100%;"></textarea>
                </p>
                <p>
                    <button type="button" class="button" id="egoi_send_order_sms_button"><?php _e('Send', 'smart-marketing-addon-sms-order'); ?></button>
                    <span id="egoi_send_order_sms_error" style="display: none; color: red;"><?php _e('You can\'t send a empty SMS', 'smart-marketing-addon-sms-order');?></span>
                    <span id="egoi_send_order_sms_notice" style="display: none;"><?php _e('Sending... Wait please', 'smart-marketing-addon-sms-order');?></span>
                </p>
            </div>
			<?php
		} else {
		    _e('The customer doesn\'t want to receive sms');
        }
	}

	/**
	 * Send SMS and add note to admin order page
	 */
	public function order_action_sms_meta_box() {
		$recipient = $this->helper->get_valid_recipient($_POST['recipient'], $_POST['country']);

		$result = json_decode($this->helper->send_sms($recipient, $_POST['message'], 'order', $_POST['order_id']));

		if (!isset($result->errorCode)) {
			$order = wc_get_order($_POST['order_id']);
			$order->add_order_note('SMS: '.$_POST['message']);

			$note = array(
				"message" => 'SMS: '.$_POST['message'],
				"date" => __('added on', 'smart-marketing-addon-sms-order').' '.current_time(get_option('date_format').' '.get_option('time_format'))
			);
			echo json_encode($note);
		} else {
			echo json_encode($result);
		}
		wp_die();
	}

	/**
	 * Add new interval to wordpress cron schedules
	 * @param $schedules
	 *
	 * @return mixed
	 */
	public function my_add_every_five_minutes($schedules) {
		$schedules['every_five_minutes'] = array(
			'interval' => 300,
			'display' => __('Every Five Minutes')
		);
		return $schedules;
	}

	public function get_order_statuses() {
        return array(
            "pending" => __("Pending payment", 'smart-marketing-addon-sms-order'),
            "failed" => __("Failed", 'smart-marketing-addon-sms-order'),
            "on-hold" => __("On Hold", 'smart-marketing-addon-sms-order'),
            "processing" => __("Processing", 'smart-marketing-addon-sms-order'),
            "completed" => __("Completed", 'smart-marketing-addon-sms-order'),
            "refunded" => __("Refunded", 'smart-marketing-addon-sms-order'),
            "cancelled" => __("Cancelled", 'smart-marketing-addon-sms-order'),
        );
    }

    public function woocommerce_dependency_notice(){
        if ( !class_exists( 'WooCommerce' ) ) {
            ?>
            <div class="notice notice-error is-dismissible">
            <p><?php _e('To use this plugin, you first need to install WooCommerce', 'smart-marketing-addon-sms-order'); ?></p>
            </div>
            <?php
        }
    }

}
