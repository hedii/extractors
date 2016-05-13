<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\TelephoneExtractor;

class TelephoneExtractorTest extends TestCase
{
    public function testExtract()
    {
        $dom = file_get_contents('http://thatsthem.com/phone/818-795-8895');
        $extractor = new TelephoneExtractor();
        $result = $extractor->extract($dom, 'http://thatsthem.com/phone/818-795-8895');

        $this->assertArraySubset(['8187958895'], $result);
    }
}