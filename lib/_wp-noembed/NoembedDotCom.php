<?php

class NoembedService_NoembedDotCom extends NoembedService {

	public function get_provider_endpoint() {
		return 'https://noembed.com/providers';
	}

	public function get_oembed_endpoint() {
		return 'https://noembed.com/embed';
	}

}

function add_noembed_dotcom( Noembed $noembed ) {
	$noembed->add_service( new NoembedService_NoembedDotCom );
}

add_action( 'noembed_loaded', 'add_noembed_dotcom' );
