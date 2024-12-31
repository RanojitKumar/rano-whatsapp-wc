<?php
/*
Plugin Name: rano WhatsApp WC
Description: Adds a WhatsApp button to single WooCommerce product pages.
Version: 1.0
Author: Ranojit K.
Author URI: https://ranojit.com/
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Add settings menu
function rano_wc_add_settings_menu() {
    add_menu_page(
        'Rano WhatsApp WC Settings',
        'rano WhatsApp',
        'manage_options',
        'rano-whatsapp-settings',
        'rano_wc_render_settings_page',
        'dashicons-whatsapp',
        25
    );
}
add_action('admin_menu', 'rano_wc_add_settings_menu');

// Render settings page
function rano_wc_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['rano_wc_save_settings'])) {
        update_option('rano_wc_whatsapp_number', sanitize_text_field($_POST['rano_wc_whatsapp_number']));
        update_option('rano_wc_single_label', sanitize_text_field($_POST['rano_wc_single_label']));
        echo '<div class="updated"><p>Settings saved successfully!</p></div>';
    }

    // Get saved options or set default values
    $whatsapp_number = get_option('rano_wc_whatsapp_number', '8801xxxxxxxxx');
    $button_label = get_option('rano_wc_single_label', 'Ask on WhatsApp');

    ?>
    <div class="wrap">
        <h1>Rano WhatsApp WC Settings</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">WhatsApp Number</th>
                    <td>
                        <input type="text" name="rano_wc_whatsapp_number" value="<?php echo esc_attr($whatsapp_number); ?>" class="regular-text">
                        <p class="description">Enter your WhatsApp number (e.g., 8801xxxxxxxxx).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Button Label</th>
                    <td>
                        <input type="text" name="rano_wc_single_label" value="<?php echo esc_attr($button_label); ?>" class="regular-text">
                        <p class="description">Enter the label for the button (e.g., "Ask on WhatsApp").</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="rano_wc_save_settings" class="button-primary" value="Save Settings">
            </p>
        </form>
    </div>
    <?php
}

// Add WhatsApp button to single product pages
function rano_wc_add_to_single_product() {
    if (!is_product()) {
        return;
    }

    global $product;
    $product_url = urlencode(get_permalink($product->get_id()));
    $product_name = urlencode($product->get_name());
    $whatsapp_number = get_option('rano_wc_whatsapp_number', '8801xxxxxxxxx');
    $button_label = get_option('rano_wc_single_label', 'Ask on WhatsApp');
    $whatsapp_text = "Hi! I'm interested in the product: $product_name. Here is the link: $product_url";

    echo '<a href="https://wa.me/' . esc_attr($whatsapp_number) . '?text=' . esc_attr($whatsapp_text) . '" class="button rano-wc-product-button">
            <span class="dashicons dashicons-whatsapp"></span> ' . esc_html($button_label) . '
          </a>';
}
add_action('woocommerce_after_add_to_cart_button', 'rano_wc_add_to_single_product');

// Enqueue styles
function rano_wc_enqueue_styles() {
    wp_enqueue_style('rano-wc-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'rano_wc_enqueue_styles');
