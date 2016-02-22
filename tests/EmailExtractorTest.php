<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\EmailExtractor;

class EmailExtractorTest extends TestCase
{
    public function testExtract()
    {
        $dom = file_get_contents('http://telecharger-videos-youtube.com/');
        $extractor = new EmailExtractor();
        $result = $extractor->extract($dom, 'http://telecharger-videos-youtube.com/');

        $this->assertArraySubset(['ton@email.com'], $result);
    }
}