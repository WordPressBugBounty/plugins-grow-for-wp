<?php

namespace Grow;

interface EnvironmentInterface {

	/**
	 * Returns if Grow Social is running in the current environment
	 *
	 * @return bool
	 */
	public function get_has_grow_social() : bool;

	/**
	 * Returns if MCP is running in the current environment
	 *
	 * @return bool
	 */
	public function get_has_mcp() : bool;

	/**
	 * Returns if Create by Mediavine is running in the current environment
	 *
	 * @return bool
	 */
	public function get_has_create() : bool;

	/**
	 * Returns the current Create by Mediavine version, if any.
	 *
	 * @return string
	 */
	public function get_create_version() : string;

	/**
	 * Returns if WP Rocket is running in the current environment.
	 *
	 * @return bool
	 */
	public function get_has_wp_rocket() : bool;

	/**
	 * Returns the domain for the site
	 *
	 * @return string
	 */
	public function get_domain() : string;


	/**
	 * Returns the path for the site
	 *
	 * @return string
	 */
	public function get_path() : string;

	/**
	 * Returns the Filesystem root path of the WordPress install.
	 *
	 * @return string
	 */
	public function get_root_path() : string;

	/**
	 * @return string
	 */
	public function get_home_url() : string;

	/**
	 * Returns the title of the site
	 *
	 * @return string
	 */
	public function get_site_title() : string;

	/**
	 * Returns a list of HTTP headers sent (or ready to send).
	 *
	 * @return array
	 */
	public function get_headers_list() : array;

	/**
	 * Checks if HTTP headers have been sent.
	 *
	 * @return bool
	 */
	public function headers_sent() : bool;

	/**
	 * Send a raw HTTP header.
	 *
	 * @param string $header  The header string.
	 *
	 * @return void
	 */
	public function set_header( string $header ): void;
}
