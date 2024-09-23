<?php
namespace Grow\Views;

use Grow\WordPress;

/**
 * Loads Views
 *
 * @since 0.0.1
 */
class ViewLoader implements ViewLoaderInterface {

	/** @var string  */
	private static string $views_directory = GROW_PLUGIN_DIR . '/inc/Grow/Views/';

	/**
	 * Returns the output of the passed view.
	 *
	 * @param string $view_file Relative path to view file from plugin root
	 * @param array  $args Array that will be passed to the included view
	 * @return string Output from the view
	 */
	public function get_view( string $view_file, array $args = [] ) : string { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$view_path = self::$views_directory . $view_file;
		ob_start();

		try {
			include $view_path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		} catch ( \Exception $exception ) {
			ob_get_clean();
			return '';
		}

		return ob_get_clean() ?: '';
	}

	/**
	 * Custom version of wp_kses to allow SVG tags.
	 *
	 * @return array
	 */
	public function get_allowed_tags() : array {
		$kses_defaults = WordPress::wp_kses_allowed_html();
		$svg_kses      = [
			'svg'     => [
				'class'               => true,
				'aria-hidden'         => true,
				'preserveaspectratio' => true,
				'aria-labelledby'     => true,
				'version'             => true,
				'xmlns'               => true,
				'width'               => true,
				'height'              => true,
				'viewbox'             => true, // <= Must be lower case!
				'fill'                => true,
			],
			'g'       => [ 'fill' => true ],
			'title'   => [ 'title' => true ],
			'path'    => [
				'd'     => true,
				'fill'  => true,
				'class' => true,
			],
			'rect'    => [
				'x'      => true,
				'y'      => true,
				'height' => true,
				'width'  => true,
				'class'  => true,
				'rx'     => true,
				'ry'     => true,
				'fill'   => true,
			],
			'ellipse' => [
				'x'      => true,
				'y'      => true,
				'height' => true,
				'width'  => true,
				'class'  => true,
				'fill'   => true,
			],
			'button'  => [
				'aria-controls' => true,
				'aria-expanded' => true,
			],
		];

		return array_merge_recursive( $kses_defaults, $svg_kses );
	}
}
