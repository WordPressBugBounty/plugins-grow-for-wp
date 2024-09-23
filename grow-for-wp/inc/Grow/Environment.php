<?php

namespace Grow;

/**
 * The Environment is a data holding class that is concerned with all the details of the environment.
 *
 * Software Versions, Other Plugins Running, anything external to the plugin but on the site itself
 * should have its information stored in this class
 *
 * @since 0.0.1
 */
class Environment implements EnvironmentInterface {

	/** @var bool Whether this installation has Grow Social Running */
	private bool $has_grow_social;

	/** @var bool Whether this installation has MCP Running */
	private bool $has_mcp;

	/** @var bool Whether this installation has Create by Mediavine Running */
	private bool $has_create;

	/** @var string The version of Create by Mediavine installed, if any. */
	private string $create_version = '';

	/** @var bool Whether this installation has WP Rocket Running */
	private bool $has_wp_rocket;

	/** @var string Domain of the Site */
	private string $domain;

	/** @var string Path of the Site */
	private string $path;

	/** @var string Filesystem root path of the WordPress install */
	private string $root_path;

	/** @var string Title of the Site */
	private string $site_title;

	/** @var string Home URL of the Site. */
	private string $home_url;

	/**
	 * @return bool
	 */
	public function get_has_grow_social() : bool {
		return $this->has_grow_social;
	}

	/**
	 * @return bool
	 */
	public function get_has_mcp() : bool {
		return $this->has_mcp;
	}

	/**
	 * @return bool
	 */
	public function get_has_create() : bool {
		return $this->has_create;
	}

	/**
	 * @return string
	 */
	public function get_create_version() : string {
		return $this->create_version;
	}

	/**
	 * @return bool
	 */
	public function get_has_wp_rocket() : bool {
		return $this->has_wp_rocket;
	}

	/**
	 * @return string
	 */
	public function get_domain() : string {
		return $this->domain;
	}

	/**
	 * @return string
	 */
	public function get_path() : string {
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function get_root_path() : string {
		return $this->root_path;
	}

	/**
	 * @return string
	 */
	public function get_home_url() : string {
		return $this->home_url;
	}


	/**
	 * @return string
	 */
	public function get_site_title() : string {
		return $this->site_title;
	}

	/**
	 * @return array
	 */
	public function get_headers_list() : array {
		return headers_list();
	}

	/**
	 * @return bool
	 */
	public function headers_sent() : bool {
		return headers_sent();
	}

	/**
	 * @param string $header The header string.
	 * @return void
	 */
	public function set_header( string $header ): void {
		header( $header );
	}


	/**
	 * Run the environmental checks
	 */
	public function __construct() {
		$this->has_grow_social = class_exists('\Social_Pug');
		$this->has_mcp         = class_exists('\Mediavine\MCP\MV_Control_Panel');
		$this->has_wp_rocket   = defined( 'WP_ROCKET_VERSION' );
		$this->domain          = WordPress::site_domain();
		$this->path            = WordPress::site_path();
		$this->home_url        = WordPress::home_url();
		$this->site_title      = WordPress::site_title();

		$this->has_create = class_exists( '\Mediavine\Create\Plugin' );
		if ( $this->has_create ) {
			$create_db_version = WordPress::get_option( 'mv_create_version' );
			if ( ! empty( $create_db_version ) && is_string( $create_db_version ) ) {
				$this->create_version = $create_db_version;
			}
		}

		$this->root_path = ABSPATH;
		if ( ! empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$this->root_path = $_SERVER['DOCUMENT_ROOT'];
		}

		// Allow for root path override.
		if ( defined( 'GROW_ROOT_PATH' ) ) {
			$this->root_path = GROW_ROOT_PATH;
		}

		$this->root_path = WordPress::trailingslashit( $this->root_path );
	}

}
