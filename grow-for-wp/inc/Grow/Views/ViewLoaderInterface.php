<?php

namespace Grow\Views;

interface ViewLoaderInterface {

	/**
	 * Returns the output of the passed view.
	 *
	 * @param string $view_file Relative path to view file from plugin root
	 * @param array  $args Array that will be passed to the included view
	 * @return string Output from the view
	 */
	public function get_view( string $view_file, array $args = [] );

	/**
	 * Custom version of wp_kses to allow SVG tags.
	 *
	 * @return array
	 */
	public function get_allowed_tags() : array;
}
