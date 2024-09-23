<?php

namespace Grow;

use Grow\Pages\AdminPage;

/**
 * The Activation class is responsible for all actions when the plugin is activated or deactivated
 *
 * @since 0.0.1
 */
class Activation implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var string Name for the action that is fired when the plugin is activated */
	public const ACTIVATION_HOOK = 'grow_activated';

	/** @var string Name for the action that is fired when the plugin is deactivated */
	public const DEACTIVATION_HOOK = 'grow_deactivated';

	/** @var OptionsInterface Allows updating options in the activation actions */
	private OptionsInterface $options;

	/** @var string The current version of the plugin */
	private string $version;

	/**
	 * Set up the class
	 *
	 * @param Repository $repository Gives access to config and options
	 */
	public function __construct( Repository $repository ) {
		$this->options = $repository->get_options();
		$this->version = $repository->get_config()->get_version();
		$this->actions = [
			new HookArguments( self::ACTIVATION_HOOK, 'run' ),
			new HookArguments( 'admin_init', 'redirect_after_activation' ),
		];
	}

	/**
	 * Runs when the plugin is activated
	 * Saves details about the plugin when it was first activated
	 *
	 * @return void
	 */
	public function run() : void {
		if ( ! $this->options->get_grow_first_install( '', true ) ) {
			$this->options->set_grow_first_install( strval(time()) );
		}
		if ( ! $this->options->get_grow_first_install_version( '', true ) ) {
			$this->options->set_grow_first_install_version( $this->version );
		}

		if ( $this->can_redirect_after_activation() ) {
			// Set flag here to auto-redirect to settings next WP cycle.
			$this->options->set_grow_should_redirect_after_activation( '1' );
		}
	}

	/**
	 * Handles redirecting to the settings page directly after the plugin has been enabled.
	 */
	public function redirect_after_activation() : void {
		if ( empty( $this->options->get_grow_should_redirect_after_activation() ) ) {
			return;
		}

		if ( ! WordPress::is_admin() ) {
			return;
		}

		$this->options->set_grow_should_redirect_after_activation( '' );
		//phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
		WordPress::wp_safe_redirect( WordPress::esc_url( WordPress::admin_url( 'admin.php?page=' . AdminPage::MENU_SLUG ) ) );
		if ( ! defined( 'GROW_TEST_MODE' ) ) {
			exit;
		}
	}

	/**
	 * Verifies that a redirect after activation is possible.
	 *
	 * @return bool
	 */
	public function can_redirect_after_activation() : bool {
		// Don't redirect on multi-site install.
		if ( WordPress::is_network_admin() ) {
			return false;
		}

		// Don't redirect if this was activated as part of a bulk-enable.
		$maybe_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_VALIDATE_BOOLEAN );
		if ( $maybe_multi ) {
			return false;
		}

		return true;
	}
}
