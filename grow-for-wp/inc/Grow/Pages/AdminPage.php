<?php

namespace Grow\Pages;

use Grow\AssetLoader;
use Grow\FrontendData;
use Grow\HasWordpressHooksInterface;
use Grow\HasWordpressHooksTrait;
use Grow\HookArguments;
use Grow\Repository;
use Grow\Views\ViewLoaderInterface;
use Grow\WordPress;
use function __;
use function wp_kses;
use const GROW_PLUGIN_BASENAME;

/**
 * Controls the custom admin page for the plugin
 *
 * @since 0.0.1
 */
class AdminPage implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	private const PAGE_TITLE = 'Grow';

	public const MENU_SLUG = 'grow';

	private const CAPABILITY_LEVEL = 'manage_options';

	public const HOOK_SUFFIX = 'toplevel_page_' . self::MENU_SLUG;

	/** @var string $remote_api_root Grow Remote API Remote for the admin to communicate with */
	private string $remote_api_root;

	/** @var string $publisher_dashboard Grow Remote Route for Site Settings */
	private string $publisher_dashboard;

	/** @var string $grow_site_id Grow Site Id */
	private string $grow_site_id;

	/** @var string $domain Domain of the site */
	private string $domain;

	/** @var string $path Path of the site */
	private string $path;

	/** @var string $encoded_site_title WodPress Site Title */
	private string $encoded_site_title;

	/** @var ViewLoaderInterface $view_loader */
	private ViewLoaderInterface $view_loader;

	/** @var string Current plugin version */
	private string $version;

	/**
	 * Sets up the Admin Page
	 *
	 * @param Repository $repository Used for the Grow Remote API Base
	 */
	public function __construct( Repository $repository ) {
		$this->actions             = [
			new HookArguments( 'admin_menu', 'register' ),
		];
		$this->filters             = [
			new HookArguments( FrontendData::GET_ADMIN_DATA_FILTER, 'with_grow_remote_api_data' ),
			new HookArguments( FrontendData::GET_ADMIN_DATA_FILTER, 'with_page_copy' ),
			new HookArguments( FrontendData::GET_ADMIN_DATA_FILTER, 'with_site_info' ),
			new HookArguments( 'plugin_action_links_' . GROW_PLUGIN_BASENAME, 'with_plugin_action_links' ),
		];
		$this->remote_api_root     = $repository->get_grow_remote()->get_api_root();
		$this->publisher_dashboard = $repository->get_grow_remote()->get_publisher_dashboard();
		$this->grow_site_id        = $repository->get_options()->get_grow_site_id();
		$this->domain              = $repository->get_environment()->get_domain();
		$this->path                = $repository->get_environment()->get_path();
		$this->encoded_site_title  = rawurlencode($repository->get_environment()->get_site_title());
		$this->view_loader         = $repository->get_view_loader();
		$this->version             = $repository->get_config()->get_version();

		if ( ! empty( $repository->get_options()->get_grow_show_need_connection_message() ) ) {
			$this->actions[] = new HookArguments( 'admin_notices', 'output_grow_need_connection_message' );
			$repository->get_options()->set_grow_show_need_connection_message( '' );
		}
	}

	/**
	 * Output the admin message to prompt user to connect to Grow.
	 *
	 * @return void
	 */
	public function output_grow_need_connection_message() {
		$message = __( 'Your site must be connected to Grow to access that functionality. Please connect to Grow using the button below and then try again.', 'grow-for-wp' );
		printf( '<div class="notice notice-error notice-grow"><p><strong>%1$s</strong></p></div>', esc_html( $message ) );
	}

	/**
	 * Register the custom Page
	 *
	 * @return void
	 */
	public function register() {
		WordPress::add_menu_page( self::PAGE_TITLE, self::PAGE_TITLE, self::CAPABILITY_LEVEL, self::MENU_SLUG, [ $this, 'render' ], self::MENU_ICON );
	}

	/**
	 * Renders the View for the page
	 *
	 * @return void
	 */
	public function render() {
		echo wp_kses( $this->view_loader->get_view( 'admin-page-view.php', $this->get_view_args() ), $this->view_loader->get_allowed_tags() );
	}

	/**
	 * @return array
	 */
	private function get_view_args() : array {
		$is_connected = ! empty( $this->grow_site_id );
		$page_copy    = $this->get_page_copy();
		$active_copy  = $is_connected ? 'connected' : 'disconnected';
		return [
			'is_connected'          => $is_connected,
			'connected_site'        => $page_copy[ $active_copy ]['connectedSite'],
			'title'                 => $page_copy[ $active_copy ]['title'],
			'subtitle'              => $page_copy[ $active_copy ]['subtitle'],
			'primary_button_text'   => $page_copy[ $active_copy ]['primaryButtonText'],
			'primary_button_href'   => $this->publisher_dashboard . '/wp-auth?domain=' . $this->domain . '&path=' . $this->path . '&title=' . $this->encoded_site_title,
			'secondary_button_href' => $this->publisher_dashboard,
			'secondary_button_text' => $page_copy[ $active_copy ]['secondaryButtonText'],
			'features'              => $this->get_features_copy(),
			'integrations'          => $this->get_integrations_copy(),
		];
	}

	/**
	 * @param mixed $data Frontend Admin Data to add the remote api data to
	 *
	 * @return mixed
	 */
	public function with_page_copy( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		$data['adminPageCopy'] = $this->get_page_copy();
		return $data;
	}

	/**
	 * @return array[]
	 */
	private function get_page_copy() : array {
		return [
			'authenticated' => [
				'connectedSite'       => __('Grow is currently running on ', 'grow-for-wp') . $this->domain,
				'title'               => __("You're currently connected to your Grow Publisher Account", 'grow-for-wp'),
				'subtitle'            => __('Staying logged into your account lets you access features like Spotlight Subscribe and Exclusive Content.', 'grow-for-wp'),
				'primaryButtonText'   => __('Manage My Account', 'grow-for-wp'),
				'secondaryButtonText' => __('Sign Out', 'grow-for-wp'),
			],
			'connected'     => [
				'connectedSite'       => __('Grow is currently running on ', 'grow-for-wp') . $this->domain,
				'title'               => __('This Site is Connected to Grow but This User Does not have Access', 'grow-for-wp'),
				'subtitle'            => __('Grow is still running on your site but you will not be able to make adjustments until you log in.', 'grow-for-wp'),
				'primaryButtonText'   => __('Log In', 'grow-for-wp'),
				'secondaryButtonText' => __('Disconnect', 'grow-for-wp'),
			],
			'disconnected'  => [
				'connectedSite'       => null,
				'title'               => __('Unlock the full power of Grow!', 'grow-for-wp'),
				'subtitle'            => __('Get all the features you need, and more, by upgrading to a full Grow account.', 'grow-for-wp'),
				'primaryButtonText'   => __('Connect or Create an Account', 'grow-for-wp'),
				'secondaryButtonText' => null,
			],
			'featureLinks'  => $this->get_feature_links(),
		];
	}

	/**
	 * Gets the content for the features on the admin page
	 *
	 * @return array[]
	 */
	private function get_features_copy() : array {
		return [
			[
				'slug'           => 'spotlight',
				'image_source'   => AssetLoader::compose_asset_source('feature-spotlight', 'png', $this->version),
				'title'          => 'Spotlight and Pop Up Subscribe Forms',
				'description'    => 'Display a Subscribe form on your site. When a user scrolls into view, it darkens the rest of the page, highlighting on the subscribe form.',
				'settings_route' => '/subscribe',
				'help_link'      => 'https://help.grow.me/hc/en-us/categories/4416451216667-Subscribe',
			],
			[
				'slug'           => 'exclusive-content',
				'image_source'   => AssetLoader::compose_asset_source('feature-exclusive-content', 'png', $this->version),
				'title'          => 'Exclusive Content',
				'description'    => 'Choose what content is exclusive to users who log into Grow and gain a Subscriber!',
				'settings_route' => '/settings/exclusive-content',
				'help_link'      => 'https://help.grow.me/hc/en-us/categories/4422789378459-Exclusive-Content',
			],
			[
				'slug'           => 'main-menu',
				'image_source'   => AssetLoader::compose_asset_source('feature-main-menu', 'png', $this->version),
				'title'          => 'Main Menu and Save Button',
				'description'    => 'Allows your readers to save your content while providing them quick and easy access to bookmarks and search.',
				'settings_route' => '/settings/appearance',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/4425153153819-Save-Subscribe-with-Grow',
			],
			[
				'slug'           => 'inline-recommended-content',
				'image_source'   => AssetLoader::compose_asset_source('feature-inline-recommended-content', 'png', $this->version),
				'title'          => 'Inline Recommended Content',
				'description'    => 'Add Inline Recommended Content to your posts to optimize click through rates!',
				'settings_route' => '/settings/recommendations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/5078562679451-Inline-Recommended-Content-Widget',
			],
			[
				'slug'           => 'header-carousel',
				'image_source'   => AssetLoader::compose_asset_source('feature-header-carousel', 'png', $this->version),
				'title'          => 'Header Carousel',
				'description'    => 'Displays recommended content at the top of your site. Available only for desktop.',
				'settings_route' => '/settings/recommendations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/4876520937755-Header-Carousel-on-Desktop-Recommended-Content',
			],
			[
				'slug'           => 'whats-next',
				'image_source'   => AssetLoader::compose_asset_source('feature-whats-next', 'png', $this->version),
				'title'          => "What's Next Recommended Content",
				'description'    => 'Appears near the bottom of your post on mobile. It recommends the next post your users should read and improves click through rates.',
				'settings_route' => '/settings/recommendations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/5078587063323-What-s-Next-on-Mobile-Recommended-Content',
			],
			[
				'slug'           => 'search',
				'image_source'   => AssetLoader::compose_asset_source('feature-search', 'png', $this->version),
				'title'          => 'Search',
				'description'    => 'A user can search for any of your posts within your site.',
				'settings_route' => '/settings/search',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/10278535640347',
			],
			[
				'slug'           => 'recipe-integration',
				'image_source'   => AssetLoader::compose_asset_source('feature-recipe-integration', 'png', $this->version),
				'title'          => 'Recipe Card Integration',
				'description'    => 'Add a Grow Save button to your favorite recipe card plugin: Create, Tasty, or WPRM.',
				'settings_route' => '/settings/recipe-integration',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/4425151745819-Grow-Save-Button-in-Recipe-Cards',
			],
			[
				'slug'           => 'bookmarks',
				'image_source'   => AssetLoader::compose_asset_source('feature-bookmarks', 'png', $this->version),
				'title'          => 'Bookmarks',
				'description'    => 'Adds a Grow Save button to your posts. Users will favorite and save posts that they want to come back to.',
				'settings_route' => '/bookmarks',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/7953889354267-How-to-Save',
			],
		];
	}

	/**
	 * Get the content for the integration features section
	 *
	 * @return array
	 */
	private function get_integrations_copy() : array {
		return [
			[
				'slug'           => 'convertkit',
				'image_source'   => AssetLoader::compose_asset_source('feature-convertkit', 'png', $this->version),
				'title'          => 'ConvertKit',
				'description'    => 'Integrate Grow Subscribe features with Convertkit tags, forms, and sequences!',
				'settings_route' => '/settings/integrations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/5400520490011',
				'type'           => 'integration',
			],
			[
				'slug'           => 'zapier',
				'image_source'   => AssetLoader::compose_asset_source('feature-zapier', 'png', $this->version),
				'title'          => 'Zapier',
				'description'    => 'Integrate Grow Subscribe features with Zapier tags, forms, and sequences!',
				'settings_route' => '/settings/integrations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/4425208605467',
				'type'           => 'integration',
			],
			[
				'slug'           => 'mailerlite',
				'image_source'   => AssetLoader::compose_asset_source('feature-mailerlite', 'png', $this->version),
				'title'          => 'MailerLite',
				'description'    => 'Integrate Grow Subscribe features with MailerLite tags, forms, and sequences!',
				'settings_route' => '/settings/integrations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/6837633614107',
				'type'           => 'integration',
			],
			[
				'slug'           => 'mailchimp',
				'image_source'   => AssetLoader::compose_asset_source('feature-mailchimp', 'png', $this->version),
				'title'          => 'Mailchimp',
				'description'    => 'Integrate Grow Subscribe features with Mailchimp tags, forms, and sequences!',
				'settings_route' => '/settings/integrations',
				'help_link'      => 'https://help.grow.me/hc/en-us/articles/6825171396507',
				'type'           => 'integration',
			],
		];
	}

	/**
	 * Get the urls needed to handle the links for the features and integrations
	 *
	 * @return array
	 */
	private function get_feature_links() : array {
		return array_reduce(array_merge($this->get_features_copy(), $this->get_integrations_copy()), function( $carry, $feature ) {
			$carry[ $feature['slug'] ] = [
				'settingsRoute' => $feature['settings_route'],
				'helpLink'      => $feature['help_link'],
			];
			return $carry;
		});
	}

	/**
	 * @param mixed $data Frontend Admin Data to add the remote api data to
	 *
	 * @return mixed
	 */
	public function with_grow_remote_api_data( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		$data['growRemote'] = [
			'apiRoot'            => $this->remote_api_root,
			'publisherDashboard' => $this->publisher_dashboard,
		];
		return $data;
	}

	/**
	 * Add the site domain to the admin data
	 *
	 * @param mixed|array<string, string|mixed> $data Existing data to add to
	 *
	 * @return mixed
	 */
	public function with_site_info( $data ) {
		if ( is_array ($data) ) {
			if ( ! isset( $data['siteDomain'] ) ) {
				$data['siteDomain'] = $this->domain;
			}
			if ( ! isset( $data['siteBasePath'] ) ) {
				$data['siteBasePath'] = $this->path;
			}
			if ( ! isset($data['siteTitle'] ) ) {
				$data['siteTitle'] = $this->encoded_site_title;
			}
			if ( ! isset ( $data['growSiteId'] ) && $this->grow_site_id ) {
				$data['growSiteId'] = $this->grow_site_id;
			}
		}
		return $data;
	}

	/**
	 * @param mixed|array<string|int, string|mixed> $actions Existing action links
	 *
	 * @return mixed
	 */
	public function with_plugin_action_links( $actions ) {
		if ( is_array($actions ) ) {
			$actions[] = '<a href="' . WordPress::admin_url( 'admin.php?page=' . self::MENU_SLUG ) . '">' . __('Settings', 'grow-for-wp') . '</a>';
		}
		return $actions;
	}

	/** @var string MENU_ICON Base 64 encoded Grow Logo Data URI */
	private const MENU_ICON = 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMTczIDE1NCIgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiNhN2FhYWQiPjxwYXRoIGZpbGw9IiNhN2FhYWQiIGQ9Ik00NiAzMWMtMTQtNC0yMS02LTM5LTYtMyA5LTM0IDk5IDY0IDExMS0xOC0xNy01NS01NS0yNS0xMDV6IiAvPjxwYXRoIGZpbGw9IiNhN2FhYWQiIGQ9Ik04NyAwQzcyIDExIDQgNjIgNzUgMTI4Yy0xMi00NyAyLTg1IDM5LTEwMUMxMDMgMTEgODcgMCA4NyAweiIgLz48cGF0aCBmaWxsPSIjYTdhYWFkIiBkPSJNMTY2IDI1Yy0yNiAwLTExMyAxMS04MCAxMTIgNjUgMCAxMDUtMzcgODAtMTEyeiIgLz48L3N2Zz4K';
}
