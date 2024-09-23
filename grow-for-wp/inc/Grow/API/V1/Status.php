<?php

namespace Grow\API\V1;

use Grow\WordPress;

/**
 * Status resource schema.
 *
 * @internal
 */
class Status {

	/**
	 * Get the schema config, formatted for JSON Schema.
	 *
	 * @return array<string, array<int|string, array<string, array<int, string>|string>|string>|string>
	 */
	public static function schema() : array {
		return [
			'$schema'    => 'https://json-schema.org/draft/2020-12/schema',
			'title'      => 'Grow Plugin Status',
			'type'       => 'object',
			'properties' => [
				'version'   => [
					'description' => esc_html__( 'Current Grow for WordPress version', 'grow-for-wp' ),
					'pattern'     => '^[\\d]+\\.[\\d]+\\.[\\d]$',
					'type'        => [ 'null', 'string' ],
				],
				'connected' => [
					'description' => esc_html__( 'Whether the site is connected to Grow', 'grow-for-wp' ),
					'type'        => 'boolean',
				],
				'valid'     => [
					'description' => esc_html__( 'Whether the passed id is the same as the connected id of the site', 'grow-for-wp' ),
					'type'        => 'boolean',
				],
			],
			'required'   => [ 'version', 'connected' ],
		];
	}

	/**
	 * Arguments for the Status Endpoint
	 *
	 * @return array[]
	 */
	public static function args() : array {
		return [
			'site_id' => [
				'type'              => 'string',
				'description'       => esc_html__( 'Site ID to check validity of', 'grow-for-wp' ),
				'required'          => false,
				'validate_callback' => function( $param ) {
					return is_string( $param );
				},
			],
		];
	}
}
