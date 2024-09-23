<?php

namespace Grow;

/**
 * The PluginConfig is a data holding class that is concerned with the static details of the plugin
 *
 * Version numbers, namespaces, required versions, and anything else that is known about the plugin
 * should be stored here
 *
 * @since 0.0.1
 */
interface PluginConfigInterface {
	/**
	 * @return string
	 */
	public function get_api_namespace() : string;

	/**
	 * @return string
	 */
	public function get_name() : string;

	/**
	 * @return string
	 */
	public function get_version() : string;

	/**
	 * @return string
	 */
	public function get_requires_wp() : string;

	/**
	 * @return string
	 */
	public function get_requires_php() : string;
}
