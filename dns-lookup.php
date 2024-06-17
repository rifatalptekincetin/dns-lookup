<?php
/*
Plugin Name: DNS Lookup
Description: Perform DNS lookup for a domain using Ajax.
Version: 1.1
Author: ALPX
*/

// Enqueue necessary scripts and localize translations
function enqueue_dns_lookup_scripts() {
    wp_enqueue_script('dns-lookup-script', plugin_dir_url(__FILE__) . 'dns-lookup.js', array('jquery'), '1.0', true);

    // Localization for JavaScript
    wp_localize_script('dns-lookup-script', 'dns_lookup_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('dns_lookup_nonce')
    ));

    // Localization for JavaScript translations
    wp_localize_script('dns-lookup-script', 'dnsLookupTranslations', array(
        'fetchingRecords' => __('Fetching DNS records...', 'dns-lookup'),
        'dnsRecordsFor'   => __('DNS Records for', 'dns-lookup'),
        'priority'        => __('Priority', 'dns-lookup'),
        'error'           => __('Error', 'dns-lookup'),
        'ajaxError'       => __('Ajax error', 'dns-lookup')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_dns_lookup_scripts');

// Ajax handler for DNS lookup
add_action('wp_ajax_dns_lookup', 'dns_lookup_callback');
add_action('wp_ajax_nopriv_dns_lookup', 'dns_lookup_callback');

function dns_lookup_callback() {
    check_ajax_referer('dns_lookup_nonce', 'security');
    $domain = isset($_POST['domain']) ? sanitize_text_field($_POST['domain']) : '';

    if (!empty($domain)) {
        $dns_records = dns_get_record($domain);
        wp_send_json_success($dns_records);
    } else {
        wp_send_json_error(__('Invalid domain provided.', 'dns-lookup'));
    }

    wp_die();
}

// Shortcode for DNS lookup form
function dns_lookup_shortcode() {
    ob_start();
    ?>
    <div id="dns-lookup-form">
        <input type="text" id="domain-name" placeholder="<?php echo esc_attr(__('Enter domain name', 'dns-lookup')); ?>">
        <button id="dns-lookup-button"><?php echo esc_html(__('Check DNS', 'dns-lookup')); ?></button>
        <div id="dns-lookup-results"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dns_lookup_form', 'dns_lookup_shortcode');
