# DEPRECATED: Julian

[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

Julian is a very clever, standalone PHP calendar class, with support for events, fully customisable templates and totally arbitrary URLs.

## Requirements

* PHP 5.3+
* [Twitter Bootstrap (included)](http://twitter.github.com/bootstrap)

## Installation

Copy the download directory into your project. Require the *libraries/julian.php* file into your script, ensure you're linking the stylesheets into your views and away you go.

**Julian was built and tested inside CodeIgniter. Copying the contents of *libraries* into your *application/libraries* folder will allow you to load Julian like any other CodeIgniter library.**

## Example Controller

```php
require_once 'libraries/julian.php';

$year = @$_GET['year'] ?: date('Y');
$month = @$_GET['month'] ?: date('m');

$calendar = new Julian(array(
    'url'           => site_url('/absence_requests?year=%y&month=%m'),
    'current_month' => $month,
    'current_year'  => $year
));
```

## Example View

```php
<link href="/stylesheets/bootstrap.min.css" rel="stylesheet" />
<link href="/stylesheets/julian.css" rel="stylesheet" />

<div id="calendar">
    <div id="calendar_header" class="alert-message block-message">
        <div class="span5"><?= anchor($calendar->prev_url(), "&lt;&lt;") ?></a></div>
        <div class="span6"><?= $calendar->current_month() ?> <?= $calendar->current_year() ?></div>
        <div class="span5"><?= anchor($calendar->next_url(), "&gt;&gt;") ?></a></div>
    </div>
    
    <table class="bordered-table zebra-striped">
        <tr>
            <?php foreach ($calendar->weekdays() as $weekday): ?>
                <td><?= $weekday ?></td>
            <?php endforeach ?>
        </tr>
        
        <?php foreach ($calendar->weeks() as $week): ?>
            <tr>
                <?php foreach ($week->days() as $day): ?>
                    <td class="calendar-day <?= $day->today_class() ?>">
                        <?php if(!$day->blank()): ?>
                            <span class="day-number"><?= $day->day() ?></span>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <table id="calendar-event-overlay">
        <?php foreach($calendar->weeks() as $week): ?>
            <tr>
                <?php foreach ($week->days() as $day): ?>
                    <td class="calendar-day">
                        <?php if(!$day->blank()): ?>
                            <?php foreach($day->events() as $event): ?>
                                <div class="calendar-event <?= $event->class_name() ?>">
                                    <?= $event->name() ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
```

## Result

![An Example Calendar](/jamierumbelow/julian/raw/master/screenshot.png)

## Configuration

Passing through an array of config values to the constructor has the same effect as calling `initialize()` directly, or by setting the instance variables on an instance. Right now, Julian is very beta, so the range of config values is fairly limited. Nonetheless, here's a list of the existing values and what they do.

* `$current_month` - Sets the currently displayed month. Should be in format MM.
* `$current_year` - Sets the currently displayed year. Should be in format YYYY.

*Changing either of these values will require recalculation, so you should ensure that you call `setup()` or set the values through `initialize()`.*

* `$url` - A template for generating the previous/next URLs using the `prev_url()` and `next_url()` methods. `%y` and `%m` will be replaced with the appropriate year and month, respectively.

## Why Julian?

Julian takes its namesake from two places. Firstly, the Julian Calendar, introduced by Julius Caesar in 46BC, and secondly, one of my closest friends, fellow web developer [@juliancheal](http://twitter.com/juliancheal).
