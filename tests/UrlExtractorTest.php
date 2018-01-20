<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\UrlExtractor;

class UrlExtractorTest extends TestCase
{
    public function testExtract()
    {
        $dom = file_get_contents($this->url('/links'));
        $extractor = new UrlExtractor();
        $result = $extractor->extract($dom, $this->url('/links'));

        $this->assertArraySubset(['http://example.com/link1'], $result);
    }
}
