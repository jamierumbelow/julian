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

class Julian_Event
{
    
    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
    
    protected $from;
    protected $to;
    protected $event;
    protected $class_name;
    protected $extra = array();
    
    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */
    
    public function __construct($event)
    {
        $this->from = (isset($event['from'])) ? strtotime(implode('-', $event['from'])) : FALSE;
        $this->to = (isset($event['to'])) ? strtotime(implode('-', $event['to'])) : FALSE;
        $this->event = @$event['event'] ?: '';
        $this->class_name = @$event['class'] ?: '';
        $this->extra = @$event['extra'] ?: array();
    }
    
    /* --------------------------------------------------------------
     * GETTERS
     * ------------------------------------------------------------ */
    
    public function name() { return $this->event ?: ''; }
    public function from($format) { return date($format, $this->from) ?: ''; }
    public function to($format) { return date($format, $this->to) ?: ''; }
    public function class_name() { return $this->class_name ?: ''; }
    
    /**
     * A simple mechanism for storing and retrieving
     * any extra data alongside the event
     */
    public function extra($key = FALSE)
    {
        return ($key) ? $this->extra[$key] : $this->extra;
    }
}