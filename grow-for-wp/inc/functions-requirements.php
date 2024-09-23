<?php
/**
 * Functions that check the requirements of the Plugin against the environment it is running in
 *
 * Functions in this file are called before anything else to ensure we gracefully exit instead of crashing. All code here
 * should be compatible with PHP 5.6
 *
 * @since 0.0.1
 */

/**
 * Checks for a minimum version
 *
 * @param string     $minimum Minimum version to check
 * @param int|string $compare 'php' to check against PHP, 'wp' to check against WP, or a specific
 *                            value to check against
 * @return boolean True if the version is compatible
 */
function grow_for_wp_is_compatible_check( $minimum, $compare = 0 ) {
	if ( 'php' === $compare ) {
		$compare = PHP_VERSION;
	}
	if ( 'wp' === $compare ) {
		global $wp_version;
		$compare = $wp_version;
	}

	if ( version_compare($compare, $minimum, '<') ) {
		return false;
	}

	return true;
}

/**
 * Checks if Grow is compatible
 *
 * @param boolean $return_errors Should the errors found be returned instead of false
 * @return boolean|array<string, string> True if compatible. False or array of errors if not compatible
 */
function grow_for_wp_is_compatible( $return_errors = false ) {
	$minimum_wp      = '5.2';
	$deprecated_wp   = '6.0';
	$minimum_php     = '7.4';
	$deprecated_php  = '8.0';
	$recommended_php = '8.1';
	$errors          = [];

	if ( ! grow_for_wp_is_compatible_check($minimum_php, 'php') ) {
		$errors['php']             = $minimum_php;
		$errors['recommended_php'] = $recommended_php;
	}

	if ( ! grow_for_wp_is_compatible_check($minimum_wp, 'wp') ) {
		$errors['wp'] = $minimum_wp;
	}

	if ( $return_errors ) {
		if ( ! grow_for_wp_is_compatible_check($deprecated_php, 'php') ) {
			$errors['deprecated_php']  = $deprecated_php;
			$errors['recommended_php'] = $recommended_php;
		}

		if ( ! grow_for_wp_is_compatible_check($deprecated_wp, 'wp') ) {
			$errors['deprecated_wp'] = $deprecated_wp;
		}
	}

	if ( ! empty($errors) ) {
		if ( $return_errors ) {
			return $errors;
		}
		return false;
	}

	return true;
}
