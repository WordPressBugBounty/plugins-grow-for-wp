<?php

namespace Grow;

/**
 * The Options class is used to get and set values stored in the wp_options table
 *
 * It has internal caching and restricts what options we allow to code to access
 */
class Options implements OptionsInterface {

	/** @var OptionProviderInterface Allows access to WordPress core option getting and setting functions */
	public OptionProviderInterface $option_provider;

	/**
	 * Set up the class
	 *
	 * @param OptionProviderInterface $option_provider Allows access to WordPress core option getting and settings
	 */
	public function __construct( OptionProviderInterface $option_provider ) {
		$this->option_provider = $option_provider;
	}


	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memoryd value is empty
	 *
	 * @return string
	 */
	public function get_grow_site_id( string $default = '', bool $force = false ) : string {
		return strval($this->get( 'grow_site_id', $default, $force ));
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_site_id( string $value ) : bool {
		return $this->set( 'grow_site_id', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memory value is empty
	 *
	 * @return string
	 */
	public function get_grow_site_uuid( string $default = '', bool $force = false ) : string {
		return strval($this->get( 'grow_site_uuid', $default, $force ));
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_site_uuid( string $value ) : bool {
		return $this->set( 'grow_site_uuid', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memoryd value is empty
	 *
	 * @return string
	 */
	public function get_grow_first_install( string $default = '', bool $force = false ) : string {
		return strval($this->get( 'grow_first_install', $default, $force ));
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_first_install( string $value ) : bool {
		return $this->set( 'grow_first_install', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memoryd value is empty
	 *
	 * @return string
	 */
	public function get_grow_current_version( string $default = '', bool $force = false ) : string {
		return strval($this->get( 'grow_current_version', $default, $force ));
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_current_version( string $value ) : bool {
		return $this->set( 'grow_current_version', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memoryd value is empty
	 *
	 * @return string
	 */
	public function get_grow_first_install_version( string $default = '', bool $force = false ) : string {
		return strval($this->get( 'grow_first_install_version', $default, $force ));
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_first_install_version( string $value ) : bool {
		return $this->set( 'grow_first_install_version', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memoryd value is empty
	 *
	 * @return string
	 */
	public function get_grow_should_redirect_after_activation( string $default = '', bool $force = false ) : string {
		return strval($this->get( 'grow_just_activated', $default, $force ));
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_should_redirect_after_activation( string $value ) : bool {
		return $this->set( 'grow_just_activated', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memory value is empty
	 *
	 * @return string
	 */
	public function get_grow_journey_status( string $default = '', bool $force = false ): string {
		return strval( $this->get( 'grow_journey_status', $default, $force ) );
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_journey_status( string $value ): bool {
		return $this->set( 'grow_journey_status', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memory value is empty
	 *
	 * @return string
	 */
	public function get_grow_ads_txt_method( string $default = '', bool $force = false ): string {
		return strval( $this->get( 'grow_ads_txt_method', $default, $force ) );
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_ads_txt_method( string $value ): bool {
		return $this->set( 'grow_ads_txt_method', $value );
	}

	/**
	 * @param string $default Value to return if the stored value is empty
	 * @param bool   $force check the database again even if we have a value in memory value is empty
	 *
	 * @return string
	 */
	public function get_grow_ads_txt_redirect_check_in_progress( string $default = '', bool $force = false ): string {
		return strval( $this->get( 'grow_ads_txt_redirect_check_in_progress', $default, $force ) );
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_ads_txt_redirect_check_in_progress( string $value ): bool {
		return $this->set( 'grow_ads_txt_redirect_check_in_progress', $value );
	}

	/**
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_show_need_connection_message( string $default = '', bool $force = false ): string {
		return strval( $this->get( 'grow_show_need_connection_message', $default, $force ) );
	}

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_show_need_connection_message( string $value ): bool {
		return $this->set( 'grow_show_need_connection_message', $value );
	}

	/**
	 * @param string $key Key for the option to set
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	private function set( string $key, string $value ) : bool {
		$success = $this->option_provider::update_option( $key, $value );
		if ( $success ) {
			$this->{$key} = $value;
		}
		return $success;
	}

	/**
	 * Retrieve the value for the option, if the value is present it will be returned, if it is not present it will
	 * be requested from the database.
	 *
	 * @param string $key    Key for the option
	 * @param mixed  $default The value to be returned if no value is present in the database
	 * @param bool   $force check the database again even if we have a value in memoryd value is empty    If true, this will always force a check of the value in the database regardless of what
	 *                         value this class has
	 *
	 * @return mixed|null
	 */
	private function get( string $key, $default = null, bool $force = false ) {
		if ( isset( $this->{$key} ) && ! $force ) {
			return $this->{$key};
		}
		$this->{$key} = $this->option_provider::get_option( $key, $default ) ?? '';
		return $this->{$key};
	}
}
