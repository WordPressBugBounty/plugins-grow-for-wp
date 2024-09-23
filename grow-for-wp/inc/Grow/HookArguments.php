<?php

namespace Grow;

/**
 * Representation of a Hook that is portable
 *
 * @since 0.0.1
 */
class HookArguments implements HookArgumentsInterface {

	/** @var string Name for this hook */
	private string $hook_name;

	/** @var string Callback to be fired by hook */
	private $method_name;

	/** @var int Priority given to this callback, higher numbers will be called later */
	private int $priority;

	/** @var int The number of args teh callback will accept */
	private int $accepted_args;

	/**
	 * @param string $hook_name Name for this hook
	 * @param string $method_name Name for the method used in the callback
	 * @param int    $priority Priority given to this callback, higher numbers will be called later
	 * @param int    $accepted_args The number of args teh callback will accept
	 */
	public function __construct(
		string $hook_name,
		string $method_name,
		int $priority = 10,
		int $accepted_args = 1
	) {
		$this->hook_name     = $hook_name;
		$this->method_name   = $method_name;
		$this->priority      = $priority;
		$this->accepted_args = $accepted_args;
	}

	/**
	 * @return array<int, int|string>
	 */
	public function get_args() : array {
		return [
			$this->hook_name,
			$this->method_name,
			$this->priority,
			$this->accepted_args,
		];
	}

	/**
	 * @return string
	 */
	public function get_hook_name() : string {
		return $this->hook_name;
	}

	/**
	 * @return string
	 */
	public function get_method_name() : string {
		return $this->method_name;
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

}
