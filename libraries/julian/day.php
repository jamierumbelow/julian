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

class Julian_Day
{
    
    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
    
    protected $day;
    protected $month;
    protected $year;
    
    protected $events = array();
    
    protected $today = FALSE;
    protected $blank = FALSE;
    
    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */
    
    public function __construct($day = FALSE, $month = FALSE, $year = FALSE, $events = array(), $today = FALSE)
    {
        if ($day == FALSE)
        {
            // Blank cell
            $this->blank = TRUE;
        }
        else
        {
            $this->day = $day;
            $this->month = $month;
            $this->year = $year;
            $this->today = $today;
            
            if ($events)
            {
                foreach ($events as $event)
                {
                    $this->events[] = new Julian_Event($event);
                }
            }
        }
    }
    
    /* --------------------------------------------------------------
     * FORMATTING
     * ------------------------------------------------------------ */
    
    /**
     * Is it today? If so, return a string (useful for today classes)
     */
    public function today_class($class = FALSE)
    {
        return ($this->today) ? ($class ?: 'today') : '';
    }
    
    /* --------------------------------------------------------------
     * GETTERS
     * ------------------------------------------------------------ */
    
    public function day() { return $this->day; }
    public function month() { return $this->month; }
    public function year() { return $this->year; }
    public function events() { return $this->events; }
    public function today() { return $this->today; }
    public function blank() { return $this->blank; }
}