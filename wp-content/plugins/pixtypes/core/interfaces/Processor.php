<?php defined('ABSPATH') or die;

/**
 * @package    pixtypes
 * @category   core
 * @author     Pixel Grade Team
 * @copyright  (c) 2013, Pixel Grade Media
 */
interface PixtypesProcessor {

	/**
	 * @return static $this
	 */
	function run();

	/**
	 * @return array
	 */
	function status();

	/**
	 * @return PixtypesMeta current data (influenced by user submitted data)
	 */
	function data();

	/**
	 * Shorthand.
	 *
	 * @return array
	 */
	function errors();

	/**
	 * Shorthand.
	 *
	 * @return boolean
	 */
	function performed_update();

	/**
	 * @return boolean true if state is nominal
	 */
	function ok();

} # interface
