<?php

namespace Grow;

interface HookInterface {

	/**
	 * @return array<int, int|array<mixed>|string|callable>
	 */
	public function get_args() : array;
}
