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

use Gyselroth\Helper\HelperImage;

class HelperImageTest extends HelperTestCase
{
    public function testCropExistingImage(): void
    {
        $this->assertTrue(HelperImage::cropImage(
            'tests/Fixtures/data/files/images/Lorem_Ipsum_Arial.png',
            900,
            900
        ));
    }

    public function testCropNotExistingImage(): void
    {
        $this->assertFalse(HelperImage::cropImage(
            'tests/Fixtures/data/files/images/Test.png',
            900,
            900
        ));
    }


    // Invalid Error -> throw exception in the function instead??
    public function testCropImageWithInvalidSubtrahend(): void
    {
        $this->assertFalse(HelperImage::cropImage(
            'tests/Fixtures/data/files/images/Lorem_Ipsum_Arial.png',
            1900,
            1900
        ));
    }

    public function testSaveThumbnailOfExistingFile(): void
    {
        $this->assertTrue(HelperImage::saveThumbnail(
            'tests/Fixtures/data/files/images/Lorem_Ipsum_Aal.png',
            'tests/Fixtures/data/files/thumbnails/',
            200,200,9));

    }

}
