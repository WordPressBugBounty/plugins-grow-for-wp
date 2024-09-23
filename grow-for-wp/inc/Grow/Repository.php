<?php

namespace Grow;

use Grow\Views\ViewLoaderInterface;

/**
 * The Repository is a wrapper class for all the Data holding classes
 *
 * The Repository shouldn't hold any data itself, only make it easy to pass and access
 * data that it holds.
 *
 * @since 0.0.1
 */
class Repository {

	/** @var PluginConfigInterface $config */
	private PluginConfigInterface $config;


	/** @var EnvironmentInterface $environment */
	private EnvironmentInterface $environment;

	/** @var OptionsInterface $options */
	private OptionsInterface $options;

	/** @var GrowRemoteInterface $grow_remote */
	private GrowRemoteInterface $grow_remote;

	/** @var ViewLoaderInterface $view_loader */
	private ViewLoaderInterface $view_loader;

	/**
	 * @param PluginConfigInterface $config Information about the Plugin
	 * @param EnvironmentInterface  $environment Information about the Environment
	 * @param OptionsInterface      $options Information from the Database
	 * @param GrowRemoteInterface   $grow_remote Information about the Grow Remote Server
	 * @param ViewLoaderInterface   $view_loader View Loader
	 */
	public function __construct( PluginConfigInterface $config, EnvironmentInterface $environment, OptionsInterface $options, GrowRemoteInterface $grow_remote, ViewLoaderInterface $view_loader ) {
		$this->config      = $config;
		$this->environment = $environment;
		$this->options     = $options;
		$this->grow_remote = $grow_remote;
		$this->view_loader = $view_loader;
	}

	/**
	 * @return PluginConfigInterface
	 */
	public function get_config() : PluginConfigInterface {
		return $this->config;
	}

	/**
	 * @return EnvironmentInterface
	 */
	public function get_environment() : EnvironmentInterface {
		return $this->environment;
	}

	/**
	 * @return OptionsInterface
	 */
	public function get_options() : OptionsInterface {
		return $this->options;
	}

	/**
	 * @return GrowRemoteInterface
	 */
	public function get_grow_remote() : GrowRemoteInterface {
		return $this->grow_remote;
	}

	/**
	 * @return ViewLoaderInterface
	 */
	public function get_view_loader() : ViewLoaderInterface {
		return $this->view_loader;
	}
}
