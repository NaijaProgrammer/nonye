<?php

class NoembedService_OembedDotIo extends NoembedService {

	public function get_provider_endpoint() {
		return 'http://oembed.io/providers';
	}

	public function get_oembed_endpoint() {
		return 'http://oembed.io/api';
	}

	public function get_provider_element_name() {
		return 's';
	}

}

function add_oembed_dotio( Noembed $noembed ) {
	$noembed->add_service( new NoembedService_OembedDotIo );
}

add_action( 'noembed_loaded', 'add_oembed_dotio' );
