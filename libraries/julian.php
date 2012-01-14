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

require_once 'julian/week.php';
require_once 'julian/day.php';
require_once 'julian/event.php';

class Julian
{
    
    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
    
    public $current_month;
    public $current_year;
    public $weeks = array();
    
    public $events = array();
    
    public $url = '';
    
    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */
     
    public function initialize($data = array())
    {
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
        
        if ($this->current_month !== NULL && $this->current_year !== NULL)
        {
            $this->setup();
        }
    }
    
    // A little useless right now, I know, but it's here in case I want to refactor
    public function calendar($config = array())
    {
        $this->initialize($config);
        return $this;
    }
    
    /* --------------------------------------------------------------
     * PUBLIC API
     * ------------------------------------------------------------ */
    
    public function current_month()
    {
        return $this->month_name($this->current_month);
    }
    
    public function current_year()
    {
        return $this->current_year;
    }
    
    public function weekdays()
    {
        return array(
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        );
    }
    
    public function month_name($num)
    {
        $months = array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        );
        
        return $months[$num] ?: FALSE;
    }
    
    public function prev_url()
    {
        $date = $this->_adjust_date($this->current_month - 1, $this->current_year);
        return str_replace(array('%m', '%y'), array($date['month'], $date['year']), $this->url);
    }
    
    public function next_url()
    {
        $date = $this->_adjust_date($this->current_month + 1, $this->current_year);
        return str_replace(array('%m', '%y'), array($date['month'], $date['year']), $this->url);
    }
    
    public function weeks()
    {
        return $this->weeks;
    }
    
    /* --------------------------------------------------------------
     * EVENT API
     * ------------------------------------------------------------ */
    
    public function add_event($from, $to, $event, $extra = array())
    {
        // Parse the dates
        $from = $this->_parse_date($from);
        $to = $this->_parse_date($to);
        
        // Add the starting event to the $from day
        $this->events[(int)$from['year']][(int)$from['month']][(int)$from['day']][] = array(
            'class' => 'start',
            'event' => $event,
            'extra' => $extra
        );
        
        // Get the difference in days between $from and $to
        $from_timestamp = strtotime(implode('-', $from));
        $to_timestamp = strtotime(implode('-', $to));
        $difference = $to_timestamp - $from_timestamp;
        $days = (int)floor($difference / 86400) - 1;
        
        $i = 0;
        $current_day = $this->_parse_date(strtotime(implode('-', $from)) + 86400);
        
        // For each day between $from and $to, add a middle event
        while ($i < $days)
        {
            $this->events[(int)$current_day['year']][(int)$current_day['month']][(int)$current_day['day']][] = array(
                'class' => 'during',
                'event' => '&nbsp;',
                'extra' => $extra
            );
            
            $i++;
            $current_day = $this->_parse_date(strtotime(implode('-', $current_day)) + 86400);
        }
        
        // And then add an end event for $to
        $this->events[(int)$to['year']][(int)$to['month']][(int)$to['day']][] = array(
            'class' => 'end',
            'event' => '&nbsp;',
            'extra' => $extra
        );
        
        // We're done.
        return;
    }
    
    public function get_events($day, $month, $year)
    {
        return (isset($this->events[(int)$year][(int)$month][(int)$day])) ? 
                      $this->events[(int)$year][(int)$month][(int)$day] 
                      : array();
    }

    /* --------------------------------------------------------------
     * PROCESSING LOGIC
     * ------------------------------------------------------------ */
    
    /**
     * Setup the Calendar -- figure out weeks & days etc
     */
    public function setup()
    {
        // Ensure we've got an appropriate month & year
        $adjusted_date = $this->_adjust_date($this->current_month, $this->current_year);
        
        $month  = $adjusted_date['month'];
        $year   = $adjusted_date['year'];
        
        // How many days in this month?
        $total_days = $this->_get_total_days($month, $year);
        
        // Set the starting day number
        $local_date = mktime(12, 0, 0, $month, 1, $year);
        $date = getdate($local_date);
        $day  = 1 - $date["wday"];
        
        while ($day > 1)
        {
            $day -= 7;
        }
        
        // Also, get today
        $today_year = date('Y');
        $today_month = date('m');
        $today_day = date('d');
        
        // Loop through each day of this month.
        while ($day <= $total_days)
        {
            $days = array();
            
            for ($i = 0; $i < 7; $i++)
            {
                if ($day > 0 AND $day <= $total_days)
                {
                    $today = (bool)($today_year == $year && $today_month == $month && $today_day == $day);
                    $days[] = new Julian_Day($day, $month, $year, $this->get_events($day, $month, $year), $today);
                }
                else
                {
                    $days[] = new Julian_Day();
                }
                
                $day++;
            }
            
            $this->weeks[] = new Julian_Week($days);
        }
    }
    
    /* --------------------------------------------------------------
     * HELPER METHODS
     * ------------------------------------------------------------ */
    
    /**
     * Adjust Date
     *
     * This function makes sure that we have a valid month/year.
     * For example, if you submit 13 as the month, the year will
     * increment and the month will become January.
     *
     * Taken from CodeIgniter's Calendar Class
     */
    protected function _adjust_date($month, $year)
    {
        $date = array();

        $date['month']  = $month;
        $date['year']   = $year;

        while ($date['month'] > 12)
        {
            $date['month'] -= 12;
            $date['year']++;
        }

        while ($date['month'] <= 0)
        {
            $date['month'] += 12;
            $date['year']--;
        }

        if (strlen($date['month']) == 1)
        {
            $date['month'] = '0'.$date['month'];
        }

        return $date;
    }
    
    /**
     * Total days in a given month
     *
     * Again, lifted from CodeIgniter
     */
    protected function _get_total_days($month, $year)
    {
        $days_in_month  = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        if ($month < 1 OR $month > 12)
        {
            return 0;
        }

        // Is the year a leap year?
        if ($month == 2)
        {
            if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0))
            {
                return 29;
            }
        }

        return $days_in_month[$month - 1];
    }
    
    /**
     * Parse a user-submitted/inputted date
     */
    protected function _parse_date($input)
    {
        $date = array();
        
        // Is it a timestamp?
        if (is_int($input))
        {
            $date['day'] = date('d', $input);
            $date['month'] = date('m', $input);
            $date['year'] = date('Y', $input);
        }
        
        return $date;
    }
}