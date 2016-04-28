<?php

namespace Hedii\Extractors\Test;

use Hedii\Extractors\Extractor;

class ExtractorTest extends TestCase
{
    public function testSingular()
    {
        $extractor = new Extractor();
        $method = $this->getPrivateMethod(Extractor::class, 'singular');
        $result1 = $method->invokeArgs($extractor, ['urls']);
        $result2 = $method->invokeArgs($extractor, ['emails']);

        $this->assertEquals('url', $result1);
        $this->assertEquals('email', $result2);
    }

    public function testGetDocument()
    {
        $extractor = new Extractor();
        $method = $this->getPrivateMethod(Extractor::class, 'getDocument');
        $result = $method->invokeArgs($extractor, ['https://raw.githubusercontent.com/hedii/extractors/master/tests/example.txt']);

        $this->assertEquals('example', $result);
    }

    public function testSearchForMethodReturnsExtractorClassInstance()
    {
        $extractor = new Extractor();
        $result = $extractor->searchFor([]);

        $this->assertInstanceOf(Extractor::class, $result);
    }

    public function testAtMethodReturnsExtractorClassInstance()
    {
        $extractor = new Extractor();
        $result = $extractor->at('');

        $this->assertInstanceOf(Extractor::class, $result);
    }

    public function testExtractorFindUrls()
    {
        $extractor = new Extractor();
        $result = $extractor->searchFor(['urls'])->at('http://telecharger-videos-youtube.com/')->get();

        $this->assertArrayHasKey('urls', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('http://telecharger-videos-youtube.com/faq', $result['urls']));
        $this->assertTrue(in_array('http://telecharger-videos-youtube.com/faq#site-supportes', $result['urls']));
    }

    public function testExtractorFindEmails()
    {
        $extractor = new Extractor();
        $result = $extractor->searchFor(['emails'])->at('http://telecharger-videos-youtube.com/')->get();

        $this->assertArrayHasKey('emails', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('ton@email.com', $result['emails']));
    }

    public function testExtractorFindEmailsAndUrls()
    {
        $extractor = new Extractor();
        $result = $extractor->searchFor(['urls', 'emails'])->at('http://telecharger-videos-youtube.com/')->get();

        $this->assertArrayHasKey('urls', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('http://telecharger-videos-youtube.com/faq', $result['urls']));
        $this->assertTrue(in_array('http://telecharger-videos-youtube.com/faq#site-supportes', $result['urls']));
        $this->assertArrayHasKey('emails', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('ton@email.com', $result['emails']));
    }

    public function testResetMethod()
    {
        $extractor = new Extractor();
        $extractor->searchFor(['urls'])->at('http://telecharger-videos-youtube.com/')->get();
        $result = $extractor->searchFor(['emails'])->at('http://telecharger-videos-youtube.com/')->get();

        $this->assertArrayNotHasKey('urls', $result);
    }
}
