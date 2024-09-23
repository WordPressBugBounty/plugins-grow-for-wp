<?php

namespace Grow;

/**
 * The Update Class is responsible for running all actions that need to happen when the plugin updates
 *
 * @since 0.0.1
 */
class Update implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var string Hook name for the action fired when the plugin has been updated */
	public const UPDATE_HOOK = 'grow_updated';

	/** @var GrowRemoteInterface Access Grow remote functionality */
	private GrowRemoteInterface $grow_remote;

	/** @var OptionsInterface Allows updating options in the update process  */
	private OptionsInterface $options;

	/**
	 * Set up Class
	 *
	 * @param Repository $repository Gives access to the Objects class
	 */
	public function __construct( Repository $repository ) {
		$this->options     = $repository->get_options();
		$this->grow_remote = $repository->get_grow_remote();
		$this->actions     = [ new HookArguments(self::UPDATE_HOOK, 'run') ];
	}

	/**
	 * Updates the DB Version of the plugin
	 *
	 * @param string $version The version passed in to update, this is the version parsed from the plugin bootstrap file
	 *
	 * @return void
	 */
	public function run( string $version ): void {
		$this->options->set_grow_current_version( $version );

		// Create site UUID from site ID if it hasn't been done before.
		$grow_site_id   = $this->options->get_grow_site_id();
		$grow_site_uuid = $this->options->get_grow_site_uuid();

		if ( ! empty( $grow_site_id ) && empty( $grow_site_uuid ) ) {
			$uuid = $this->grow_remote->convert_site_id_to_uuid( $grow_site_id );
			if ( ! empty( $uuid ) ) {
				$this->options->set_grow_site_uuid( $uuid );
			}
		}
	}
}
