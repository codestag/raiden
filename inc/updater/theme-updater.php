<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Theme Updater
 */

// Includes the files needed for the theme updater.
if ( ! class_exists( 'EDD_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

// Loads the updater classes.
$updater = new EDD_Theme_Updater_Admin(

	// Config settings.
	$config = array(
		'remote_api_url' => 'https://codestag.com',
		'item_name'      => 'Raiden',
		'theme_slug'     => 'raiden',
		'version'        => RAIDEN_VERSION,
		'author'         => 'Codestag',
	),
	// Strings.
	$strings = array(
		'theme-license'             => __( 'Theme License', 'raiden' ),
		'enter-key'                 => __( 'Enter your theme license key.', 'raiden' ),
		'license-key'               => __( 'License Key', 'raiden' ),
		'license-action'            => __( 'License Action', 'raiden' ),
		'deactivate-license'        => __( 'Deactivate License', 'raiden' ),
		'activate-license'          => __( 'Activate License', 'raiden' ),
		'status-unknown'            => __( 'License status is unknown.', 'raiden' ),
		'renew'                     => __( 'Renew?', 'raiden' ),
		'unlimited'                 => __( 'unlimited', 'raiden' ),
		'license-key-is-active'     => __( 'License key is active.', 'raiden' ),
		'expires%s'                 => __( 'Expires %s.', 'raiden' ),
		'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'raiden' ),
		'license-key-expired-%s'    => __( 'License key expired %s.', 'raiden' ),
		'license-key-expired'       => __( 'License key has expired.', 'raiden' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'raiden' ),
		'license-is-inactive'       => __( 'License is inactive.', 'raiden' ),
		'license-key-is-disabled'   => __( 'License key is disabled.', 'raiden' ),
		'site-is-inactive'          => __( 'Site is inactive.', 'raiden' ),
		'license-status-unknown'    => __( 'License status is unknown.', 'raiden' ),
		'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'raiden' ),
		'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'raiden' )
	)
);
