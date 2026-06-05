<?php
/**
 * Removes KlenWriter data on uninstall.
 *
 * @package KlenWriter
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'klenwriter_options' );
delete_transient( 'klenwriter_activation_notice' );
