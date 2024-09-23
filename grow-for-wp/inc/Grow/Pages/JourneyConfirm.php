<?php

namespace Grow\Pages;

use Grow\AssetLoader;
use Grow\FrontendData;
use Grow\HasWordpressHooksInterface;
use Grow\HasWordpressHooksTrait;
use Grow\HookArguments;
use Grow\OptionsInterface;
use Grow\Repository;
use Grow\Views\ViewLoaderInterface;
use Grow\WordPress;
use function __;

/**
 * Registers and displays the Journey Enable confirmation page.
 */
class JourneyConfirm implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var OptionsInterface $options */
	private OptionsInterface $options;

	/** @var ViewLoaderInterface $view_loader */
	private ViewLoaderInterface $view_loader;

	/** @var string Current plugin version */
	private string $version;

	/** @var string $grow_site_id Grow Site ID */
	private string $grow_site_id;

	/** @var string $grow_site_uuid Grow Site UUID */
	private string $grow_site_uuid;

	/**
	 * Sets up the Admin Page
	 *
	 * @param Repository $repository Used for the Grow Remote API Base
	 */
	public function __construct( Repository $repository ) {
		$this->actions = [
			new HookArguments( 'admin_menu', 'register' ),
			new HookArguments( 'wp_ajax_grow_journey_enable', 'handle_journey_enable' ),
			new HookArguments( 'wp_ajax_grow_journey_disable', 'handle_journey_disable' ),
			new HookArguments( 'wp_ajax_grow_journey_troubleshoot', 'handle_journey_troubleshoot' ),
			new HookArguments( 'load-admin_page_grow-journey-enable', 'redirect_on_missing_site_uuid' ),
			new HookArguments( 'load-admin_page_grow-journey-disable', 'redirect_on_missing_site_uuid' ),
			new HookArguments( 'load-admin_page_grow-journey-troubleshoot', 'redirect_on_missing_site_uuid' ),
		];
		$this->filters = [
			new HookArguments( FrontendData::GET_ADMIN_DATA_FILTER, 'with_grow_journey_data' ),
		];

		$this->view_loader    = $repository->get_view_loader();
		$this->version        = $repository->get_config()->get_version();
		$this->options        = $repository->get_options();
		$this->grow_site_id   = $this->options->get_grow_site_id();
		$this->grow_site_uuid = $this->options->get_grow_site_uuid();

		// Handle edge case where Site UUID may not be immediately available after plugin update.
		if ( empty( $this->grow_site_uuid ) && ! empty( $this->grow_site_id ) ) {
			$this->grow_site_uuid = $repository->get_grow_remote()->convert_site_id_to_uuid( $this->grow_site_id );
			if ( ! empty( $this->grow_site_uuid ) ) {
				$this->options->set_grow_site_uuid( $this->grow_site_uuid );
			}
		}
	}

	/**
	 * Ensure site UUID is available before attempting to load pages.
	 *
	 * @return void
	 */
	public function redirect_on_missing_site_uuid() {
		$page_slug = strtolower( strval( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) );
		$pages     = [ 'grow-journey-enable', 'grow-journey-disable', 'grow-journey-troubleshoot' ];

		if ( ! in_array( $page_slug, $pages, true ) ) {
			return;
		}

		if ( ! empty( $this->grow_site_uuid ) ) {
			return;
		}

		// Redirect to landing admin page if install is missing required values.
		$this->options->set_grow_show_need_connection_message( '1' );
		//phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
		WordPress::wp_safe_redirect( WordPress::esc_url( WordPress::admin_url( 'admin.php?page=' . AdminPage::MENU_SLUG ) ) );
		if ( ! defined( 'GROW_TEST_MODE' ) ) {
			exit;
		}
	}

	/**
	 * Register the custom Page
	 *
	 * @return void
	 */
	public function register() {
		// Add a submenu page without a parent to prevent it being added to sidebar.
		WordPress::add_submenu_page('', 'Enable Confirmation Page', '', 'manage_options', 'grow-journey-enable', [ $this, 'render_enable' ]);
		WordPress::add_submenu_page('', 'Disable Confirmation Page', '', 'manage_options', 'grow-journey-disable', [ $this, 'render_disable' ]);
		WordPress::add_submenu_page('', 'Troubleshoot Confirmation Page', '', 'manage_options', 'grow-journey-troubleshoot', [ $this, 'render_troubleshoot' ]);
	}

	/**
	 * @param mixed $data Frontend Admin Data to add Journey required data to.
	 *
	 * @return mixed
	 */
	public function with_grow_journey_data( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		$data['journeyData'] = [
			'enableNonce'       => WordPress::create_nonce( 'grow-journey-enable' ),
			'disableNonce'      => WordPress::create_nonce( 'grow-journey-disable' ),
			'troubleshootNonce' => WordPress::create_nonce( 'grow-journey-troubleshoot' ),
		];
		return $data;
	}

	/**
	 * Renders the View for the page
	 *
	 * @return void
	 */
	public function render_enable() {
		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo WordPress::wp_kses( $this->view_loader->get_view( 'journey-confirm-enable-view.php', $this->get_enable_view_args() ), $this->view_loader->get_allowed_tags() );
	}

	/**
	 * Handles AJAX callback from Journey Enable Confirmation page.
	 *
	 * @return void
	 */
	public function handle_journey_enable() {
		WordPress::check_ajax_referer( 'grow-journey-enable' );
		$this->options->set_grow_journey_status( '1' );
		WordPress::do_action( 'grow_journey_enabled' );
		WordPress::wp_send_json_success( [], 200 );
	}

	/**
	 * @return array
	 */
	private function get_enable_view_args(): array {
		return [
			'all'     => [
				'logo'     => AssetLoader::compose_asset_source( 'journey-logo', 'svg', $this->version ),
				'logo_alt' => __( 'Journey By Mediavine', 'grow-for-wp' ),
			],
			'confirm' => [
				'intro_text'       => __( 'Ready to start running ads on your site with <strong>Journey</strong>? Please grant Grow permission to:', 'grow-for-wp' ),
				'check_1'          => __( 'Add the Journey ads script to your site', 'grow-for-wp' ),
				'check_2'          => __( 'Manage your ads.txt file', 'grow-for-wp' ),
				'check_3'          => __( 'Provide language for your privacy policy related to our ads', 'grow-for-wp' ),
				'button_secondary' => __( 'No, Thanks', 'grow-for-wp' ),
				'button_primary'   => __( 'Enable', 'grow-for-wp' ),
			],
			'success' => [
				'bg'            => AssetLoader::compose_asset_source( 'journey-success-bg', 'svg', $this->version ),
				'header'        => __( 'Journey Successfully Enabled!', 'grow-for-wp' ),
				'body'          => __( 'Please clear your plugin, server, and CDN caches. <br/><a href="https://journeymv.zendesk.com/hc/en-us/articles/24250297500955-Clearing-Your-Cache" target="_blank">More info here.</a>', 'grow-for-wp' ),
				'button_return' => __( 'Return to Journey Onboarding', 'grow-for-wp' ),
			],
			'error'   => [
				'icon'          => AssetLoader::compose_asset_source( 'icon-error', 'svg', $this->version ),
				'icon_alt'      => __( 'Error Icon', 'grow-for-wp' ),
				'header'        => __( 'Journey Could Not Be Enabled', 'grow-for-wp' ),
				'body'          => __( 'Error encountered while trying to activate Journey. Please try again. If the error persists, return to Grow.', 'grow-for-wp' ),
				'button_cancel' => __( 'Cancel', 'grow-for-wp' ),
				'button_retry'  => __( 'Retry', 'grow-for-wp' ),
			],
		];
	}

	/**
	 * Renders the View for the page
	 *
	 * @return void
	 */
	public function render_disable() {
		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo WordPress::wp_kses( $this->view_loader->get_view( 'journey-confirm-disable-view.php', $this->get_disable_view_args() ), $this->view_loader->get_allowed_tags() );
	}

	/**
	 * Handles AJAX callback from Journey Disable Confirmation page.
	 *
	 * @return void
	 */
	public function handle_journey_disable() {
		WordPress::check_ajax_referer( 'grow-journey-disable' );
		$this->options->set_grow_journey_status( '' );
		WordPress::do_action( 'grow_journey_disabled' );
		WordPress::wp_send_json_success( [], 200 );
	}

	/**
	 * @return array
	 */
	private function get_disable_view_args(): array {
		return [
			'all'     => [
				'logo'     => AssetLoader::compose_asset_source( 'journey-logo', 'svg', $this->version ),
				'logo_alt' => __( 'Journey By Mediavine', 'grow-for-wp' ),
			],
			'confirm' => [
				'icon'             => AssetLoader::compose_asset_source( 'icon-error', 'svg', $this->version ),
				'icon_alt'         => __( 'Warning Icon', 'grow-for-wp' ),
				'header'           => __( 'Disable Journey', 'grow-for-wp' ),
				'body'             => __( 'Please ensure you have provided 30 days notice prior to disabling your Journey account per the Journey terms of service. By disabling your Journey account, you will lose the following:', 'grow-for-wp' ),
				'check_1'          => __( 'Journey ads will no longer display', 'grow-for-wp' ),
				'check_2'          => __( 'Your ads.txt file will no longer be managed by Journey', 'grow-for-wp' ),
				'check_3'          => __( 'Journey privacy policy will be removed', 'grow-for-wp' ),
				'button_secondary' => __( 'Cancel', 'grow-for-wp' ),
				'button_primary'   => __( 'Disable', 'grow-for-wp' ),
			],
			'success' => [
				'icon'          => AssetLoader::compose_asset_source( 'icon-check', 'svg', $this->version ),
				'icon_alt'      => __( 'Check Icon', 'grow-for-wp' ),
				'header'        => __( 'Journey Successfully Disabled', 'grow-for-wp' ),
				'body'          => __('Your site is no longer running Journey ads.', 'grow-for-wp'),
				'button_return' => __( 'Return to Wordpress', 'grow-for-wp' ),
			],
		];
	}

	/**
	 * Renders the View for the page
	 *
	 * @return void
	 */
	public function render_troubleshoot() {
		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo WordPress::wp_kses( $this->view_loader->get_view( 'journey-confirm-troubleshoot-view.php', $this->get_troubleshoot_view_args() ), $this->view_loader->get_allowed_tags() );
	}

	/**
	 * Handles AJAX callback from Journey Troubleshoot Confirmation page.
	 *
	 * @return void
	 */
	public function handle_journey_troubleshoot() {
		WordPress::check_ajax_referer( 'grow-journey-troubleshoot' );
		$this->options->set_grow_journey_status( '1' );
		WordPress::do_action( 'grow_journey_troubleshoot' );
		WordPress::wp_send_json_success( [], 200 );
	}

	/**
	 * @return array
	 */
	private function get_troubleshoot_view_args(): array {
		return [
			'all'     => [
				'logo'     => AssetLoader::compose_asset_source( 'journey-logo', 'svg', $this->version ),
				'logo_alt' => __( 'Journey By Mediavine', 'grow-for-wp' ),
			],
			'confirm' => [
				'header'           => __('Let\'s troubleshoot your ads.txt file', 'grow-for-wp'),
				'intro_text'       => __( 'Grow for Wordpress will reassess the method used to generate your ads.txt file and ensure your ads.txt is present and up-to-date.', 'grow-for-wp' ),
				'button_secondary' => __( 'Cancel', 'grow-for-wp' ),
				'button_primary'   => __( 'Run Troubleshooting', 'grow-for-wp' ),
			],
			'success' => [
				'bg'            => AssetLoader::compose_asset_source( 'journey-success-bg', 'svg', $this->version ),
				'header'        => __( 'Ads.txt is Updated', 'grow-for-wp' ),
				'body'          => __( 'Please clear your plugin, server, and CDN caches. <br/><a href="https://journeymv.zendesk.com/hc/en-us/articles/24250297500955-Clearing-Your-Cache" target="_blank">More info here.</a>', 'grow-for-wp' ),
				'button_return' => __( 'Return to Journey', 'grow-for-wp' ),
			],
			'error'   => [
				'icon'          => AssetLoader::compose_asset_source( 'icon-error', 'svg', $this->version ),
				'icon_alt'      => __( 'Error Icon', 'grow-for-wp' ),
				'header'        => __( 'Unable to Update Ads.txt', 'grow-for-wp' ),
				'body'          => __( 'We encountered an error. Please try again. If the error persists, return to your Journey Dashboard for further troubleshooting instructions.', 'grow-for-wp' ),
				'button_cancel' => __( 'Return to Journey', 'grow-for-wp' ),
				'button_retry'  => __( 'Retry', 'grow-for-wp' ),
			],
		];

	}

}
