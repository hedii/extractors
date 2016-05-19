<?php

namespace Hedii\Extractors;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class PhoneExtractor extends Extractor
{
    /**
     * An array of found phone numbers.
     *
     * @var array
     */
    protected $phones;

    /**
     * Extract the phone numbers contained in the body of the provided dom.
     *
     * @param mixed $dom
     * @param string $url  Will not be used.
     * @return array
     */
    public function extract($dom, $url)
    {
        $this->resetPhones();

        $that = $this;

        $crawler = new DomCrawler($dom, $url);
        $text = $crawler->filter('body')->text();

        // First extract phone numbers from links with 'tel:' action
        $href_phones = $crawler->filter('a')->count() > 0 ? $crawler->filter('a')->each( function ($node) use ($that) {
            $href = $node->attr('href');
            if ( $that->startsWith(strtolower($href), ['tel:']) ) {
                return trim(ltrim($href,'tel:'));
            }
        }) : [];

        // Now regex match body text to find telephones
        $phone_regex = "/(?:(?:\\(?(?:00|\\+)([1-4]\\d\\d|[1-9]\\d?)\\)?)?[\\-\\ \\\\\\/]?)?((?:\\(?\\d{1,}\\)?[\\-\\ \\\\\\/]?){0,})(?:[\\-\\ \\\\\\/]?(?:#|ext\\.?|extension|x)[\\-\\ \\\\\\/]?(\\d+))?/mi";

        // TODO: make so that it doesnt return empty strings initially
        preg_match_all($phone_regex, $text, $matches, PREG_SET_ORDER);

        $phone_delimiters = array(' ', '+', '(', ')', '-', '_', '/', '\\');

        $body_phones = [];

        if ($matches) {
            foreach ($matches as $key => $match) {
                $original = trim(isset($match[0])?$match[0]:'');
                $country = str_replace($phone_delimiters, '', trim(isset($match[1])?$match[1]:''));
                $number = str_replace($phone_delimiters, '', trim(isset($match[2])?$match[2]:''));
                $ext = str_replace($phone_delimiters, '', trim(isset($match[3])?$match[3]:''));
                $full_number = ( $country !== '' ? '+'.$country.' ' : '' ). $number . ( $ext !== '' ? ', '.$ext : '' );

                if ( $number !== '' && trim($full_number) !== '' && strlen($number) > 7 ) {
                    $body_phones[] = $full_number;
                }
            }
        }

        // Join href phones with body found phones
        $this->phones = array_unique( array_merge($href_phones,$body_phones) );

        return $this->phones;
    }

    /**
     * Reset the phone numbers array.
     *
     * @return void
     */
    private function resetPhones()
    {
        $this->phones = [];
    }
}