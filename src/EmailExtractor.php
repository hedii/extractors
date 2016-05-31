<?php

namespace Hedii\Extractors;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class EmailExtractor extends Extractor
{
    /**
     * An array of found emails.
     *
     * @var array
     */
    protected $emails;

    /**
     * An array of media files: some media file name could
     * be treated as email if we don't set a filter with
     * these extensions.
     *
     * @var array
     */
    protected $mediaExtensions = [
        '.jpg',
        '.jp2',
        '.jpeg',
        '.raw',
        '.png',
        '.gif',
        '.tiff',
        '.bmp',
        '.pdf',
        '.svg',
        '.fla',
        '.swf',
        '.css',
        '.js',
        '.html',
        '.htm',
        '.php',
    ];

    /**
     * Extract the emails contained in the body of the provided dom.
     *
     * @param mixed $dom
     * @param string $url  Will not be used.
     * @return array
     */
    public function extract($dom, $url)
    {
        $this->resetEmails();

        $that = $this;

        $crawler = new DomCrawler($dom, $url);
        $text = (string) ( $crawler->filter('body')->count() > 0 ? $crawler->filter('body')->text() : '' );

        // First extract emails from links with 'mailto:' and 'mail:' action
        $href_emails = $crawler->filter('body a')->count() > 0 ? $crawler->filter('body a')->each( function ($node) use ($that) {
            $href = strtolower($node->attr('href'));
            if ( $that->startsWith(strtolower($href), ['mailto:']) ) {
                return trim(ltrim($href,'mailto:'));
            }
            else if ( $that->startsWith(strtolower($href), ['mail:']) ) {
                return trim(ltrim($href,'mail:'));
            }
        }) : [];

        $body_emails = [];

        preg_match_all('/[a-z\d._%+-]+[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', $text, $matches);

        foreach ($matches[0] as $match) {
            if (filter_var($match, FILTER_VALIDATE_EMAIL)) {
                if ($this->endsWith(strtolower($match), $this->mediaExtensions)) {
                    continue;
                }
                $body_emails[] = strtolower(trim($match));
            }
        }

        // Join href emails with body found emails, also filter empty and unique only
        $this->emails = array_filter( array_unique( array_merge($href_emails,$body_emails) ) );

        return $this->emails;
    }

    /**
     * Reset the emails array.
     *
     * @return void
     */
    private function resetEmails()
    {
        $this->emails = [];
    }
}