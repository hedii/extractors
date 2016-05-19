<?php

namespace Hedii\Extractors;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class MetaExtractor extends Extractor
{
    /**
     * An array of found meta data.
     *
     * @var array
     */
    protected $meta;

    /**
     * Extract the meta data contained in the html head of the provided dom.
     *
     * @param mixed $dom
     * @param string $url  Will not be used.
     * @return array
     */
    public function extract($dom, $url)
    {
        $this->resetMeta();

        $crawler = new DomCrawler($dom, $url);
        $text = $crawler->html();
        $meta_data = [];


        preg_match('/<title>(.+)<\/title\>/Uims', $text, $matches);
        if ($matches) {
           $meta_data['title'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*name=\"description\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['description'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*name=\"keywords\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['keywords'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*name=\"author\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['author'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        // todo - optimalize to one preg_match for all og:*
        preg_match('/<meta\s*property=\"og:title\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['og:title'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"article:section\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['article:section'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"article:published_time\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['article:published_time'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"article:modified_time\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
          // var_dump($matches);
            $meta_data['article:modified_time'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"og:description\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['og:description'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"og:type\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['og:type'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"og:url\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['og:url'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"og:site_name\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['og:site_name'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        preg_match('/<meta\s*property=\"og:image\"\s*content=\"(.+)\"\s*[\/]*\>/Uims', $text, $matches);
        if ($matches) {
            $meta_data['og:image'] = preg_replace('/\s+/', ' ', trim(htmlspecialchars_decode($matches[1])));
        }

        $this->meta = $meta_data;

        return $this->meta;
    }

    /**
     * Reset the meta data array.
     *
     * @return void
     */
    private function resetMeta()
    {
        $this->meta = [];
    }
}