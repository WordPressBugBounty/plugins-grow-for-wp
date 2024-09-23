<?php

namespace Grow;

/**
 * The Grow Remote Class is a data holding class that is concerned with all the details production Grow instance
 *
 * Anything dealing with the remote Grow Server should be handled here
 *
 * @since 0.0.1
 */
interface GrowRemoteInterface {

	/**
	 * @return string
	 */
	public function get_script_root() : string;

	/**
	 * @return string
	 */
	public function get_api_root() : string;

	/**
	 * @return string
	 */
	public function get_publisher_dashboard() : string;

	/**
	 * Converts the GPP Site ID to the Site UUID.
	 *
	 * @param string $site_id The GPP Site ID.
	 *
	 * @return string The Site UUID or an empty string if unable to convert.
	 */
	public function convert_site_id_to_uuid ( $site_id): string;
}
