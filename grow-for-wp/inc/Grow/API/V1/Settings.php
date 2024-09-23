<?php

namespace Grow\API\V1;

use Grow\WordPress;

/**
 * Status resource schema.
 *
 * @internal
 */
class Settings {

	/**
	 * Get the schema config, formatted for JSON Schema.
	 *
	 * @return array<string, array<string, array>|string>
	 */
	public static function schema() : array {
		return [
			'$schema'    => 'https://json-schema.org/draft/2020-12/schema',
			'title'      => 'Grow Plugin Settings',
			'type'       => 'object',
			'properties' => [
				'grow_site_id' => self::grow_site_id(),
			],
		];
	}

	/**
	 * @return array[]
	 */
	public static function args() : array {
		return [ 'grow_site_id' => self::grow_site_id() ];
	}

	/**
	 * @return array
	 */
	private static function grow_site_id() : array {
		return [
			'type'        => 'string',
			'description' => esc_html__( 'Grow Site ID', 'grow-for-wp' ),
		];
	}
}
