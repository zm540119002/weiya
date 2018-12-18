<?php
namespace web\all\Lib;

/**
 * The date library.
 * @package     Date
 */
class Date
{
    /**
     * Build hour time list.
     *
     * @param  int $begin
     * @param  int $end
     * @param  int $delta
     * @access public
     * @return array
     */
    public static function buildTimeList($begin, $end, $delta)
    {
        $times = array();
        for ($hour = $begin; $hour <= $end; $hour++) {
            for ($minutes = 0; $minutes < 60; $minutes += $delta) {
                $time = sprintf('%02d%02d', $hour, $minutes);
                $label = sprintf('%02d:%02d', $hour, $minutes);
                $times[$time] = $label;
            }
        }
        return $times;
    }

    /**
     * Get today.
     * @param string $format
     * @return bool|string
     */

    public static function today($format = 'Y-m-d')
    {
        return date($format, time());
    }

    /**
     * Get yesterday
     *
     * @access public
     * @return date
     */
    public static function yesterday()
    {
        return date('Y-m-d', strtotime('yesterday'));
    }

    /**
     * Get tomorrow.
     *
     * @access public
     * @return date
     */
    public static function tomorrow()
    {
        return date('Y-m-d', strtotime('tomorrow'));
    }

    /**
     * Get the day before yesterday.
     *
     * @access public
     * @return date
     */
    public static function twoDaysAgo()
    {
        return date('Y-m-d', strtotime('-2 days'));
    }

    /**
     * Get now time period.
     *
     * @param  int $delta
     * @access public
     * @return string the current time period, like 0915
     */
    public static function now($delta = 10)
    {
        $range = range($delta, 60 - $delta, $delta);
        $hour = date('H', time());
        $minute = date('i', time());

        if ($minute > 60 - $delta) {
            $hour += 1;
            $minute = 00;
        } else {
            for ($i = 0; $i < $delta; $i++) {
                if (in_array($minute + $i, $range)) {
                    $minute = $minute + $i;
                    break;
                }
            }
        }

        return sprintf('%02d%02d', $hour, $minute);
    }

    /**
     * Format time 0915 to 09:15
     *
     * @param  string $time
     * @access public
     * @return string
     */
    public static function formatTime($time)
    {
        if (strlen($time) != 4 or $time == '2400') return '';
        return substr($time, 0, 2) . ':' . substr($time, 2, 2);
    }

    /**
     * Get the begin and end date of this week.
     *
     * @access public
     * @return array
     */
    public static function getThisWeek()
    {
        $baseTime = self::getMiddleOfThisWeek();
        $begin = date('Y-m-d', strtotime('last monday', $baseTime)) . ' 00:00:00';
        $end = date('Y-m-d', strtotime('next sunday', $baseTime)) . ' 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Get the begin and end date of last week.
     *
     * @access public
     * @return array
     */
    public static function getLastWeek()
    {
        $baseTime = self::getMiddleOfLastWeek();
        $begin = date('Y-m-d', strtotime('last monday', $baseTime)) . ' 00:00:00';
        $end = date('Y-m-d', strtotime('next sunday', $baseTime)) . ' 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Get the time at the middle of this week.
     *
     * If today in week is 1, move it one day in future. Else is 7, move it back one day. To keep the time geted in this week.
     *
     * @access public
     * @return time
     */
    public static function getMiddleOfThisWeek()
    {
        $baseTime = time();
        $weekDay = date('N');
        if ($weekDay == 1) $baseTime = time() + 86400;
        if ($weekDay == 7) $baseTime = time() - 86400;
        return $baseTime;
    }

    /**
     * Get middle of last week.
     *
     * @access public
     * @return time
     */
    public static function getMiddleOfLastWeek()
    {
        $weekDay = date('N');
        $baseTime = time() - 86400 * 7;
        if ($weekDay == 1) $baseTime = time() - 86400 * 4;  // Make sure is last thursday.
        if ($weekDay == 7) $baseTime = time() - 86400 * 10; // Make sure is last thursday.
        return $baseTime;
    }

    /**
     * Get begin and end time of this month.
     *
     * @access public
     * @return array
     */
    public static function getThisMonth()
    {
        $begin = date('Y-m') . '-01 00:00:00';
        $end = date('Y-m', strtotime('next month')) . '-00 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Get begin and end time of last month.
     *
     * @access public
     * @return array
     */
    public static function getLastMonth()
    {
        $begin = date('Y-m', strtotime('last month', strtotime(date('Y-m', time()) . '-01 00:00:01'))) . '-01 00:00:00';
        $end = date('Y-m', strtotime('this month')) . '-00 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Get begin and end time of this season.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getThisSeason()
    {
        $season = ceil((date('n')) / 3);                                                // Get this session.
        $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 2, 1, date('Y')));
        $endDay = date('t', mktime(0, 0, 0, $season * 3, 1, date("Y")));               // Get end day.
        $end = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, $endDay, date('Y')));

        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Get begin and end time of last season.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getLastSeason()
    {
        $season = ceil((date('n')) / 3) - 1;                                             // Get last session.
        $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 2, 1, date('Y')));
        $endDay = date('t', mktime(0, 0, 0, $season * 3, 1, date("Y")));                // Get end day.
        $end = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, $endDay, date('Y')));

        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Get begin and end time of this year.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getThisYear()
    {
        $begin = date('Y-m-d', strtotime('1/1 this year')) . ' 00:00:00';
        $end = date('Y-m-d', strtotime('1/1 next year -1 day')) . ' 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * 获取今天的起始时间戳
     * @return array
     */
    public static function getThisDay()
    {
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        return array('begin' => $beginToday, 'end' => $endToday);
    }

    /**
     * Get begin and end time of last year.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getLastYear()
    {
        $begin = date('Y-m-d', strtotime('1/1 last year')) . ' 00:00:00';
        $end = date('Y-m-d', strtotime('1/1 this year -1 day')) . ' 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }

    /**
     * Format timestamp 1432011255 to 2015-10-11
     * @param $timestamp
     * @param string $format
     * @return bool|string
     */
    public static function formatTimestamp($timestamp, $format = 'Y-m-d H:i:s')
    {
        return date($format, $timestamp);
    }

    //获取结算时间 暂定周四12:00
    public static function getSettlementTime()
    {
        $time = strtotime ( "Thursday 23:59:59");
        $time = date('Y/m/d H:i:s',$time);
        return $time;
    }
}