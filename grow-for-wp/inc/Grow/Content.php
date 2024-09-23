<?php

namespace Grow;

use WP_Post;

/**
 * This class is a representation of the current content viewed in the request
 *
 * It accepts a resolver to get the data needed for Grow
 */
class Content implements HasWordpressHooksInterface {

	use HasWordpressHooksTrait;

	/**
	 * @var int ID for this content
	 */
	private int $ID;

	/** @var array<int, array<string, int>> d */
	private array $categories;

	/** @var string $grow_site_uuid Grow Site UUID */
	private string $grow_site_uuid;

	/** @var string $grow_journey_status Journey enabled/disabled status */
	private string $grow_journey_status;

	/** @var EnvironmentInterface Provides access to environment related information */
	private EnvironmentInterface $environment;

	/**
	 * Set up hooks
	 *
	 * @param Repository $repository Gives access to config and options
	 */
	public function __construct( Repository $repository ) {
		$this->grow_site_uuid      = $repository->get_options()->get_grow_site_uuid();
		$this->grow_journey_status = $repository->get_options()->get_grow_journey_status();
		$this->environment         = $repository->get_environment();

		$this->filters = [
			new HookArguments( 'wp_head', 'resolve_content' ),
			new HookArguments( FrontendData::GET_DATA_FILTER, 'add_data' ),
			new HookArguments( 'post_class', 'add_post_class', 10 ),
			new HookArguments( 'body_class', 'add_body_class', 11 ),
			new HookArguments( 'mv_test_is_plugin_active_mediavine-control-panel', 'add_trellis_sidebar_slot', 11 ),
		];

		$this->actions = [
			new HookArguments( 'mv_create_modify_card_style_hooks', 'modify_create_card_style_hooks', 11 ),
			new HookArguments( 'mv_create_list_after_single', 'output_create_list_ads', 11, 3 ),
			new HookArguments( 'mv_create_list_after_row', 'output_create_list_ads_grid', 11, 3 ),
		];
	}

	/**
	 * Adds data about the content to the frontend dat to be output
	 *
	 * @param mixed $data The current data that we will add content data to
	 *
	 * @return array<string, mixed>|mixed
	 */
	public function add_data( $data ) {
		if ( ! isset( $this->ID ) || ! isset( $this->categories ) ) {
			return $data;
		}
		if ( is_array($data) && empty($data['content']) ) {
			$data['content'] = [
				'ID'         => $this->ID,
				'categories' => $this->categories,
			];
		}

		return $data;
	}

	/**
	 * Identify what type of content we are currently viewing, then normalize the data
	 *
	 * @return void
	 */
	public function resolve_content() : void {
		$unresolved_content = WordPress::get_post();
		if ( WordPress::is_singular() && $unresolved_content instanceof WP_Post ) {
			$resolver = new GenericPostResolver( $unresolved_content );
		}
		if ( ! isset( $resolver ) ) {
			return;
		}
		$data             = $resolver->resolve();
		$this->ID         = intval($data['ID']);
		$this->categories = is_array($data['categories']) ? $data['categories'] : [];
	}

	/**
	 * Adds a CSS class to all posts' wrappers for script targeting.
	 *
	 * @param array $classes List of classes currently defined.
	 *
	 * @return array
	 */
	public function add_body_class( $classes ) {
		// Only add for singular content types (ex: post, page, etc)
		if ( ! WordPress::is_singular() ) {
			return $classes;
		}

		// Don't duplicate if it already exists.
		if ( in_array( 'grow-content-body', $classes, true ) ) {
			return $classes;
		}

		$classes[] = 'grow-content-body';
		return $classes;
	}

	/**
	 * Add class 'grow-content-main' to all posts' wrappers for ad targeting.
	 *
	 * @param array $classes Classes to be used.
	 *
	 * @return array Classes to be used.
	 */
	public function add_post_class( $classes ) {
		if ( is_main_query() && is_singular() && ! in_array( 'grow-content-main', $classes, true ) ) {
			$classes[] = 'grow-content-main';
		}
		return $classes;
	}

	/**
	 * Determines if `.mv-sticky-slot` should be rendered in Trellis sidebar.
	 *
	 * @return bool
	 */
	public function add_trellis_sidebar_slot() {
		if ( $this->environment->get_has_mcp() ) {
			return true;
		}

		if ( empty( $this->grow_site_uuid ) ) {
			return false;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Modify the Create card output to inject ad slots where they are expected.
	 *
	 * @param string $card_style The style type of the card.
	 *
	 * @return void
	 */
	public function modify_create_card_style_hooks( $card_style ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if ( ! $this->environment->get_has_create() ) {
			return;
		}

		if ( empty( $this->grow_site_uuid ) ) {
			return;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return;
		}

		// Only apply workaround for older versions of Create.
		if ( version_compare( $this->environment->get_create_version(), '1.9.6', '>' ) ) {
			return;
		}

		$priority = WordPress::has_action( 'mv_create_card_content', 'Mediavine\\Create\\Creations_Views_Hooks::mv_create_ad_div' );

		if ( false === $priority ) {
			return;
		}

		$priority = intval( $priority );
		WordPress::remove_action( 'mv_create_card_content', 'Mediavine\\Create\\Creations_Views_Hooks::mv_create_ad_div', $priority );
		WordPress::add_action( 'mv_create_card_content', [ $this, 'get_create_ad_div' ], $priority );
	}

	/**
	 * Output the Create by Mediavine recipe card ad slot.
	 *
	 * @return void
	 */
	public function get_create_ad_div() {
		//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo WordPress::wp_kses_post( '<div class="mv-create-target mv-create-primary-unit"><div class="mv_slot_target" data-slot="recipe"></div></div>' );
	}

	/**
	 * Insert ad slots into Create by Mediavine recipe lists.
	 *
	 * @param array $args array of arguments
	 * @param int   $i the index of the list item
	 * @param int   $count the total number of list items
	 * @return void
	 */
	public function output_create_list_ads( $args, $i, $count ) {
		if ( empty( $this->grow_site_uuid ) ) {
			return;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return;
		}

		// Only apply workaround for older versions of Create.
		if ( version_compare( $this->environment->get_create_version(), '1.9.6', '>' ) ) {
			return;
		}

		// Ensure there are items in the list.
		if ( empty( $args['creation']['list_items_between_ads'] ) ) {
			return;
		}

		// Ensure we are not on the print page.
		if ( ! empty( $args['print'] ) ) {
			return;
		}

		// Ensure placement is correct.
		if ( 0 !== ( $i + 1 ) % $args['creation']['list_items_between_ads'] ) {
			return;
		}

		// Ensure we aren't at the end of the list.
		if ( ( $i + 1 ) === $count ) {
			return;
		}

		echo '<div class="mv-list-adwrap"><div class="mv_slot_target" data-slot="content"></div></div>';
	}

	/**
	 * Insert ad slots into Create by Mediavine recipe grid.
	 *
	 * @param array $args array of arguments
	 * @param int   $row the row of list items we're on
	 * @param int   $count the total number of list items
	 * @return void
	 */
	public function output_create_list_ads_grid( $args, $row, $count ) {
		if ( empty( $this->grow_site_uuid ) ) {
			return;
		}

		if ( empty( $this->grow_journey_status ) ) {
			return;
		}

		// Only apply workaround for older versions of Create.
		if ( version_compare( $this->environment->get_create_version(), '1.9.6', '>' ) ) {
			return;
		}

		// Ensure there are items in the list.
		if ( empty( $args['creation']['list_items_between_ads'] ) ) {
			return;
		}

		// Ensure we are not on the print page.
		if ( ! empty( $args['print'] ) ) {
			return;
		}

		// Ensure we aren't on the first row.
		if ( 1 === $row ) {
			return;
		}

		// Ensure we are on a correct row.
		if ( 0 !== $row % $args['creation']['list_items_between_ads'] ) {
			return;
		}

		// Ensure not at end of items.
		if ( ( $row * 2 ) >= $count ) {
			return;
		}

		echo '<div class="mv-list-adwrap"><div class="mv_slot_target mv_slot_target-grid" data-slot="content"></div></div>';
	}
}
