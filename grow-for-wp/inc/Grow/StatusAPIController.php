<?php

namespace Grow;

use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Controller for the Grow status resource
 */
class StatusAPIController implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var string */
	private string $plugin_version;

	/** @var Endpoint The Status API Controller Endpoint */
	private Endpoint $endpoint;

	/** @var string The ID of the site in the Grow Remote */
	private string $connected_site_id;


	/**
	 * Set up the instance.
	 *
	 * @param Repository                                        $repository Allows Access to plugin config
	 * @param array<string, string|array<string, string>|mixed> $status_schema The JSON schema
	 * @param array<string, string|array<string, string>|mixed> $status_args The JSON schema
	 */
	public function __construct( Repository $repository, array $status_schema, array $status_args ) {
		$config                  = $repository->get_config();
		$this->plugin_version    = $config->get_version();
		$this->endpoint          = new Endpoint([ $this, 'get_status' ], Endpoint::READABLE, 'status\/?(?P<site_id>.*)', $config->get_api_namespace(), $status_schema, $status_args );
		$this->actions           = [ new HookArguments('rest_api_init', 'register_route') ];
		$this->connected_site_id = $repository->get_options()->get_grow_site_id();
	}

	/**
	 * Get the addon status.
	 *
	 * @param \WP_REST_Request $request The request object
	 *
	 * @return WP_REST_Response
	 */
	public function get_status( \WP_REST_Request $request ) : WP_REST_Response {
		$data = [
			'version'   => $this->plugin_version,
			'connected' => boolval($this->connected_site_id),
		];
		if ( ! empty( $request->get_param( 'site_id' ) ) ) {
			$data['valid'] = $this->connected_site_id === $request->get_param( 'site_id' );
		}
		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @param WP_REST_Server $wp_rest_server The rest server from the WordPress instance
	 */
	public function register_route( WP_REST_Server $wp_rest_server ) : void {
		$this->endpoint->register_route($wp_rest_server);
	}
}
