<?php
/*
Plugin Name: Noembed - oEmbed everything
Plugin URI:  https://github.com/johnbillion/noembed
Description: Embed content from a whole list of sites that don't natively support oEmbed. Uses embeds from oembed.io and noembed.com.
Author:      John Blackbourn
Version:     1.1
Author URI:  https://johnblackbourn.com/
Text Domain: noembed
Domain Path: /languages/
License:     GPL v2 or later

Copyright © 2014 John Blackbourn

Thanks of course go to Sam Snelling of oembed.io and Lee Aylward of noembed.com for providing their web services.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

defined( 'ABSPATH' ) or exit;

class Noembed {

	protected $services = array();

	protected function __construct() {

		add_filter( 'oembed_providers', array( $this, 'filter_oembed_providers' ) );
		add_action( 'plugins_loaded',   array( $this, 'action_plugins_loaded' ), 1 );

	}

	public function action_plugins_loaded() {

		do_action_ref_array( 'noembed_loaded', array( $this ) );

	}

	public function filter_oembed_providers( array $providers ) {

		foreach ( $this->services as $service ) {
			$service_providers = self::get_providers( $service );

			if ( empty( $service_providers ) ) {
				continue;
			}

			$endpoint = $service->get_oembed_endpoint();

			foreach ( $service_providers as $provider ) {

				$key = $provider;
				$key = str_replace( '\\/', '/', $key );   # Un-escape slashes
				$key = str_replace( '\\#', '#', $key );   # Un-escape hashes that might be escaped already
				$key = str_replace( '#', '\\#', $key );   # Escape hashes
				$key = preg_replace( '/^\^/', '', $key ); # Remove leading ^
				$key = preg_replace( '/\$$/', '', $key ); # Remove trailing $
				$key = '#' . $key . '#';

				# First come, first served:
				if ( isset( $providers[$key] ) ) {
					continue;
				}

				$providers[$key] = array(
					$endpoint,
					true
				);

			}
		}

		return $providers;
	}

	public function add_service( NoembedService $service ) {
		array_push( $this->services, $service );
	}

	public static function get_providers( NoembedService $service ) {
		$key = sprintf( 'noembed_%s',
			$service->get_oembed_endpoint()
		);
		$providers = get_site_transient( $key );

		if ( false !== $providers ) {
			return json_decode( $providers );
		}

		if ( $providers = self::fetch_providers( $service ) ) {
			set_site_transient( $key, json_encode( $providers ), HOUR_IN_SECONDS );
		}

		return $providers;
	}

	public static function fetch_providers( NoembedService $service ) {
		$result = wp_remote_get( $service->get_provider_endpoint(), array(
			'user-agent' => sprintf( 'Noembed plugin for WordPress; %s',
				home_url()
			),
		) );

		if ( is_wp_error( $result ) ) {
			return false;
		}
		if ( 200 != wp_remote_retrieve_response_code( $result ) ) {
			return false;
		}
		if ( ! $json = json_decode( wp_remote_retrieve_body( $result ) ) ) {
			return false;
		}

		$providers = array();
		$element   = $service->get_provider_element_name();

		foreach ( $json as $provider ) {
			if ( isset( $provider->$element ) ) {
				$providers = array_merge( $providers, (array) $provider->$element );
			}
		}

		return $providers;
	}

	public static function init() {
		static $instance = null;

		if ( !$instance ) {
			$instance = new Noembed;
		}

		return $instance;
	}

}

abstract class NoembedService {

	abstract public function get_provider_endpoint();

	abstract public function get_oembed_endpoint();

	public function get_provider_element_name() {
		return 'patterns';
	}

}

include dirname( __FILE__ ) . '/OembedDotIo.php';
include dirname( __FILE__ ) . '/NoembedDotCom.php';

Noembed::init();
