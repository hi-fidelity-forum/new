<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Date helper.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Date {

	// Second amounts for various time increments
	const YEAR   = 31556926;
	const MONTH  = 2629744;
	const WEEK   = 604800;
	const DAY    = 86400;
	const HOUR   = 3600;
	const MINUTE = 60;

	// Available formats for Date::months()
	const MONTHS_LONG  = '%B';
	const MONTHS_SHORT = '%b';

	public static $timestamp_format = 'Y-m-d H:i:s';

	public static $timezone;

	public static function offset($remote, $local = NULL, $now = NULL)
	{
		if ($local === NULL)
		{
			// Use the default timezone
			$local = date_default_timezone_get();
		}

		if (is_int($now))
		{
			// Convert the timestamp into a string
			$now = date(DateTime::RFC2822, $now);
		}

		// Create timezone objects
		$zone_remote = new DateTimeZone($remote);
		$zone_local  = new DateTimeZone($local);

		// Create date objects from timezones
		$time_remote = new DateTime($now, $zone_remote);
		$time_local  = new DateTime($now, $zone_local);

		// Find the offset
		$offset = $zone_remote->getOffset($time_remote) - $zone_local->getOffset($time_local);

		return $offset;
	}

	public static function seconds($step = 1, $start = 0, $end = 60)
	{
		// Always integer
		$step = (int) $step;

		$seconds = array();

		for ($i = $start; $i < $end; $i += $step)
		{
			$seconds[$i] = sprintf('%02d', $i);
		}

		return $seconds;
	}

	public static function minutes($step = 5)
	{
		// Because there are the same number of minutes as seconds in this set,
		// we choose to re-use seconds(), rather than creating an entirely new
		// function. Shhhh, it's cheating! ;) There are several more of these
		// in the following methods.
		return Date::seconds($step);
	}

	public static function hours($step = 1, $long = FALSE, $start = NULL)
	{
		// Default values
		$step = (int) $step;
		$long = (bool) $long;
		$hours = array();

		// Set the default start if none was specified.
		if ($start === NULL)
		{
			$start = ($long === FALSE) ? 1 : 0;
		}

		$hours = array();

		// 24-hour time has 24 hours, instead of 12
		$size = ($long === TRUE) ? 23 : 12;

		for ($i = $start; $i <= $size; $i += $step)
		{
			$hours[$i] = (string) $i;
		}

		return $hours;
	}

	public static function ampm($hour)
	{
		// Always integer
		$hour = (int) $hour;

		return ($hour > 11) ? 'PM' : 'AM';
	}

	public static function adjust($hour, $ampm)
	{
		$hour = (int) $hour;
		$ampm = strtolower($ampm);

		switch ($ampm)
		{
			case 'am':
				if ($hour == 12)
				{
					$hour = 0;
				}
			break;
			case 'pm':
				if ($hour < 12)
				{
					$hour += 12;
				}
			break;
		}

		return sprintf('%02d', $hour);
	}

	public static function days($month, $year = FALSE)
	{
		static $months;

		if ($year === FALSE)
		{
			// Use the current year by default
			$year = date('Y');
		}

		// Always integers
		$month = (int) $month;
		$year  = (int) $year;

		// We use caching for months, because time functions are used
		if (empty($months[$year][$month]))
		{
			$months[$year][$month] = array();

			// Use date to find the number of days in the given month
			$total = date('t', mktime(1, 0, 0, $month, 1, $year)) + 1;

			for ($i = 1; $i < $total; $i++)
			{
				$months[$year][$month][$i] = (string) $i;
			}
		}

		return $months[$year][$month];
	}

	public static function months($format = NULL)
	{
		$months = array();

		if ($format === Date::MONTHS_LONG OR $format === Date::MONTHS_SHORT)
		{
			for ($i = 1; $i <= 12; ++$i)
			{
				$months[$i] = strftime($format, mktime(0, 0, 0, $i, 1));
			}
		}
		else
		{
			$months = Date::hours();
		}

		return $months;
	}

	public static function years($start = FALSE, $end = FALSE)
	{
		// Default values
		$start = ($start === FALSE) ? (date('Y') - 5) : (int) $start;
		$end   = ($end   === FALSE) ? (date('Y') + 5) : (int) $end;

		$years = array();

		for ($i = $start; $i <= $end; $i++)
		{
			$years[$i] = (string) $i;
		}

		return $years;
	}

	public static function span($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// Normalize output
		$output = trim(strtolower( (string) $output));

		if ( ! $output)
		{
			// Invalid output
			return FALSE;
		}

		// Array with the output formats
		$output = preg_split('/[^a-z]+/', $output);

		// Convert the list of outputs to an associative array
		$output = array_combine($output, array_fill(0, count($output), 0));

		// Make the output values into keys
		extract(array_flip($output), EXTR_SKIP);

		if ($local === NULL)
		{
			// Calculate the span from the current time
			$local = time();
		}

		// Calculate timespan (seconds)
		$timespan = abs($remote - $local);

		if (isset($output['years']))
		{
			$timespan -= Date::YEAR * ($output['years'] = (int) floor($timespan / Date::YEAR));
		}

		if (isset($output['months']))
		{
			$timespan -= Date::MONTH * ($output['months'] = (int) floor($timespan / Date::MONTH));
		}

		if (isset($output['weeks']))
		{
			$timespan -= Date::WEEK * ($output['weeks'] = (int) floor($timespan / Date::WEEK));
		}

		if (isset($output['days']))
		{
			$timespan -= Date::DAY * ($output['days'] = (int) floor($timespan / Date::DAY));
		}

		if (isset($output['hours']))
		{
			$timespan -= Date::HOUR * ($output['hours'] = (int) floor($timespan / Date::HOUR));
		}

		if (isset($output['minutes']))
		{
			$timespan -= Date::MINUTE * ($output['minutes'] = (int) floor($timespan / Date::MINUTE));
		}

		// Seconds ago, 1
		if (isset($output['seconds']))
		{
			$output['seconds'] = $timespan;
		}

		if (count($output) === 1)
		{
			// Only a single output was requested, return it
			return array_pop($output);
		}

		// Return array
		return $output;
	}

	public static function fuzzy_span($timestamp, $local_timestamp = NULL)
	{
		$local_timestamp = ($local_timestamp === NULL) ? time() : (int) $local_timestamp;

		// Determine the difference in seconds
		$offset = abs($local_timestamp - $timestamp);

		if ($offset <= Date::MINUTE)
		{
			$span = 'moments';
		}
		elseif ($offset < (Date::MINUTE * 20))
		{
			$span = 'a few minutes';
		}
		elseif ($offset < Date::HOUR)
		{
			$span = 'less than an hour';
		}
		elseif ($offset < (Date::HOUR * 4))
		{
			$span = 'a couple of hours';
		}
		elseif ($offset < Date::DAY)
		{
			$span = 'less than a day';
		}
		elseif ($offset < (Date::DAY * 2))
		{
			$span = 'about a day';
		}
		elseif ($offset < (Date::DAY * 4))
		{
			$span = 'a couple of days';
		}
		elseif ($offset < Date::WEEK)
		{
			$span = 'less than a week';
		}
		elseif ($offset < (Date::WEEK * 2))
		{
			$span = 'about a week';
		}
		elseif ($offset < Date::MONTH)
		{
			$span = 'less than a month';
		}
		elseif ($offset < (Date::MONTH * 2))
		{
			$span = 'about a month';
		}
		elseif ($offset < (Date::MONTH * 4))
		{
			$span = 'a couple of months';
		}
		elseif ($offset < Date::YEAR)
		{
			$span = 'less than a year';
		}
		elseif ($offset < (Date::YEAR * 2))
		{
			$span = 'about a year';
		}
		elseif ($offset < (Date::YEAR * 4))
		{
			$span = 'a couple of years';
		}
		elseif ($offset < (Date::YEAR * 8))
		{
			$span = 'a few years';
		}
		elseif ($offset < (Date::YEAR * 12))
		{
			$span = 'about a decade';
		}
		elseif ($offset < (Date::YEAR * 24))
		{
			$span = 'a couple of decades';
		}
		elseif ($offset < (Date::YEAR * 64))
		{
			$span = 'several decades';
		}
		else
		{
			$span = 'a long time';
		}

		if ($timestamp <= $local_timestamp)
		{
			// This is in the past
			return $span.' ago';
		}
		else
		{
			// This in the future
			return 'in '.$span;
		}
	}

	public static function unix2dos($timestamp = FALSE)
	{
		$timestamp = ($timestamp === FALSE) ? getdate() : getdate($timestamp);

		if ($timestamp['year'] < 1980)
		{
			return (1 << 21 | 1 << 16);
		}

		$timestamp['year'] -= 1980;

		// What voodoo is this? I have no idea... Geert can explain it though,
		// and that's good enough for me.
		return ($timestamp['year']    << 25 | $timestamp['mon']     << 21 |
		        $timestamp['mday']    << 16 | $timestamp['hours']   << 11 |
		        $timestamp['minutes'] << 5  | $timestamp['seconds'] >> 1);
	}

	public static function dos2unix($timestamp = FALSE)
	{
		$sec  = 2 * ($timestamp & 0x1f);
		$min  = ($timestamp >>  5) & 0x3f;
		$hrs  = ($timestamp >> 11) & 0x1f;
		$day  = ($timestamp >> 16) & 0x1f;
		$mon  = ($timestamp >> 21) & 0x0f;
		$year = ($timestamp >> 25) & 0x7f;

		return mktime($hrs, $min, $sec, $mon, $day, $year + 1980);
	}

	public static function formatted_time($datetime_str = 'now', $timestamp_format = NULL, $timezone = NULL)
	{
		$timestamp_format = ($timestamp_format == NULL) ? Date::$timestamp_format : $timestamp_format;
		$timezone         = ($timezone === NULL) ? Date::$timezone : $timezone;

		$tz   = new DateTimeZone($timezone ? $timezone : date_default_timezone_get());
		$time = new DateTime($datetime_str, $tz);

		if ($time->getTimeZone()->getName() !== $tz->getName())
		{
			$time->setTimeZone($tz);
		}

		return $time->format($timestamp_format);
	}

} // End date
