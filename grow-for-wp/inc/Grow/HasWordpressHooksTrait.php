<?php

namespace Grow;

/**
 * This is a trait to add properties to classes to make sure they work with the
 * WordPress lifecycle class
 *
 * @since 0.0.1
 */
trait HasWordpressHooksTrait {
	/** @var array<HookArgumentsInterface> */
	private array $filters = [];

	/** @var array<HookArgumentsInterface> */
	private array $actions = [];

	/**
	 * @return array<Hook>
	 */
	public function get_filters() : array {
		return array_map(function( $hook_arguments ) {
			return new Hook($hook_arguments->get_hook_name(), [ $this, $hook_arguments->get_method_name() ], $hook_arguments->get_priority(), $hook_arguments->get_accepted_args());
		}, $this->filters);
	}

	/**
	 * @return array<Hook>
	 */
	public function get_actions() : array {
		return array_map(function( $hook_arguments ) {
			return new Hook($hook_arguments->get_hook_name(), [ $this, $hook_arguments->get_method_name() ], $hook_arguments->get_priority(), $hook_arguments->get_accepted_args());
		}, $this->actions);
	}

}
