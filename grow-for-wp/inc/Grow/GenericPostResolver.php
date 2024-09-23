<?php

namespace Grow;

use WP_Post;
use WP_Term;

/**
 * This is the content resolver for the post type of post
 *
 * It will also work for any custom post types that declare support for categories
 */
class GenericPostResolver implements ContentResolverInterface {

	/** @var WP_Post The class instance of this WordPress Post */
	private WP_Post $post;

	/**
	 * Initialize the class
	 *
	 * @param WP_Post $post Post to get data from
	 */
	public function __construct( WP_Post $post ) {
		$this->post = $post;
	}

	/**
	 * Turn the Object into an array with the required data
	 *
	 * @return array<string, int|array<int, array<string, int>>>
	 */
	public function resolve() : array {
		return [
			'ID'         => $this->get_id(),
			'categories' => $this->get_categories(),
		];
	}

	/**
	 * Get the post ID
	 *
	 * @return int
	 */
	public function get_id() : int {
		return $this->post->ID;
	}

	/**
	 * Get the categories for the post
	 *
	 * @return array<int, array<string, int>>
	 */
	public function get_categories() : array {
		$categories = WordPress::get_the_category($this->post->ID);
		if ( ! is_array( $categories ) ) {
			return [];
		}

		$post_categories = [];
		foreach ( $categories as $category ) {
			if ( ! ( $category instanceof WP_Term ) ) {
				continue;
			}

			$post_categories[] = [ 'ID' => $category->term_id ];
		}

		return $post_categories;
	}
}
