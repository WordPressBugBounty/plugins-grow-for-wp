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
					'description' => 'Current Grow for WordPress version',
					'pattern'     => '^[\\d]+\\.[\\d]+\\.[\\d]$',
					'type'        => [ 'null', 'string' ],
				],
				'connected' => [
					'description' => 'Whether the site is connected to Grow',
					'type'        => 'boolean',
				],
				'valid'     => [
					'description' => 'Whether the passed id is the same as the connected id of the site',
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
				'description'       => 'Site ID to check validity of',
				'required'          => false,
				'validate_callback' => function( $param ) {
					return is_string( $param );
				},
			],
		];
	}
}
