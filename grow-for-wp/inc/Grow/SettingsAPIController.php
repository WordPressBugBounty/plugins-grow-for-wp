<?php

namespace Grow;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Controller for the Grow Settings
 */
class SettingsAPIController implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var GrowRemoteInterface Grants access to GPP related functionality */
	private GrowRemoteInterface $remote;

	/** @var OptionsInterface Allows setting values in the database */
	private OptionsInterface $options;

	/** @var Endpoint The Settings API Endpoint */
	private Endpoint $endpoint;


	/**
	 * Set up the instance.
	 *
	 * @param Repository                                 $repository Allows access to config and options
	 * @param array<string, array<string, array>|string> $settings_schema Schema for this endpoint
	 * @param array[]                                    $settings_args Args for this endpoint
	 */
	public function __construct( Repository $repository, array $settings_schema, array $settings_args ) {
		$config         = $repository->get_config();
		$this->options  = $repository->get_options();
		$this->remote   = $repository->get_grow_remote();
		$this->endpoint = new Endpoint( [ $this, 'put_settings' ], Endpoint::EDITABLE, 'settings', $config->get_api_namespace(), $settings_schema, $settings_args, function () {
			return WordPress::current_user_can( 'manage_options' );
		} );
		$this->actions  = [ new HookArguments('rest_api_init', 'register_route') ];
	}

	/**
	 * Get the addon status.
	 *
	 * @param WP_REST_Request $request The Request object from the WordPress installation
	 *
	 * @return WP_REST_Response
	 */
	public function put_settings( WP_REST_Request $request ) : WP_REST_Response {
		$data           = $request->get_params();
		$grow_site_id   = $data['grow_site_id'];
		$grow_site_uuid = $this->remote->convert_site_id_to_uuid( $grow_site_id );

		$id_success   = $this->check_no_update_success( 'grow_site_id', $grow_site_id );
		$uuid_success = $this->check_no_update_success( 'grow_site_uuid', $grow_site_uuid );

		$success = ( $id_success && $uuid_success );

		return new WP_REST_Response( [ 'success' => $success ], $success ? 200 : 500 );
	}

	/**
	 * Because update_option will return false if the value does not update, we need to check if that was the reason we
	 * are getting false, and still consider that a success
	 *
	 * @param string $key Key to update
	 * @param mixed  $value Value to update with
	 *
	 * @return bool
	 */
	private function check_no_update_success( string $key, $value ) : bool {
		$current_value    = $this->options->{'get_' . $key}();
		$values_identical = $current_value === $value;
		$success          = $this->options->{'set_' . $key}( $value );
		if ( ! $success && $values_identical ) {
			$success = true;
		}
		return $success;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @param WP_REST_Server $wp_rest_server Server instance from the WordPress installation
	 */
	public function register_route( WP_REST_Server $wp_rest_server ) : void {
		$this->endpoint->register_route( $wp_rest_server );
	}
}
