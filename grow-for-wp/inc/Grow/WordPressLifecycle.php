<?php

namespace Grow;

/**
 * The WordPressLifecycle is responsible for managing Hooks for WordPress
 *
 * This class is responsible for collecting all the hooks from the other classes, then executing them at the
 * appropriate time This architecture allows us to see ay any point which hooks have been registered with which
 * properties, and at a glance gain an understanding of all the hooks that the Plugin uses
 *
 * @since 0.0.1
 */
class WordPressLifecycle implements WordPressLifecycleInterface {
	/**
	 * @var Hook[]
	 */
	public array $filters = [];

	/**
	 * @var Hook[]
	 */
	public array $actions = [];

	/** @var OptionsInterface Allows access to version for checking if the plugin has updated */
	private OptionsInterface $options;

	/** @var string Current version from the bootstrap file */
	private string $version;

	/**
	 * @param Repository $repository Gives access to version
	 */
	public function __construct( Repository $repository ) {
		$this->options = $repository->get_options();
		$this->version = $repository->get_config()->get_version();
		$this->create_update_action();
	}

	/**
	 * Register the hooks for a given class
	 *
	 * @param HasWordpressHooksInterface $class Class with hooks to add
	 *
	 * @return void
	 */
	public function collect( HasWordpressHooksInterface $class ) : void {
		foreach ( $class->get_filters() as $hook ) {
			$this->filters[] = $hook;
			WordPress::add_filter( $hook->get_hook_name(), $hook->get_callback(), $hook->get_priority(), $hook->get_accepted_args() ); // @phpstan-ignore-line
		}

		foreach ( $class->get_actions() as $hook ) {
			$this->actions[] = $hook;
			WordPress::add_action( $hook->get_hook_name(), $hook->get_callback(), $hook->get_priority(), $hook->get_accepted_args() ); // @phpstan-ignore-line
		}
	}

	/**
	 * Register the hooks for an array of classes
	 *
	 * @param HasWordpressHooksInterface[] $classes Classes with hooks to register
	 *
	 * @return void
	 */
	public function collect_many( array $classes ) : void {
		foreach ( $classes as $class ) {
			$this->collect( $class );
		}
	}

	/**
	 * Creates an Action that is run when the Database version differs from the code version
	 *
	 * @return void
	 */
	private function create_update_action() : void {
		$hook            = new Hook('wp_loaded', function () {
			$code_version = $this->version; // Code Version should be newer and used to replace the db version
			$db_version   = $this->options->get_grow_current_version();
			if ( $code_version !== $db_version ) {
				WordPress::do_action( Update::UPDATE_HOOK, [ $code_version ] );
			}
		} );
		$this->actions[] = $hook;
		WordPress::add_action( $hook->get_hook_name(), $hook->get_callback(), $hook->get_priority(), $hook->get_accepted_args() ); // @phpstan-ignore-line
	}

	/**
	 * Registers and creates a hook to be run when plugin is activated
	 *
	 * @return void
	 */
	public function register_activation() : void {
		WordPress::do_action( Activation::ACTIVATION_HOOK );
	}

	/**
	 * Register and create a hook to be run when plugin is deactivated
	 *
	 * @return void
	 */
	public function register_deactivation() : void {
		WordPress::do_action( Activation::DEACTIVATION_HOOK );
	}
}
