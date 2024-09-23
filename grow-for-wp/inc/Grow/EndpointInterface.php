<?php

namespace Grow;

use WP_REST_Server;

/**
 * Controller for the Grow status resource.
 */
interface EndpointInterface {

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @param WP_REST_Server $wp_rest_server The rest server from the WordPress instance
	 */
	public function register_route( WP_REST_Server $wp_rest_server): void;
}
