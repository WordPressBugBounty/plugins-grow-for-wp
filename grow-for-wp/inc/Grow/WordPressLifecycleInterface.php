<?php

namespace Grow;

interface WordPressLifecycleInterface {

	/**
	 * Register the hooks for a given class
	 *
	 * @param HasWordpressHooksInterface $class Class with Hooks to add
	 *
	 * @return void
	 */
	public function collect( HasWordpressHooksInterface $class): void;

	/**
	 * Register the hooks for an array of classes
	 *
	 * @param HasWordpressHooksInterface[] $classes Array of classes with Hooks to add
	 * @return void
	 */
	public function collect_many( array $classes): void;

	/**
	 * Registers and creates a hook to be run when plugin is activated
	 *
	 * @return void
	 */
	public function register_activation() : void;

	/**
	 * Register and create a hook to be run when plugin is deactivated
	 *
	 * @return void
	 */
	public function register_deactivation() : void;
}
