<?php
/**
 * Define item inside cache system.
 *
 * Each item must be associated with a specific key, which can be set
 * according to the implementing system and typically passed by the
 * KYSS\Cache\PoolInterface object.
 *
 * @package  KYSS\Cache
 * @since  0.15.0
 */

namespace KYSS\Cache;

/**
 * Interface for interacting with objects inside a cache.
 *
 * @package  KYSS\Cache
 * @since  0.15.0
 * @version  1.0.0
 */
interface CacheItemInterface {
	/**
	 * Retrieve key of current cache item.
	 *
	 * The key is loaded by the Implementing Library, but should be available
	 * to the higher level callers when needed.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  string The key for this cache item.
	 */
	public function getKey();

	/**
	 * Retrieve value of the item from the cache.
	 *
	 * The value returned must be identical to the original value stored
	 * by `set()`.
	 *
	 * If `isHit()` returns false, this method MUST return null. Note that
	 * null is a legitimate cached value, so the `isHit()` method SHOULD
	 * be used to differentiate between "null value was found" and "no value
	 * was found".
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  mixed The value corresponding to this cache item's key, or null
	 * if not found.
	 */
	public function get();

	/**
	 * Set value represented by this cache item.
	 *
	 * The `$value` argument may be any item that can be serialized by PHP,
	 * although the method of serialization is left up to the Implementing
	 * Library.
	 *
	 * Implementing Libraries MAY provide a default TTL if one is not specified.
	 * If no TTL is specified and no default TTL has been set, the TTL MUST be
	 * set to the maximum possible duration of the underlying storage mechanism,
	 * or permanent if possible.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  mixed $value The serializable value to be stored.
	 * @param  int|\DateTime $ttl
	 * - If an integer is passed, it is interpreted as the numer of seconds after
	 * which the item must be considered expired.
	 * - If a DateTime object is passed, it is interpreted as the point in time
	 * after which the item must be considered expired.
	 * - If no value is passed, a default value may be used. If none is set, the
	 * value should be stored permanently or for as long as the implementation
	 * allows.
	 * @return  static The invoked object.
	 */
	public function set( $value, $ttl = null );

	/**
	 * Confirm if the cache item lookup resulted in a cache hit.
	 *
	 * Note: This method MUST NOT have a race condition between calling `isHit()`
	 * and calling `get()`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  bool True if hit, false otherwise.
	 */
	public function isHit();

	/**
	 * Confirm if the cache item exists in the cache.
	 *
	 * Note: This method MAY avoid retrieving the cached value for performance
	 * reasons, which could result in a race condition between `exists()` and `get()`.
	 * To avoid that potential race condition use `isHit()` instead.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  bool True if the item exists in cache, false otherwise.
	 */
	public function exists();

	/**
	 * Set expiration for this cache item.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  int|\DateTime $ttl
	 * - If an integer is passed, it is interpretated as the number of seconds
	 * after which the item MUST be considered expired.
	 * - If a DateTime object is passed, it is interpreted as the point in time
	 * after which the item MUST be considered expired.
	 * - If null is passed, a default value MAY be used. If none is set, the value
	 * should be stored permanently or for as long as the implementation allows.
	 * @return  static The called object.
	 */
	public function setExpiration( $ttl = null );

	/**
	 * Retrieve expiration time of non-yet-expired cache item.
	 *
	 * If this cache item is a Cache Miss, this method MAY return the time at which
	 * the item expired or the current time if that is not available.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  \DateTime The timestamp at which this cache item will expire.
	 */
	public function getExpiration();
}