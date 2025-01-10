<?php
/**
 * Abstract singleton class
 *
 * @package Rekai
 */

namespace Rekai;

use RuntimeException;

/**
 * Abstract Singleton class
 *
 * @abstract
 */
abstract class Singleton {
	/**
	 * The singleton instance
	 */
	public static $instances = array();

	/**
	 * Singletons cannot be cloned, so we disable cloning.
	 *
	 * @return void
	 */
	public function __clone() {}

	/**
	 * Singletons cannot be unserialized, so we disable serialization.
	 *
	 * @return void
	 * @throws RuntimeException Throws an exception if we try to serialize the instance.
	 */
	public function __wakeup(): void {
		throw new RuntimeException( 'Cannot unserialize a singleton' );
	}

	/**
	 * Gets the Rekai instance.
	 *
	 * @return static
	 */
	public static function get_instance(): Singleton {
		$cls = static::class;
		if ( ! isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new static();
		}

		return self::$instances[ $cls ];
	}
}
