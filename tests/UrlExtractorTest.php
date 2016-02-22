<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\UrlExtractor;

class UrlExtractorTest extends TestCase
{
    public function testExtract()
    {
        $dom = file_get_contents('http://example.com');
        $extractor = new UrlExtractor();
        $result = $extractor->extract($dom, 'http://example.com');

        $this->assertArraySubset(['http://www.iana.org/domains/example'], $result);
    }
}