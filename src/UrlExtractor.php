<?php

namespace Hedii\Extractors;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class UrlExtractor extends Extractor
{
    /**
     * An array of found urls.
     *
     * @var array
     */
    protected $urls;

    /**
     * Extract the urls contained in the body of the provided dom.
     *
     * @param mixed $dom
     * @param string $url
     * @return array
     */
    public function extract($dom, $url)
    {
        $this->resetUrls();
        $crawler = new DomCrawler($dom, $url);
        $links = $crawler->filter('body a')->links();

        foreach ($links as $link) {
            if (!in_array($link->getUri(), $this->urls)) {
            	if ($this->url === 'http://null') {
            		$this->urls[] = $link->getNode()->getAttribute('href');
            	} else {
            		$this->urls[] = $link->getUri();
            	}
            }
        }

        return $this->urls;
    }

    /**
     * Reset the urls array.
     *
     * @return void
     */
    private function resetUrls()
    {
        $this->urls = [];
    }
}