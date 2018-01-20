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
        $result3 = $method->invokeArgs($extractor, ['phones']);

        $this->assertEquals('url', $result1);
        $this->assertEquals('email', $result2);
        $this->assertEquals('phone', $result3);
    }

    public function testGetDocument()
    {
        $extractor = new Extractor();
        $method = $this->getPrivateMethod(Extractor::class, 'getDocument');
        $result = $method->invokeArgs($extractor, [$this->url('/example')]);

        $this->assertEquals('example', trim($result));
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
        $result = $extractor->searchFor(['urls'])->at($this->url('/links'))->get();

        $this->assertArrayHasKey('urls', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('http://example.com/link1', $result['urls']));
        $this->assertTrue(in_array('http://example.com/link1', $result['urls']));
    }

    public function testExtractorFindEmails()
    {
        $extractor = new Extractor();
        $result = $extractor->searchFor(['emails'])->at($this->url('/emails'))->get();

        $this->assertArrayHasKey('emails', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('contact@example.com', $result['emails']));
    }

    public function testExtractorFindEmailsAndUrls()
    {
        $extractor = new Extractor();
        $result = $extractor->searchFor(['urls', 'emails'])->at($this->url('/emails'))->get();

        $this->assertArrayHasKey('urls', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('http://example.com/link1', $result['urls']));
        $this->assertTrue(in_array('http://example.com/link2', $result['urls']));
        $this->assertArrayHasKey('emails', $result);
        $this->assertTrue(!empty($result));
        $this->assertTrue(in_array('contact@example.com', $result['emails']));
    }

    public function testResetMethod()
    {
        $extractor = new Extractor();
        $extractor->searchFor(['urls'])->at($this->url('/emails'))->get();
        $result = $extractor->searchFor(['emails'])->at($this->url('/emails'))->get();

        $this->assertArrayNotHasKey('urls', $result);
    }
}
