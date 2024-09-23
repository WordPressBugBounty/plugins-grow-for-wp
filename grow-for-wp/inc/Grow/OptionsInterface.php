<?php

namespace Grow;

interface OptionsInterface {
	/**
	 * Gets the Site ID in the Grow Remote that corresponds to this WordPress site.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_site_id( string $default = '', bool $force = false ) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_site_id( string $value ) : bool;

	/**
	 * Gets the Grow Site UUID.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_site_uuid( string $default = '', bool $force = false ) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_site_uuid( string $value ) : bool;

	/**
	 * Gets the Unix Epoch Timestamp for when the plugin was first activated.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_first_install( string $default = '', bool $force = false) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_first_install( string $value ) : bool;

	/**
	 * Gets the current version of the plugin.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_current_version( string $default = '', bool $force = false) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_current_version( string $value ) : bool;

	/**
	 * Gets the version of the plugin when it was first activated.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_first_install_version( string $default = '', bool $force = false) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_first_install_version( string $value ) : bool;

	/**
	 * Gets the temporarily stored value on whether Grow should redirect on next request.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_should_redirect_after_activation( string $default = '', bool $force = false) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_should_redirect_after_activation( string $value ) : bool;

	/**
	 * Gets the current enabled/disabled status for Journey related functionality.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_journey_status( string $default = '', bool $force = false) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_journey_status( string $value ) : bool;

	/**
	 * Gets the current cached ads.txt management method.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_ads_txt_method( string $default = '', bool $force = false) : string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_ads_txt_method( string $value ) : bool;

	/**
	 * Gets the temporary lock status for whether an ads.txt redirect method check is in progress.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_ads_txt_redirect_check_in_progress( string $default = '', bool $force = false ): string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_ads_txt_redirect_check_in_progress( string $value ): bool;

	/**
	 * Gets the flag that determines whether the need connection message should be shown.
	 *
	 * @param string $default the default value to get if the property is empty
	 * @param bool   $force check the database again even if we have a value in memory
	 *
	 * @return string
	 */
	public function get_grow_show_need_connection_message( string $default = '', bool $force = false ): string;

	/**
	 * @param string $value New value to set
	 *
	 * @return bool
	 */
	public function set_grow_show_need_connection_message( string $value ): bool;
}
