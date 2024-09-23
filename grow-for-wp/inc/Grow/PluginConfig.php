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
class PluginConfig implements PluginConfigInterface {

	/** @var string Namespace for the plugin api */
	private string $api_namespace;

	/** @var string Readable name of the plugin */
	private string $name;

	/** @var string Current Version of the plugin */
	private string $version;

	/** @var string Minimum Required Version of WordPress to use this plugin */
	private string $requires_wp;

	/** @var string Minimum Required Version of PHP to use this plugin */
	private string $requires_php;

	/**
	 * @param string $api_namespace Namespace for the plugin api
	 * @param string $name Readable name of the plugin
	 * @param string $version Current Version of the plugin
	 * @param string $requires_wp Minimum Required Version of WordPress to use this plugin
	 * @param string $requires_php Minimum Required Version of PHP to use this plugin
	 */
	public function __construct(
		string $api_namespace,
		string $name,
		string $version,
		string $requires_wp,
		string $requires_php
	) {
		$this->api_namespace = $api_namespace;
		$this->name          = $name;
		$this->version       = $version;
		$this->requires_wp   = $requires_wp;
		$this->requires_php  = $requires_php;
	}

	/**
	 * @return string
	 */
	public function get_api_namespace() : string {
		return $this->api_namespace;
	}

	/**
	 * @return string
	 */
	public function get_name() : string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_version() : string {
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function get_requires_wp() : string {
		return $this->requires_wp;
	}

	/**
	 * @return string
	 */
	public function get_requires_php() : string {
		return $this->requires_php;
	}
}
