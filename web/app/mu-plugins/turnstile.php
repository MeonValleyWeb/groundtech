<?php
/**
 * Plugin Name: CF7 Turnstile Server Validation
 * Description: Server-side Turnstile token verification for Contact Form 7.
 * Author: Your Name
 * Version: 1.0.1
 */

add_action('wpcf7_before_send_mail', function($contact_form) {
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return;

    $post_data = $submission->get_posted_data();

    $token = $post_data['cf-turnstile-response']
        ?? $post_data['cloudflare_turnstile-832']
        ?? '';

    if (empty($token)) {
        error_log('Turnstile: No token found in submitted form.');
    }

    if (!verify_turnstile_token($token)) {
        error_log('Turnstile: Token verification failed.');
        $contact_form->skip_mail = true;

        $submission->set_response([
            'invalid_fields' => [
                [
                    'into' => 'cf-turnstile-response',
                    'message' => 'Turnstile validation failed. Please try again.',
                ]
            ]
        ]);
    }
}); // ← ✅ this line was missing

function verify_turnstile_token($token) {
    $secret_key = getenv('CF_TURNSTILE_SECRET_KEY');
    $remote_ip  = $_SERVER['REMOTE_ADDR'];
    $verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    if (empty($secret_key)) {
        error_log('Turnstile: Missing secret key from environment.');
        return false;
    }

    $response = wp_remote_post($verify_url, [
        'body' => [
            'secret'   => $secret_key,
            'response' => $token,
            'remoteip' => $remote_ip,
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('Turnstile: wp_remote_post failed - ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    error_log('Turnstile: API response - ' . print_r($body, true));

    return isset($body['success']) && $body['success'] === true;
}

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'cf-turnstile',
        'https://challenges.cloudflare.com/turnstile/v0/api.js',
        [],
        null,
        true
    );
});