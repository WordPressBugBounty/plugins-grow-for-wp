<?php

namespace Grow;

/**
 * Handles outputting the script wrapper.
 *
 * @since 6.4.0
 */
class AdsScript implements HasWordpressHooksInterface {
	use HasWordpressHooksTrait;

	/** @var string $grow_site_uuid Grow Site UUID */
	private string $grow_site_uuid;

	/** @var string $grow_journey_status Journey enabled/disabled status */
	private string $grow_journey_status;

	/** @var EnvironmentInterface Provides access to environment related information */
	private EnvironmentInterface $environment;

	/**
	 * Set up the class
	 *
	 * @param Repository $repository Gives access to config and options
	 */
	public function __construct( Repository $repository ) {
		$this->grow_site_uuid      = $repository->get_options()->get_grow_site_uuid();
		$this->grow_journey_status = $repository->get_options()->get_grow_journey_status();
		$this->environment         = $repository->get_environment();

		$this->actions = [
			new HookArguments( 'wp_enqueue_scripts', 'enqueue_ads_script', 11 ),
		];
		$this->filters = [
			new HookArguments( 'script_loader_tag', 'add_script_attributes', 11, 2 ),
		];

		// Handle WPRocket integration, if it is installed.
		if ( $this->environment->get_has_wp_rocket() ) {
			$this->filters[] = new HookArguments( 'rocket_delay_js_exclusions', 'add_rocket_js_exclusions' );
			$this->filters[] = new HookArguments( 'rocket_exclude_defer_js', 'add_rocket_js_exclusions' );
			$this->filters[] = new HookArguments( 'rocket_defer_inline_exclusions', 'add_rocket_js_exclusions' );
			$this->filters[] = new HookArguments( 'rocket_minify_excluded_external_js', 'add_rocket_js_exclusions_by_domain' );
		}
	}

	/**
	 * Enqueue Mediavine Script Wrapper.
	 *
	 * @return void
	 */
	public function enqueue_ads_script() {
		// Check if MCP already doing this and stop gracefully, if so.
		if ( $this->environment->get_has_mcp() && WordPress::wp_script_is( 'mv-script-wrapper' ) ) {
			return;
		}

		if ( empty( $this->grow_site_uuid ) ) {
			return;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return;
		}

		// Don't show ads when editor is logged into WordPress.
		if ( WordPress::current_user_can( 'manage_options' ) ) {
			return;
		}

		WordPress::enqueue_script( 'mv-script-wrapper', 'https://scripts.scriptwrapper.com/tags/' . $this->grow_site_uuid . '.js' );
	}

	/**
	 * Adds required attributes to embedded script.
	 *
	 * @param string $tag html tag of script output.
	 * @param string $handle wp id of script for enqueue.
	 *
	 * @return string
	 */
	public function add_script_attributes( $tag, $handle ) {
		if ( 'mv-script-wrapper' !== $handle ) {
			return $tag;
		}

		$tag = str_replace( ' src', ' async="async" src', $tag );
		$tag = str_replace( ' src', ' data-noptimize="1" src', $tag );
		// Disable Cloudflare Rocket Loader.
		// @see https://developers.cloudflare.com/speed/optimization/content/rocket-loader/ignore-javascripts/ .
		$tag = str_replace( ' src', ' data-cfasync="false" src', $tag );

		return $tag;
	}

	/**
	 * Exclude scripts from WP Rocket JS delay and defer.
	 *
	 * @param array $excluded List of excluded JS config.
	 *
	 * @return array
	 */
	public function add_rocket_js_exclusions( $excluded = array() ) {
		// Fail gracefully in case WP Rocket decides to change how the parameter
		// gets passed in the future.
		if ( ! is_array( $excluded ) ) {
			return $excluded;
		}

		$excluded[] = 'journeymv';
		$excluded[] = 'scriptwrapper';

		return $excluded;
	}

	/**
	 * Exclude scripts from WP Rocket JS combine and minify.
	 *
	 * @param array $excluded List of excluded JS domains.
	 *
	 * @return array
	 */
	public function add_rocket_js_exclusions_by_domain( $excluded = array() ) {
		// Fail gracefully in case WP Rocket decides to change how the parameter
		// gets passed in the future.
		if ( ! is_array( $excluded ) ) {
			return $excluded;
		}

		$excluded[] = 'journeymv.com';
		$excluded[] = 'scriptwrapper.com';

		return $excluded;
	}
}
