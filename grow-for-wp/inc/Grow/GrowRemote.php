<?php

namespace Grow;

/**
 * The Grow Remote Class is a data holding class that is concerned with all the details of the production Grow instance
 *
 * Anything dealing with the remote Grow Server should be handled here
 *
 * @since 0.0.1
 */
class GrowRemote implements GrowRemoteInterface {

	/** @var string Script Root for the Production Server */
	private const SCRIPT_ROOT_PRODUCTION = 'https://faves.grow.me';

	/** @var string Script ROot for the Staging Server */
	private const SCRIPT_ROOT_STAGING = 'https://faves-grow-me-staging.mediavine.dev';

	/** @var string Script Root for the Development Server */
	private const SCRIPT_ROOT_DEVELOPMENT = 'http://localhost:3000';

	/** @var string Name of the filter to altering the script root */
	public const SCRIPT_ROOT_FILTER = 'grow_remote_script_root';

	/** @var string API Base URL for the Production Server */
	private const API_ROOT_PRODUCTION = 'https://api.grow.me';

	/** @var string API Base URL for the Staging Server */
	private const API_ROOT_STAGING = 'https://api-grow-me-staging.mediavine.dev';

	/** @var string API Base URL for the Development Server */
	private const API_ROOT_DEVELOPMENT = 'http://localhost:3001';

	/** @var string Name of the filter to altering the api base */
	public const API_ROOT_FILTER = 'grow_remote_api_root';

	/** @var string API Base URL for the Production Server */
	private const PUBLISHER_DASHBOARD_PRODUCTION = 'https://publishers.grow.me';

	/** @var string API Base URL for the Staging Server */
	private const PUBLISHER_DASHBOARD_STAGING = 'https://dashboard-staging.mediavine.com';

	/** @var string API Base URL for the Development Server */
	private const PUBLISHER_DASHBOARD_DEVELOPMENT = 'http://localhost:3004';

	/** @var string Name of the filter to altering the api base */
	public const PUBLISHER_DASHBOARD_FILTER = 'grow_remote_publisher_dashboard';

	/** @var string Key for filters to indicate Production values should be used */
	public const PRODUCTION = 'PRODUCTION';

	/** @var string Key for filters to indicate Staging values should be used */
	public const STAGING = 'STAGING';

	/** @var string Key for filters to indicate Development values should be used */
	public const DEVELOPMENT = 'DEVELOPMENT';

	/**
	 * Get the appropriate script root
	 *
	 * Will return the production script root by default unless the filter returns a different value
	 * PRODUCTION, STAGING, or DEVELOPMENT as values from the filter will use a pre-defined url as the script root,
	 * otherwise any other url can be passed if needed.
	 *
	 * @return string
	 */
	public function get_script_root() : string {
		$script_root = WordPress::apply_filters( self::SCRIPT_ROOT_FILTER, null );
		$script_root = is_string( $script_root ) ? $script_root : null;
		return self::get_environment_value(
			$script_root,
			self::SCRIPT_ROOT_PRODUCTION,
			self::SCRIPT_ROOT_STAGING,
			self::SCRIPT_ROOT_DEVELOPMENT
		);
	}

	/**
	 * Get the appropriate script root
	 *
	 * Will return the production script root by default unless the filter returns a different value
	 * PRODUCTION, STAGING, or DEVELOPMENT as values from the filter will use a pre-defined url as the script root,
	 * otherwise any other url can be passed if needed.
	 *
	 * @return string
	 */
	public function get_api_root() : string {
		$api_root = WordPress::apply_filters( self::API_ROOT_FILTER, null );
		$api_root = is_string( $api_root ) ? $api_root : null;
		return self::get_environment_value(
			$api_root,
			self::API_ROOT_PRODUCTION,
			self::API_ROOT_STAGING,
			self::API_ROOT_DEVELOPMENT
		);
	}

	/**
	 * Get the appropriate site settings route
	 *
	 * Will return the production settings root by default unless the filter returns a different value
	 * PRODUCTION, STAGING, or DEVELOPMENT as values from the filter will use a pre-defined url as the site settings page,
	 * otherwise any other url can be passed if needed.
	 *
	 * @return string
	 */
	public function get_publisher_dashboard() : string {
		$api_root = WordPress::apply_filters( self::PUBLISHER_DASHBOARD_FILTER, null );
		$api_root = is_string( $api_root ) ? $api_root : null;
		return self::get_environment_value(
			$api_root,
			self::PUBLISHER_DASHBOARD_PRODUCTION,
			self::PUBLISHER_DASHBOARD_STAGING,
			self::PUBLISHER_DASHBOARD_DEVELOPMENT
		);
	}

	/**
	 * @param string|null $value The value to test against, may be a token or arbitrary string
	 * @param string      $prod The desired value for Production environments
	 * @param string      $staging The desired value for Staging Environments
	 * @param string      $dev The desired value for Development Environments
	 *
	 * @return string
	 */
	private static function get_environment_value( ?string $value, string $prod, string $staging, string $dev ) : string {
		switch ( $value ) {
			case self::PRODUCTION:
			case null:
				return $prod;
			case self::STAGING:
				return $staging;
			case self::DEVELOPMENT:
				return $dev;
			default:
				return $value;
		}
	}

	/**
	 * Converts the GPP Site ID to the Site UUID.
	 *
	 * @param string $site_id The GPP Site ID.
	 *
	 * @return string The Site UUID or an empty string if unable to convert.
	 */
	public function convert_site_id_to_uuid( $site_id ): string {
		if ( empty( $site_id ) ) {
			return '';
		}

		$uuid = base64_decode( $site_id );
		$uuid = str_ireplace( 'Site:', '', $uuid );
		if ( strlen( $uuid ) === 36 ) {
			return $uuid;
		}

		return '';
	}
}
