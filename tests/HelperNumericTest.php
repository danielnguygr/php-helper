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

use Gyselroth\Helper\HelperNumeric;

class HelperNumericTest extends HelperTestCase
{
    public function testFormatAmountDigits(): void
    {
        $this->assertEquals('01', HelperNumeric::formatAmountDigits(1, 2));

        $this->assertEquals(HelperNumeric::formatAmountDigits(1, 1), '1');
        $this->assertEquals(HelperNumeric::formatAmountDigits(1, 2), '01');
        $this->assertEquals(HelperNumeric::formatAmountDigits(1, 3), '001');
        $this->assertEquals(HelperNumeric::formatAmountDigits(100, 1), '100');
    }

    /**
     * Test: HelperNumeric::intImplode
     */
    public function testIntImplode(): void
    {
        $this->assertSame(HelperNumeric::intImplode([3, 5, 1, 3, 4, 2, 2], '-', false, true), '3-5-1-4-2');
        $this->assertSame(HelperNumeric::intImplode([3, 5, 1, 3, 4, 2.3, 2.7]), '1,2,2,3,4,5');
    }

    /**
     * Test: HelperNumeric::intExplode
     */
    public function testIntExplode(): void
    {
        $this->assertSame(json_encode(HelperNumeric::intExplode('3-5-1-4-2-2', '-')), '[3,5,1,4,2,2]');
//        $this->assertSame(json_encode(HelperNumeric::intExplode(null)), '[]');
        $this->assertSame(json_encode(HelperNumeric::intExplode('')), '[0]');
    }

    /**
     * Test: HelperNumeric::floatExplode
     */
    public function testFloatExplode(): void
    {
        $this->assertSame(json_encode(HelperNumeric::floatExplode('3.3-5-1.5-4-2-2', '-')), '[3.3,5,1.5,4,2,2]');
        $this->assertSame(json_encode(HelperNumeric::floatExplode('3.3-5-1.5-4-2-2', '.')), '[3,3,5]');
//        $this->assertSame(json_encode(HelperNumeric::floatExplode(null, '.', false)), '[]');
    }

    /**
     * Test: HelperNumeric::calcBytesSize
     */
    public function testCalcBytesSize(): void
    {
        $this->assertSame(json_encode(HelperNumeric::calcBytesSize(234)), '{"size":234,"unit":"B"}');
        $this->assertSame(json_encode(HelperNumeric::calcBytesSize(1150)), '{"size":1.1,"unit":"KB"}');
        $this->assertSame(json_encode(HelperNumeric::calcBytesSize(4500000)), '{"size":4.4,"unit":"MB"}');
    }

    public function testGetPercentage(): void
    {
        $empty                  = 0;
        $amountFull             = 125;
        $amountPartialEqualFull = 125;
        $amountPartial          = 70;

        $amountFullFloat             = 107.2;
        $amountPartialEqualFullFloat = 107.2;
        $amountPartialFloat          = 45.7;

        assertSame(56, HelperNumeric::getPercentage($amountFull, $amountPartial));
        assertSame(100, HelperNumeric::getPercentage($empty, $amountPartial));
        assertSame(100, HelperNumeric::getPercentage($amountFull, $amountPartialEqualFull));

        assertSame(42.630597, HelperNumeric::getPercentage($amountFullFloat, $amountPartialFloat));
        assertSame(100, HelperNumeric::getPercentage($amountFullFloat, $amountPartialEqualFullFloat));


    }

    public function testRemoveEmptyItemsFromIDsCsv(): void
    {
        $nothingToRemove = 'Das, ist, Ein, Test';
        $removeSpaces = 'Hier, , müssen, Dinge, , entfernt, , werden';
        $remove = 'Hier,, müssen, Dinge,, entfernt,, werden';

        $this->assertSame('Das, ist, Ein, Test', HelperNumeric::removeEmptyItemsFromIDsCsv($nothingToRemove));
        $this->assertSame('Hier, müssen, Dinge, entfernt, werden', HelperNumeric::removeEmptyItemsFromIDsCsv($remove));

        // really only works if it is empty - characters such as spaces will not be removed
        // eventually update function or add notice
        $this->assertSame('Hier, müssen, Dinge, entfernt, werden', HelperNumeric::removeEmptyItemsFromIDsCsv($removeSpaces));


    }

}
