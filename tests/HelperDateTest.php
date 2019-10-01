<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Tests;

use Gyselroth\Helper\HelperDate;
use PHPUnit\Framework\Constraint\IsType;
use Zend_Date;

class HelperDateTest extends HelperTestCase
{
    public function testIsDateTimeString(): void
    {
        $this->assertFalse(HelperDate::isDateTimeString('2017-4-2 12:03'));
        $this->assertTrue(HelperDate::isDateTimeString('2017-4-2 12:03:12'));
        $this->assertTrue(HelperDate::isDateTimeString('3.9.01 09:12', 'j.n.y H:i'));
    }

    public function testIsDateString(): void
    {
        $this->assertTrue(HelperDate::isDateString('31-12-2017', '-', true));
        $this->assertFalse(HelperDate::isDateString('24-3-2017'));
        $this->assertTrue(HelperDate::isDateString('24.3.2017', '.', true));
        $this->assertFalse(HelperDate::isDateString('03-24-2017 12:05'));
    }

    public function testIsTimeString(): void
    {
        $this->assertTrue(HelperDate::isTimeString('3:02:02'));
        $this->assertTrue(HelperDate::isTimeString('12:02', false));
    }

    /**
     * @throws \Zend_Date_Exception
     * @throws \Zend_Locale_Exception
     */
    public function testGetCurrentDate(): void
    {
        $weekdays = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
        $months   = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
        $locale   = new \Zend_Locale();
        if ($locale->getLanguage() === 'de') {
            $string = $weekdays[date('w')] . ', ' . date('d.') . ' ' . $months[date('n') - 1] . ' ' . date('Y');
        } else {
            $string = date('l, d. F Y');
        }
        $this->assertSame($string, HelperDate::getCurrentDate());
    }

    /**
     * Test HelperDate::getUnixTimestampFromDate()
     * @throws \Zend_Date_Exception
     */
    public function testGetUnixTimestampFromDate(): void
    {
        // Test: convert date-string to UNIX timestamp
        $dateString = '2015-12-31';
        $result     = HelperDate::getUnixTimestampFromDate($dateString);
        $this->assertThat(
            $result,
            new IsType('int')
        );
        $this->assertGreaterThan(0, $result);
        $this->assertEquals(strtotime($dateString), $result);

        // Test: convert dateTime-string to UNIX timestamp
        $dateString = '2015-12-31 12:30:00';
        $result     = HelperDate::getUnixTimestampFromDate($dateString);
        $this->assertThat(
            $result,
            new IsType('int')
        );
        $this->assertGreaterThan(0, $result);
        $this->assertEquals(strtotime($dateString), $result);

        // Test: convert Zend object to UNIX timestamp
        $date      = new Zend_Date();
        $timestamp = $date->toValue();
        $this->assertSame($timestamp, HelperDate::getUnixTimestampFromDate($date));
    }

    /**
     * Test HelperDate::getDateFromUnixTimestamp()
     * @throws \Zend_Date_Exception
     */
    public function testGetDateFromUnixTimestamp(): void
    {
        $this->assertSame('10:20:20', HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_TIME_MYSQL));
        $this->assertEquals('1498645220000', HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_TIMESTAMP_JAVASCRIPT));
        $this->assertSame('Wed, 28. June 2017', HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_WEEKDAY_SHORT_DAY_MONTH_YEAR));
        $this->assertSame('Wednesday, 28. June 2017', HelperDate::getDateFromUnixTimestamp(1498645220, HelperDate::INDEX_FORMAT_WEEKDAY_LONG_DAY_MONTH_YEAR));
    }

    /**
     * @throws \Zend_Date_Exception
     */
    public function testGetMySqlDateTimeFromDate(): void
    {
        $this->assertSame('2017-06-28 10:20:20', HelperDate::getMySqlDateTimeFromDate(1498645220));
    }

    public function testGetDateTime(): void
    {
        $this->assertSame('{"date":"2017-06-28 10:20:20.000000","timezone_type":1,"timezone":"+00:00"}', json_encode(HelperDate::getDateTime(1498645220)));
        $this->assertSame('{"date":"2017-06-28 12:20:20.000000","timezone_type":3,"timezone":"UTC"}', json_encode(HelperDate::getDateTime('28.6.2017, 12:20:20')));
    }

    public function testGetDateStringFromDateTimeString(): void
    {
        $this->assertSame('2017-05-02', HelperDate::getDateStringFromDateTimeString('2017-05-02 12:20:20'));
    }

    public function testGetTimeStringFromDateTimeString(): void
    {
        $this->assertSame('12:20:20', HelperDate::getTimeStringFromDateTimeString('2017-05-02 12:20:20'));
    }

    public function testGetDateParts(): void
    {
        $this->assertSame('{"year":"2017","month":"6","day":"28"}', json_encode(HelperDate::getDateParts(1498645220)));
        $this->assertSame('{"year":"17","month":"6","day":"28"}', json_encode(HelperDate::getDateParts('28.6.2017', 'y', 'n', 'd')));
    }

    public function testgetDatePartsAtStartOfDay(): void
    {
        $this->assertSame('{"array":["1994","05","22","12","20","15"],"timestamp":769564800}', json_encode(HelperDate::getDatePartsAtStartOfDay('1994-05-22-12-20-15')));
        $this->assertSame('{"array":["1994","05","22"],"timestamp":769564800}', json_encode(HelperDate::getDatePartsAtStartOfDay('1994-05-22')));
    }

    public function testgetTimestampStartOfDay(): void
    {
        $this->assertEquals('769564800', HelperDate::getTimestampStartOfDay('1994-05-22'));
    }

    public function testgetTimestampEndOfDay(): void
    {
        $this->assertEquals('769651199', HelperDate::getTimestampEndOfDay('1994-05-22'));
    }

    public function testGetTimeStringParts(): void
    {
        $this->assertSame('{"hour":12,"minutes":5,"seconds":12}', json_encode(HelperDate::getTimeStringParts('12:05:12')));
        $this->assertSame('{"hour":12,"minutes":5}', json_encode(HelperDate::getTimeStringParts('12:05')));
    }

    public function testgetTimeString(): void
    {
        $this->assertSame('10:20:20', HelperDate::getTimeString(1498645220800, true));
        $this->assertSame('10:20:20', HelperDate::getTimeString(1498645220));
        $this->assertSame('12:20', HelperDate::getTimeString('12:20:20', false, false, true));
    }

    public function testGetSumSecondsOfTimeParts(): void
    {
        $this->assertEquals('44420',
            HelperDate::getSumSecondsOfTimeParts([
                'hour'    => '12',
                'minutes' => '20',
                'seconds' => '20'
            ]));
        $this->assertEquals(44420 + strtotime('today'),
            HelperDate::getSumSecondsOfTimeParts([
                'hour'    => '12',
                'minutes' => '20',
                'seconds' => '20'
            ], true));
    }

    public function testGetSumMinutesOfTimeParts(): void
    {
        $this->assertEquals('740',
            round(HelperDate::getSumMinutesOfTimeParts([
                'hour'    => '12',
                'minutes' => '20',
                'seconds' => '20'
            ])));
        $this->assertEquals(round((44420 + strtotime('today')) / 60),
            round(HelperDate::getSumMinutesOfTimeParts([
                'hour'    => '12',
                'minutes' => '20',
                'seconds' => '20'
            ], true)));
    }

    public function testGetSumSecondsOfTimeString(): void
    {
        $this->assertEquals('44420', HelperDate::getSumSecondsOfTimeString('12:20:20'));
    }

    public function testGetSumMinutesOfTimeString(): void
    {
        $this->assertEquals('740', round(HelperDate::getSumMinutesOfTimeString('12:20:20')));
    }

    public function testGetWeekdayNumberFromTimestamp(): void
    {
        $this->assertEquals('3', HelperDate::getWeekdayNumberFromTimestamp(1498645220));
    }

    /**
     * @throws \Zend_Date_Exception
     */
    public function testGetMondayOfWeek(): void
    {
        $this->assertEquals('26.06.2017', HelperDate::getMondayOfWeek('2017-01-07')->toString('dd.MM.Y'));
    }

    /**
     * @throws \Exception
     * @throws \Gyselroth\Helper\Exception\LoggerException
     * @throws \Zend_Date_Exception
     */
    public function testGetDateDiff(): void
    {
        $this->assertEquals('2', HelperDate::getDateDiff(new Zend_Date(1498645220), new Zend_Date(1498745220)));
        $this->assertEquals('27', HelperDate::getDateDiff(new Zend_Date(1498645220), new Zend_Date(1498745220), 'hour'));
        $this->assertNull(HelperDate::getDateDiff(new Zend_Date(1498645220), new Zend_Date(1498745220), 'minute'));
    }

    public function testGetWeeksBetween(): void
    {
//        $this->assertSame(
//            date('W', strtotime('17.5.2017')),
//            20,
//            'check calendar week of date (17.5.2017)'
//        );

        $this->assertSame(
            '{"startWeek":"20","weeks":1,"endWeek":21}',
            json_encode(HelperDate::getWeeksBetween(strtotime('17.5.2017'), strtotime('29.5.2017'))),
            'getWeeksBetween function has several errors. See @todo for more information.'
        );

        $this->assertSame(
            '{"startWeek":"02","weeks":0,"endWeek":2}',
            json_encode(HelperDate::getWeeksBetween(strtotime('13.1.2017'), strtotime('15.1.2017'))));

        $this->assertSame('{"startWeek":"02","weeks":52,"endWeek":2}',
            json_encode(HelperDate::getWeeksBetween(strtotime('12.1.2016'), strtotime('14.1.2017'))));

        $this->assertSame('{"startWeek":"51","weeks":2,"endWeek":2}',
            json_encode(HelperDate::getWeeksBetween(strtotime('25.12.2016'), strtotime('14.1.2017'))));
    }

    public function testGetDaysBetween(): void
    {
//        $this->assertSame(2, HelperDate::getDaysBetween(strtotime('31.12.2016 12:00'),
//            strtotime('2.1.2017 0:00')), 'Should be 2, since new days start at 0:00');
        $this->assertSame(2, HelperDate::getDaysBetween(strtotime('31.12.2016 12:00'),
            strtotime('2.1.2017 0:01')));
    }

    public function testGetMonthNameByNumber(): void
    {
        $this->assertSame('März', HelperDate::getMonthNameByNumber(3));
        $this->assertSame('Mär', HelperDate::getMonthNameByNumber(3, true));
        $this->assertSame('Mai', HelperDate::getMonthNameByNumber(strtotime('12.5.2017')));
    }

    public function testGetWeekdayNameByNumber(): void
    {
        $this->assertSame('Dienstag', HelperDate::getWeekdayNameByNumber(2));
        $this->assertSame('Di', HelperDate::getWeekdayNameByNumber(2, true));
        $this->assertSame('Freitag', HelperDate::getWeekdayNameByNumber(strtotime('12.5.2017')));
    }

    /**
     * @throws \Gyselroth\Helper\Exception\DateException
     */
    public function testgetIcsDateFromDateString(): void
    {
        $this->assertSame('20170512', HelperDate::getIcsDateFromDateString('12.5.2017'));
        $this->assertSame('20170512T123456', HelperDate::getIcsDateFromDateString('12.5.2017 12:34:56', true));
        $this->expectException('Gyselroth\Helper\Exception\DateException');
        HelperDate::getIcsDateFromDateString('125.2017');
    }

    /**
     * @throws \Gyselroth\Helper\Exception\DateException
     */
    public function testGetIcsDateTimeFromDateString(): void
    {
        $this->assertSame('20170512T123456', HelperDate::getIcsDateTimeFromDateString('12.5.2017 12:34:56'));
    }

    public function testGetTimestampFirstDayOfCalendarWeek(): void
    {
        $this->assertSame(1483315200, HelperDate::getTimestampFirstDayOfCalendarWeek(1, 2017));
        $this->assertSame(1485129600, HelperDate::getTimestampFirstDayOfCalendarWeek(4, 17));
        $this->assertSame(1452470400, HelperDate::getTimestampFirstDayOfCalendarWeek(2, 2016));
    }

    public function testGetAgeByBirthYear(): void
    {
        $this->markTestSkipped('Result dependent on current year: the formula needed to test this is the same as the function being tested');
    }

    public function testGetClosestDate(): void
    {
        $this->assertSame(4, HelperDate::getClosestDate(5, [2, 1, 10, 9, 8, 4]));
    }

    public function testGetDateShifted(): void
    {
        $this->markTestSkipped('Function returns empty array');
    }

    /**
     * @throws \Zend_Date_Exception
     */
    public function testRenderTimerangeHumanReadable(): void
    {
        $this->assertSame('12. April 2017 bis 15. April 2017',
            HelperDate::renderTimerangeHumanReadable('12.4.2017', '15.4.2017'));

        $this->assertSame('30. Dezember 2016 bis 3. Januar 2017',
            HelperDate::renderTimerangeHumanReadable('30.12.2016', '3.1.2017'));

        $this->assertSame('12. April 2017 bis 15. April 2017', HelperDate::renderTimerangeHumanReadable('12.4.2017', '15.4.2017', 'de'));
//        $this->assertSame('December 30, 2016 until January 3, 2017', HelperDate::renderTimerangeHumanReadable('30.12.2016', '3.1.2017', 'en'), '"bis" is not being translated to English');
    }

    public function testRemoveMeridiem(): void
    {
        $this->assertSame('2', HelperDate::removeMeridiem('2pm'));
        $this->assertSame('2', HelperDate::removeMeridiem('2 pm'));
        $this->assertSame('12', HelperDate::removeMeridiem('12 am'));
    }

    public function testEnsureTimeStringHasSeconds(): void
    {
        $this->assertSame('12:34:00', HelperDate::ensureTimeStringHasSeconds('12:34'));
        $this->assertSame('12:34:56', HelperDate::ensureTimeStringHasSeconds('12:34:56'));
    }

    /**
     * @throws \Zend_Date_Exception
     * @throws \Zend_Locale_Exception
     */
    public function testConvertDateToUTC(): void
    {
        $this->assertSame('20170512T000000', HelperDate::convertDateToUTC('12.5.2017'));
        $this->assertSame('2017-05-12 12:34:56', HelperDate::convertDateToUTC(strtotime('12.5.2017 12:34:56'), 'yyyy-MM-dd HH:mm:ss'));
    }

    public function testConvertDelimitedDateString(): void
    {
        $this->assertSame('2017.5.12', HelperDate::convertDelimitedDateString('12-5-2017'));
        $this->assertSame('2017.5.12', HelperDate::convertDelimitedDateString('12"5"2017', '"'));
    }

    public function testGetZendDatePartByType(): void
    {
        $this->assertSame('mm', HelperDate::getZendDatePartByType('minUte'));
        $this->assertSame('U', HelperDate::getZendDatePartByType('nothing'));
    }

    public function testGetCurrentWeekAndYear(): void
    {
        $this->markTestSkipped('The function needed to test is identical');
    }

    public function testGetTimeOutOfTimeDateString(): void
    {
        $emptyString            = '';
        $correctTimeDateOrder   = '08:20 2019.09.30';
        $incorrectTimeDateOrder = '2019.09.30, 08:20';
        $noTime                 = 'hello test';

        $this->assertSame('08:20', HelperDate::getTimeOutOfTimeDateString($correctTimeDateOrder));

        // will not asserted as same - implement notice/warning if the first 5 characters are not a time
        // or update function that it will always find the time in a String
        $this->assertSame('08:20', HelperDate::getTimeOutOfTimeDateString($incorrectTimeDateOrder));

        // will output hello  even though it is not a time
        // update function that it will return a warning instead such as 'Given String is not a time'
        $this->assertSame('Given String is not a time', HelperDate::getTimeOutOfTimeDateString($noTime));

        // will out put empty String
        // eventually add a warning here as well
        $this->assertSame('', HelperDate::getTimeOutOfTimeDateString($emptyString));
    }

    public function testGetDayNumberOfWeekFromZendDateWithMonday(): void
    {
        $this->assertSame(1, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('23.09.2019')));
    }

    public function testGetDayNumberOfWeekFromZendDateWithTuesday(): void
    {
        $this->assertSame(2, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('24.09.2019')));
    }

    public function testGetDayNumberOfWeekFromZendDateWithWednesday(): void
    {
        $this->assertSame(3, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('25.09.2019')));
    }

    public function testGetDayNumberOfWeekFromZendDateWithThursday(): void
    {
        $this->assertSame(4, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('26.09.2019')));
    }

    public function testGetDayNumberOfWeekFromZendDateWithFriday(): void
    {
        $this->assertSame(5, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('27.09.2019')));
    }

    public function testGetDayNumberOfWeekFromZendDateWithSaturday(): void
    {
        $this->assertSame(6, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('28.09.2019')));
    }

    public function testGetDayNumberOfWeekFromZendDateWithSunday(): void
    {
        $this->assertSame(7, HelperDate::getDayNumberOfWeekFromZendDate(new Zend_Date('29.09.2019')));
    }

    public function testPassesWeekendMonday(): void
    {
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 1));
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 2));
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 3));
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 4));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 5));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 6));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('23.09.2019'), 7));

    }

    public function testPassesWeekendTuesday(): void
    {
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('24.09.2019'), 1));
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('24.09.2019'), 2));
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('24.09.2019'), 3));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('24.09.2019'), 4));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('24.09.2019'), 5));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('24.09.2019'), 6));

    }

    public function testPassesWeekendWednesday(): void
    {
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('25.09.2019'), 1));
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('25.09.2019'), 2));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('25.09.2019'), 3));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('25.09.2019'), 4));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('25.09.2019'), 5));

    }

    public function testPassesWeekendThursday(): void
    {
        $this->assertFalse(HelperDate::passesWeekend(new Zend_Date('26.09.2019'), 1));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('26.09.2019'), 2));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('26.09.2019'), 3));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('26.09.2019'), 4));
    }

    public function testPassesWeekendFriday(): void
    {
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('27.09.2019'), 1));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('27.09.2019'), 2));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('27.09.2019'), 3));
    }

    public function testPassesWeekendSaturday(): void
    {
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('28.09.2019'), 1));
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('28.09.2019'), 2));

    }

    public function testPassesWeekendSunday(): void
    {
        // expected to be false?
        $this->assertTrue(HelperDate::passesWeekend(new Zend_Date('29.09.2019'), 1));
    }

    public function testTimeSpansIntersect(): void
    {
        $notOverlappingStartTime1 = '07:45';
        $notOverlappingEndTime1   = '08:30';
        $notOverlappingStartTime2 = '09:45';
        $notOverlappingEndTime2   = '10:30';

        $OverlappingStartTime1 = '07:45';
        $OverlappingEndTime1   = '08:30';
        $OverlappingStartTime2 = '08:00';
        $OverlappingEndTime2   = '10:30';

        $StartTime1 = '07:45';
        $EndTime1   = '08:30';
        $StartTime2 = '08:30';
        $EndTime2   = '10:30';


        $this->assertTrue(HelperDate::timeSpansIntersect
            (
                $notOverlappingStartTime1,
                $notOverlappingEndTime1,
                $notOverlappingStartTime2,
                $notOverlappingEndTime2
            )
        );

        $this->assertFalse(HelperDate::timeSpansIntersect
            (
                $OverlappingStartTime1,
                $OverlappingEndTime1,
                $OverlappingStartTime2,
                $OverlappingEndTime2
            )
        );

        // should it be considered as overlapping or not???
        $this->assertFalse(HelperDate::timeSpansIntersect
            (
                $StartTime1,
                $EndTime1,
                $StartTime2,
                $EndTime2
            )
        );
    }

    public function testIsTimeSpan(): void
    {
        $isTimeSpan = '07:45 - 08:30';
        $isWrongFormatTimeSpan = '07:45-08:30';
        $isNotTimeSpan = '07:45';

        $this->assertTrue(HelperDate::isTimeSpan($isTimeSpan));

        // is actually a timespan but the format is not the same
        // eventually add more possibilities - such as no space here or instead of '-' 'bis'...
        $this->assertFalse(HelperDate::isTimeSpan($isWrongFormatTimeSpan));
        $this->assertFalse(HelperDate::isTimeSpan($isNotTimeSpan));


    }

    public function testIsDateStringFormattedTrue(): void
    {
        $this->assertTrue( HelperDate::isDateStringFormatted(
            '17.09.2019',
            2,
            2,
            4,
            '.'
        ));
    }

    public function testIsDateStringFormattedFalse(): void
    {
        $this->assertFalse( HelperDate::isDateStringFormatted(
            '17.09.2019',
            4,
            2,
            2,
            '.'
        ));
        $this->assertFalse( HelperDate::isDateStringFormatted(
            '17.09.2019',
            2,
            2,
            4
        ));
        $this->assertFalse( HelperDate::isDateStringFormatted(
            '17.09.2019',
            2,
            2,
            5
        ));
    }


    public function testSkipWeekendSaturday(): void
    {
        $this->assertEquals(new Zend_Date('30.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('28.09.2019'),
                false,
                false));

        $this->assertEquals(new Zend_Date('29.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('28.09.2019'),
                false,
                true));

        $this->assertEquals(new Zend_Date('28.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('28.09.2019'),
                true,
                true));
        $this->assertEquals(new Zend_Date('28.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('28.09.2019'),
                true,
                false));


    }

    public function testSkipWeekendSunday(): void
    {
        $this->assertEquals(new Zend_Date('30.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('29.09.2019'),
                false,
                false));
        $this->assertEquals(new Zend_Date('30.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('29.09.2019'),
                true,
                false));

        $this->assertEquals(new Zend_Date('29.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('29.09.2019'),
                false,
                true));
        $this->assertEquals(new Zend_Date('29.09.2019'),
            HelperDate::skipWeekend(new Zend_Date('29.09.2019'),
                true,
                true));
    }

}
