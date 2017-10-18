<?php

namespace MarketingCloud;

/**
 * Utility functions.
 */
class Util {

	/**
	 * @param array      $array    The array
	 * @return bool      Returns true if the parameter array is dictionary type array, false otherwise.
	 */
	public static function isAssoc($array) {
		return ($array !== array_values($array));
	}

	/**
	 * This method will not change until a major release.
	 *
	 * @api
	 *
	 * @return string
	 */
	public static function getSDKVersion() {
		return "FuelSDK-PHP-v1.0.0";
	}

}
