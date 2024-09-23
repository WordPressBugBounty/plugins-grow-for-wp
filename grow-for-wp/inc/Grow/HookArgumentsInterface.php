<?php

namespace Grow;

/**
 * Representation of a Hook that is portable
 *
 * @since 0.0.1
 */
interface HookArgumentsInterface {
	/**
	 * @return array<int, int|string>
	 */
	public function get_args() : array;

	/**
	 * @return string
	 */
	public function get_hook_name() : string;

	/**
	 * @return string
	 */
	public function get_method_name() : string;

	/**
	 * @return int
	 */
	public function get_priority() : int;

	/**
	 * @return int
	 */
	public function get_accepted_args() : int;
}
