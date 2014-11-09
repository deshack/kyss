<?php
/**
 * Cache Item Pool Interface.
 *
 * @package KYSS\Cache
 * @since  0.15.0
 */

namespace KYSS\Cache;

/**
 * Cache Item Pool Interface.
 *
 * The primary purpose of this interface is to accept a key from the Calling
 * Library and return the associated KYSS\Cache\CacheItemInterface object.
 * It is also the primary point of interaction with the entire cache collection.
 * All configuration and initialization of the Pool is left up to an Implementing
 * Library.
 *
 * @package  KYSS\Cache
 * @since  0.15.0
 * @version  1.0.0
 */
interface CacheItemPoolInterface {
	/**
	 * Retrieve Cache Item representing the specified key.
	 *
	 * This method must always return an ItemInterface object, even in case of
	 * a cache miss. It MUST NOT return null.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $key The key for which to return the corresponding Cache Item.
	 * @return  \KYSS\Cache\CacheItemInterface The corresponding Cache Item.
	 * @throws  \KYSS\Exceptions\InvalidArgumentException If the $key is not a legal
	 * value.
	 */
	public function getItem( $key );

	/**
	 * Retrieve traversable set of cache items.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $keys Indexed array of keys of items to retrieve.
	 * @return  array|\Traversable Traversable collection of Cache Items keyed by
	 * the cache keys of each item. A Cache Item will be returned for each key,
	 * even if that key is not found. However, if no keys are specified, then
	 * an empty traversable MUST be returned instead.
	 */
	public function getItems( array $keys = array() );

	/**
	 * Delete all items in the pool.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  bool True if the pool was successfully cleared. False if there
	 * was an error.
	 */
	public function clear();

	/**
	 * Remove multiple items from the pool.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  array $keys Array of keys that should be removed from the pool.
	 * @return  static The invoked object.
	 */
	public function deleteItems( array $keys );

	/**
	 * Persist a cache item immediately.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  CacheItemInterface $item The cache item to save.
	 * @return  static The invoked object.
	 */
	public function save( CacheItemInterface $item );

	/**
	 * Set Cache Item to be persisted later.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  CacheItemInterface $item The Cache Item to save.
	 * @return  static The invoked object.
	 */
	public function saveDeferred( CacheItemInterface $item );

	/**
	 * Persist any deferred Cache Items.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  bool True if all not-yet-saved items were successfully saved.
	 * False otherwise.
	 */
}