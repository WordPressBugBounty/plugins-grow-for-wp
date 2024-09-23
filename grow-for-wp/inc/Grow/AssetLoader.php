<?php

namespace Grow;

/**
 * The AssetLoader is responsible for loading scripts, images, and styles
 *
 * AssetLoader should not modify assets themselves but ensure that they are
 * loaded at the right time and in the right place. If existing scripts need
 * to be modified (i.e. made async) this class will be responsible for that too
 *
 * @since 0.0.1
 */
class AssetLoader implements HasWordpressHooksInterface {

	use HasWordpressHooksTrait;

	/** @var string Handle for the plugin admin javascript asset */
	const ADMIN_SCRIPT_SLUG = 'grow-admin-script';

	/** @var string Handle for the plugin admin javascript asset */
	const ADMIN_STYLE_SLUG = 'grow-admin-style';

	/** @var string Handle for the plugin public javascript asset */
	const PUBLIC_SCRIPT_SLUG = 'grow-public-script';

	/** @var string Directory where the distribution assets are kept */
	const ASSET_DIRECTORY = GROW_PLUGIN_DIR_URL . 'assets/dist/';

	/** @var string Name of the filter to enable development assets */
	public const DEVELOPMENT_MODE_ENABLED_FILTER = 'grow_development_mode_enabled';

	/** @var string Current plugin version */
	private string $version;

	/** @var string Id For the Site in Grow Remote */
	private string $grow_site_id;

	/** @var string  */
	private string $grow_remote_script_root;

	/** @var bool  */
	private bool $development_mode_enabled;

	/**
	 * Sets up the class
	 *
	 * @param Repository $repository Gives access to plugin version
	 */
	public function __construct( Repository $repository ) {
		$this->actions                  = [
			new HookArguments('admin_enqueue_scripts', 'enqueue_admin_assets' ),
			new HookArguments('wp_print_footer_scripts', 'output_grow_initializer' ),
		];
		$this->version                  = $repository->get_config()->get_version();
		$this->grow_site_id             = $repository->get_options()->get_grow_site_id();
		$this->grow_remote_script_root  = $repository->get_grow_remote()->get_script_root();
		$this->development_mode_enabled = boolval(WordPress::apply_filters( self::DEVELOPMENT_MODE_ENABLED_FILTER, false ));
	}

	/**
	 * Register the public assets
	 *
	 * @return void
	 */
	public function enqueue_public_assets() : void {
		WordPress::enqueue_script( self::PUBLIC_SCRIPT_SLUG, '', [], $this->version, true );
	}

	/**
	 * Register and enqueue admin assets
	 *
	 * @param string $hook_suffix String appended to hooks to be run just for this admin page, useful for identifying the page
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook_suffix ) : void {
		if ( ! str_contains( $hook_suffix, '_grow' ) ) {
			// Grow scripts and styles should only be added to Grow pages.
			return;
		}
		$js_src  = $this->development_mode_enabled ? self::ASSET_DIRECTORY . 'dev-entry.js' : self::compose_asset_source('admin', 'js', $this->version );
		$css_src = $this->development_mode_enabled ? self::ASSET_DIRECTORY . 'dev-entry.css' : self::compose_asset_source('admin', 'css', $this->version );
		WordPress::enqueue_script( self::ADMIN_SCRIPT_SLUG, $js_src, [], $this->version, true );
		WordPress::enqueue_style(self::ADMIN_STYLE_SLUG, $css_src, [], $this->version);
	}


	/**
	 * Echos the grow script with the appropriate values
	 *
	 * @return void
	 */
	public function output_grow_initializer() : void {
		if ( ! $this->should_output_grow_initializer() ) {
			return;
		}
		echo '<script data-grow-initializer="">!(function(){window.growMe||((window.growMe=function(e){window.growMe._.push(e);}),(window.growMe._=[]));var e=document.createElement("script");(e.type="text/javascript"),(e.src="' . esc_url( $this->grow_remote_script_root ) . '/main.js"),(e.defer=!0),e.setAttribute("data-grow-faves-site-id","' . esc_attr( $this->grow_site_id ) . '");var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t);})();</script>'; // @codingStandardsIgnoreLine

	}

	/**
	 * Check if the Grow Script initializer should be output
	 *
	 * @return bool
	 */
	public function should_output_grow_initializer() : bool {
		return ! empty( $this->grow_site_id );
	}

	/**
	 * Build the url for the source of the asset
	 *
	 * @param string $filename the name of the asset file
	 * @param string $extension the extension of the asset file
	 * @param string $version version to be added to file name
	 *
	 * @return string
	 */
	public static function compose_asset_source( string $filename, string $extension, string $version ) : string {
		return sprintf( '%s%s.%s.%s', self::ASSET_DIRECTORY, $filename, $version, $extension );
	}
}
