<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\PhoneExtractor;

class PhoneExtractorTest extends TestCase
{
    public function testExtract()
    {
        $dom = file_get_contents('http://thatsthem.com/phone/818-795-8895');
        $extractor = new PhoneExtractor();
        $result = $extractor->extract($dom, 'http://thatsthem.com/phone/818-795-8895');

        $this->assertArraySubset(['8187958895'], $result);
    }
}