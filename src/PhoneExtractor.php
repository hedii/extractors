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

        // $phone_regex = '/\b([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})\b/m';
        $phone_regex = "/(?:(?:\\(?(?:00|\\+)([1-4]\\d\\d|[1-9]\\d?)\\)?)?[\\-\\ \\\\\\/]?)?((?:\\(?\\d{1,}\\)?[\\-\\ \\\\\\/]?){0,})(?:[\\-\\ \\\\\\/]?(?:#|ext\\.?|extension|x)[\\-\\ \\\\\\/]?(\\d+))?/mi";

        $crawler = new DomCrawler($dom, $url);


        // 1. Extract phones from links with 'tel:' and 'phone:' action

        // Crawl links for href phones
        $href_phones = $crawler->filter('a')->count() > 0 ? $crawler->filter('a')->each( function ($node) use ($that) {
        	$href = strtolower($node->attr('href'));
            if ( $that->startsWith($href, ['tel:']) || $that->startsWith($href, ['phone:']) || $that->startsWith($href, ['telephone:']) || $that->startsWith($href, ['call:']) || $that->startsWith($href, ['number:'])) {
                $phone = $that->trimPhone($href);
                return $phone;
            }
        }) : [];

        // Cleanup found link phones
        $href_phones = array_values( array_filter( array_unique( $href_phones ) ) );


        // 2. Extract phones from website body text

        $body_phones = [];

        // Text we will be searching in
        $search_text = (string) ( $crawler->filter('body')->count() > 0 ? $crawler->filter('body')->text() : '' );

        // Split text into smaller chunks
        // $search_text_chunks = str_split($search_text,80000);

        // for ($i=0; $i < count($search_text_chunks); $i++) {

        	// Regex match phones
	        try {
	        	preg_match_all($phone_regex, $search_text, $matches);
	        	$regex_valid = true;
	        } catch (Exception $e) {
	        	// echo $e->getMessage();
	        	$regex_valid = false;
	        }

	        // No regex errors returned
		    if (preg_last_error() === PREG_NO_ERROR && $regex_valid === true) {

		        //foreach ($matches[0] as $match) {
		        //    $body_phones[] = $that->trimPhone($match);
		        //}

		        $phone_delimiters = array(' ', '+', '(', ')', '-', '_', '/', '\\');

		        if ($matches) {
		            foreach ($matches as $key => $match) {
		                $original = trim(isset($match[0])?$match[0]:'');
		                $country = str_replace($phone_delimiters, '', trim(isset($match[1])?$match[1]:''));
		                $number = str_replace($phone_delimiters, '', trim(isset($match[2])?$match[2]:''));
		                $ext = str_replace($phone_delimiters, '', trim(isset($match[3])?$match[3]:''));
		                $full_number = ( $country !== '' ? '+'.$country.' ' : '' ). $number . ( $ext !== '' ? ', '.$ext : '' );

		                if ( $number !== '' && trim($full_number) !== '' && strlen($number) > 7 && strlen($full_number) < 24 ) {
		                    $body_phones[] = $full_number;
		                }
		            }
		        }
		    }

        // }

        // Cleanup found body phones
		$body_phones = array_values( array_filter( array_unique( $body_phones ) ) );

        // Join href phones with body found phones, also filter empty and unique only
        $this->phones = array_values( array_filter( array_unique( array_merge($href_phones,$body_phones) ) ) );

        return $this->phones;
    }

    /**
     * Trim phone
     *
     * @return void
     */
    private function trimPhone($str) {
        $str = strtolower($str);
        if ( $this->startsWith(trim($str), ['tel:']) ) {
            $str = trim(str_replace('tel:','',$str));
        }
        else if ( $this->startsWith(trim($str), ['phone:']) ) {
            $str = trim(str_replace('phone:','',$str));
        }
        else if ( $this->startsWith(trim($str), ['telephone:']) ) {
            $str = trim(str_replace('telephone:','',$str));
        }
        else if ( $this->startsWith(trim($str), ['call:']) ) {
            $str = trim(str_replace('call:','',$str));
        }
        else if ( $this->startsWith(trim($str), ['number:']) ) {
            $str = trim(str_replace('number:','',$str));
        }

        $str = trim(htmlentities(urldecode($str), ENT_QUOTES | ENT_IGNORE, "UTF-8"));

        return $str;
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