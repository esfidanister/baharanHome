<?php
/**
 * Loads the official API.IR WordPress SDK (v1.0.0) copied under apiir/.
 * Token stays in APIIR_TOKEN (wp-config.php or APIIR_TOKEN env). Never expose it to the browser.
 */
if (!defined('ABSPATH')) {
    exit;
}

$apiir_dir = dirname(__DIR__) . '/apiir';
if (!is_dir($apiir_dir . '/services')) {
    return;
}

foreach (glob($apiir_dir . '/services/*.php') ?: [] as $apiir_service) {
    require_once $apiir_service;
}
unset($apiir_dir, $apiir_service);
