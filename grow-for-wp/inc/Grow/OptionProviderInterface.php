<?php

namespace Grow;

interface OptionProviderInterface {

	/**
	 * @param string $key Key of option to update
	 * @param mixed  $value Valye to set
	 * @param bool   $autoload Whether the option should be autoloaded by WordPress, don't use this without a good reason
	 *
	 * @return bool
	 */
	public static function update_option( string $key, $value, bool $autoload = false ) : bool;

	/**
	 * @param string $key Key of option to get
	 * @param mixed  $default Value to return if the stored value is empty
	 *
	 * @return mixed
	 */
	public static function get_option( string $key, $default = null );
}
