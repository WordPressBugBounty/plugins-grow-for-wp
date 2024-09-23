<?php

namespace Grow;

use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

/**
 * The Endpoint class registers a single endpoint to WordPress
 *
 * Each endpoint may respond to multiple methods but there can only be
 * one response callback per endpoint
 */
class Endpoint implements EndpointInterface {

	public const READABLE = 'READABLE';

	public const CREATABLE = 'CREATABLE';

	public const EDITABLE = 'EDITABLE';

	public const DELETABLE = 'DELETABLE';

	public const ALLMETHODS = 'ALLMETHODS';


	/** @var mixed[] Callback to be fired when request is made */
	private array $response_callback;

	/** @var string Methods this endpoint should respond to */
	private string $api_namespace;

	/** @var array<string, string|array<string, string>|mixed> The route to this endpoint */
	private array $schema;

	/** @var string The namespace for the route */
	private string $methods;

	/** @var string The schema for the endpoint */
	private string $route;

	/** @var array<string, mixed>  The arguments for the endpoint */
	private array $args;

	/** @var callable|string|mixed[] Callback to fire to determine if request has permissions */
	private $permission_callback;


	/**
	 * @param mixed[]                                           $response_callback Callback to be fired when request is made
	 * @param string                                            $methods Methods this endpoint should respond to
	 * @param string                                            $route The route to this endpoint
	 * @param string                                            $api_namespace The namespace for the route
	 * @param array<string, string|array<string, string>|mixed> $schema The schema for the endpoint
	 * @param array<string, mixed>                              $args The arguments for the endpoint
	 * @param string|callable|mixed[]                           $permission_callback Callback to fire to determine if request has permissions
	 */
	public function __construct( array $response_callback, string $methods, string $route, string $api_namespace, array $schema, array $args = [], $permission_callback = '__return_true' ) {
		$this->response_callback   = $response_callback;
		$this->methods             = $methods;
		$this->route               = $route;
		$this->api_namespace       = $api_namespace;
		$this->schema              = $schema;
		$this->args                = $args;
		$this->permission_callback = $permission_callback;
	}

	/**
	 * Turns a string into a method string, allows the usage of WordPress method aliases without
	 * introducing a dependency
	 *
	 * @param string         $methods Methods or Method Token to use
	 * @param WP_REST_Server $wp_rest_server Rest server from the WordPress installation
	 *
	 * @return string
	 */
	private function resolve_methods( string $methods, WP_REST_Server $wp_rest_server ) : string {
		switch ( strtoupper( $methods ) ) {
			case self::READABLE:
				return $wp_rest_server::READABLE;
			case self::CREATABLE:
				return $wp_rest_server::CREATABLE;
			case self::EDITABLE:
				return $wp_rest_server::EDITABLE;
			case self::DELETABLE:
				return $wp_rest_server::DELETABLE;
			case self::ALLMETHODS:
				return $wp_rest_server::ALLMETHODS;
			default:
				return $methods;
		}
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @param WP_REST_Server $wp_rest_server Rest server from the WordPress installation
	 */
	public function register_route( WP_REST_Server $wp_rest_server ) : void {
		$wp_rest_server->register_route(
			$this->api_namespace,
			'/' . $this->api_namespace . '/' . $this->route,
			[
				[
					'args'                => $this->args,
					'callback'            => $this->response_callback,
					'methods'             => $this->resolve_methods( $this->methods, $wp_rest_server ),
					'permission_callback' => $this->permission_callback,
				],
				'schema' => [ $this->schema, 'json_schema' ],
			]
		);
	}
}
