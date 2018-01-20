<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\EmailExtractor;

class EmailExtractorTest extends TestCase
{
    public function testExtract()
    {
        $dom = file_get_contents($this->url('/emails'));
        $extractor = new EmailExtractor();
        $result = $extractor->extract($dom, $this->url('/emails'));

        $this->assertArraySubset(['contact@example.com'], $result);
    }
}
