<?php

namespace Grow;

/**
 * Representation of a Hook that is portable
 *
 * @since 0.0.1
 */
class Hook implements HookInterface {

	/** @var string Name for this hook */
	private string $hook_name;

	/** @var array<int, string|HasWordpressHooksInterface>|\Closure Callback to be fired by hook */
	private $callback;

	/** @var int Priority given to this callback, higher numbers will be called later */
	private int $priority;

	/** @var int The number of args teh callback will accept */
	private int $accepted_args;

	/**
	 * @param string                                                 $hook_name Name for this hook
	 * @param array<int, string|HasWordpressHooksInterface>|\Closure $callback Callback to be fired by hook
	 * @param int                                                    $priority Priority given to this callback, higher numbers will be called later
	 * @param int                                                    $accepted_args The number of args teh callback will accept
	 */
	public function __construct(
		string $hook_name,
		$callback,
		int $priority = 10,
		int $accepted_args = 1
	) {
		$this->hook_name     = $hook_name;
		$this->callback      = $callback;
		$this->priority      = $priority;
		$this->accepted_args = $accepted_args;
	}

	/**
	 * @return string
	 */
	public function get_hook_name() : string {
		return $this->hook_name;
	}

	/**
	 * @return array<int, string|HasWordpressHooksInterface>|\Closure
	 */
	public function get_callback() {
		return $this->callback;
	}

	/**
	 * @return int
	 */
	public function get_priority() : int {
		return $this->priority;
	}

	/**
	 * @return int
	 */
	public function get_accepted_args() : int {
		return $this->accepted_args;
	}

	/**
	 * @return array<int, int|array<mixed>|string|callable|\Closure>
	 */
	public function get_args() : array {
		return [
			$this->hook_name,
			$this->callback,
			$this->priority,
			$this->accepted_args,
		];
	}
}
