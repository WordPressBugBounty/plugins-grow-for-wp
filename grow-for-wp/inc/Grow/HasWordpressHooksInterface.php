<?php

namespace Grow;

interface HasWordpressHooksInterface {
	/**
	 * @return array<Hook>
	 */
	public function get_filters() : array;

	/**
	 * @return array<Hook>
	 */
	public function get_actions() : array;
}
