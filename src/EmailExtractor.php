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
        // $text = (string) ( $crawler->filter('body')->count() > 0 ? $crawler->filter('body')->text() : '' );

        // First extract emails from links with 'mailto:' and 'mail:' action
        $href_emails = $crawler->filter('a')->count() > 0 ? $crawler->filter('a')->each( function ($node) use ($that) {
            $href = strtolower($node->attr('href'));
            if ( $that->startsWith($href, ['mailto:']) || $that->startsWith($href, ['mail:']) || $that->startsWith($href, ['email:']) ) {
                return $that->trimEmail($href);
            }
        }) : [];

        $href_emails = array_values( array_filter( array_unique( $href_emails ) ) );

        $body_emails = [];

        preg_match_all('/[a-z\d._%+-]+[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', $dom, $matches);

        foreach ($matches[0] as $match) {
            if (filter_var($that->trimEmail($match), FILTER_VALIDATE_EMAIL)) {
                if ($this->endsWith(strtolower($match), $this->mediaExtensions)) {
                    continue;
                }
                $body_emails[] = $that->trimEmail($match);
            }
        }

        $body_emails = array_values( array_filter( array_unique( $href_emails ) ) );

        // Join href emails with body found emails, also filter empty and unique only
        $this->emails = array_values( array_filter( array_unique( array_merge($href_emails,$body_emails) ) ) );

        var_dump($this->emails);


        return $this->emails;
    }

    /**
     * Trim email
     *
     * @return void
     */
    private function trimEmail($str) {
    	$str = strtolower($str);
	    if ( $this->startsWith(trim($str), ['mailto:']) ) {
	        $str = trim(str_replace('mailto:','',$str));
	    }
	    else if ( $this->startsWith(trim($str), ['mail:']) ) {
	        $str = trim(str_replace('mail:','',$str));
	    }
	    else if ( $this->startsWith(trim($str), ['email:']) ) {
	        $str = trim(str_replace('email:','',$str));
	    }

	    $str = trim(htmlentities(urldecode($str), ENT_QUOTES | ENT_IGNORE, "UTF-8"));

	    return $str;
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