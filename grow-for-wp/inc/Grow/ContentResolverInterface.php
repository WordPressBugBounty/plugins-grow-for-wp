<?php

namespace Grow;

/**
 * This interface defines a contract that will allow multiple different post types or other sources of content to be
 * normalized into a set of data that will enable compatibility with Grow.
 */
interface ContentResolverInterface {

	/**
	 * Turn the information about the content we have  into a normalized array
	 *
	 * @return array<string, int|array<int, array<string, int>>>
	 */
	public function resolve() : array;
}
