<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Julian
 *
 * A very clever, Welsh and bespectacled Calendar class
 *
 * @author 		Jamie Rumbelow <http://jamierumbelow.net>
 * @version		0.1.0
 * @copyright 	(c)2011 Jamie Rumbelow
 */

class Julian_Week
{
    
    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
    
    protected $days = array();
    
    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */
    
    public function __construct($days = array())
    {
        $this->days = $days;
    }
    
    /* --------------------------------------------------------------
     * GETTERS
     * ------------------------------------------------------------ */
    
    public function days() { return $this->days; }
}