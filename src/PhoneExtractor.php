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


        $crawler = new DomCrawler($dom, $url);
        $text = $crawler->filter('body')->text();

        // TODO: make so that it doesnt return empty strings initially
        preg_match_all("/(?:(?:\\(?(?:00|\\+)([1-4]\\d\\d|[1-9]\\d?)\\)?)?[\\-\\.\\ \\\\\\/]?)?((?:\\(?\\d{1,}\\)?[\\-\\.\\ \\\\\\/]?){0,})(?:[\\-\\.\\ \\\\\\/]?(?:#|ext\\.?|extension|x)[\\-\\.\\ \\\\\\/]?(\\d+))?/mi", $text, $matches, PREG_SET_ORDER);

		$phone_delimiters = array(' ', '+', '(', ')', '-', '_', '/', '\\');

		if ($matches) {
			foreach ($matches as $key => $match) {
				$original = trim(isset($match[0])?$match[0]:'');
				$country = str_replace($phone_delimiters, '', trim(isset($match[1])?$match[1]:''));
				$number = str_replace($phone_delimiters, '', trim(isset($match[2])?$match[2]:''));
				$ext = str_replace($phone_delimiters, '', trim(isset($match[3])?$match[3]:''));
				$full_number = ( $country !== '' ? '+'.$country.' ' : '' ). $number . ( $ext !== '' ? ', '.$ext : '' );
				if ( $number !== '' && trim($full_number) !== '' && strlen($number) > 7 ) {
					$this->phones[] = $full_number;
				}
			}
		}

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