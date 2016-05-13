<?php
/**
 * AA Backup Manager Main manager.
 *
 * @package AA Backup Manager
 * @since   1.0
 */
if ( ! function_exists( 'BKM_manager' ) ) {
	/**
	 * AA Backup Manager manager.
	 * @since 1.0
	 * @return BKM_Manager
	 */
	function BKM_manager() {
		global $BKM_manager;

		return $BKM_manager;
	}
}
if ( ! function_exists( 'BKM_mapper' ) ) {
	/**
	 * Shorthand for BKM_Manager Mapper.
	 * @since 1.0
	 * @return BKM_Mapper
	 */
	function BKM_mapper() {
		return BKM_manager()->mapper();
	}
}
if ( ! function_exists( 'BKM_settings' ) ) {
	/**
	 * Shorthand for AA Backup Manager settings.
	 * @since 1.0
	 * @return BKM_Settings
	 */
	function BKM_settings() {
		return BKM_manager()->settings();
	}
}
if ( ! function_exists( 'BKM_path_dir' ) ) {
	/**
	 * Get file/directory path in BKM_Manager.
	 *
	 * @param string $name - path name
	 * @param string $file
	 *
	 * @since 1.0
	 * @return string
	 */
	function BKM_path_dir( $name, $file = '' ) {
		return BKM_manager()->path( $name, $file );
	}
}
if ( ! function_exists( 'BKM_upload_dir' ) ) {
	/**
	 * Temporary files upload dir;
	 * @since 1.0
	 * @return string
	 */
	function BKM_upload_dir() {
		return BKM_manager()->uploadDir();
	}
}
if ( ! function_exists( 'BKM_path_dir' ) ) {
	/**
	 * Get file/directory path in BKM_Manager.
	 *
	 * @param string $name - path name
	 * @param string $file
	 *
	 * @since 4.2
	 * @return string
	 */
	function BKM_path_dir( $name, $file = '' ) {
		return BKM_manager()->path( $name, $file );
	}
}
if ( ! function_exists( 'BKM_post_param' ) ) {
	/**
	 * Get param value from $_POST if exists.
	 *
	 * @param $param
	 * @param $default
	 *
	 * @since 1.0
	 * @return null|string - null for undefined param.
	 */
	function BKM_post_param( $param, $default = null ) {
		return isset( $_POST[ $param ] ) ? $_POST[ $param ] : $default;
	}
}
if ( ! function_exists( 'BKM_get_param' ) ) {
	/**
	 * Get param value from $_GET if exists.
	 *
	 * @param $param
	 * @param $default
	 *
	 * @since 1.0
	 * @return null|string - null for undefined param.
	 */
	function BKM_get_param( $param, $default = null ) {
		return isset( $_GET[ $param ] ) ? $_GET[ $param ] : $default;
	}
}
if ( ! function_exists( 'BKM_request_param' ) ) {
	/**
	 * Get param value from $_REQUEST if exists.
	 *
	 * @param $param
	 * @param $default
	 *
	 * @since 4.4
	 * @return null|string - null for undefined param.
	 */
	function BKM_request_param( $param, $default = null ) {
		return isset( $_REQUEST[ $param ] ) ? $_REQUEST[ $param ] : $default;
	}
}
if ( ! function_exists( 'BKM_action' ) ) {
	/**
	 * Get BKM_Manager special action param.
	 * @since 1.0
	 * @return string|null
	 */
	function BKM_action() {
		$BKM_action = 'import';
		if ( isset( $_GET['BKM_action'] ) ) {
			$BKM_action = $_GET['BKM_action'];
		} elseif ( isset( $_POST['BKM_action'] ) ) {
			$BKM_action = $_POST['BKM_action'];
		}

		return $BKM_action;
	}
}

if ( ! function_exists( 'BKM_asset_url' ) ) {
	/**
	 * Get full url for assets.
	 *
	 * @param string $file
	 *
	 * @since 4.2
	 * @return string
	 */
	function BKM_asset_url( $file ) {
		return BKM_manager()->assetUrl( $file );
	}
}

/**
 * Plugin name for BKM_time_elapsed_string.
 *
 * @since 1.0
 * @return string
 */
if ( ! function_exists( 'BKM_time_elapsed_string' ) ) {
	function BKM_time_elapsed_string($ptime)
	{
	    $etime = time() - $ptime;
	
	    if ($etime < 1)
	    {
	        return '0 seconds';
	    }
	
	    $a = array( 365 * 24 * 60 * 60  =>  'year',
	                 30 * 24 * 60 * 60  =>  'month',
	                      24 * 60 * 60  =>  'day',
	                           60 * 60  =>  'hour',
	                                60  =>  'minute',
	                                 1  =>  'second'
	                );
	    $a_plural = array( 'year'   => 'years',
	                       'month'  => 'months',
	                       'day'    => 'days',
	                       'hour'   => 'hours',
	                       'minute' => 'minutes',
	                       'second' => 'seconds'
	                );
	
	    foreach ($a as $secs => $str)
	    {
	        $d = $etime / $secs;
	        if ($d >= 1)
	        {
	            $r = round($d);
	            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
	        }
	    }
	}
}


/**
 * Plugin name for BKM_Manager.
 *
 * @since 1.0
 * @return string
 */
function BKM_plugin_name() {
	return BKM_manager()->pluginName();
}