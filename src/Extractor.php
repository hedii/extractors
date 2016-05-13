<?php

namespace Hedii\Extractors;

class Extractor
{
    /**
     * The target url.
     *
     * @var string
     */
    protected $url;

    /**
     * The target dom document.
     *
     * @var mixed null|string
     */
    protected $dom;

    /**
     * An array that holds the the search info.
     *
     * @var array
     */
    protected $resourcesMap = [];

    /**
     * The found resources array.
     *
     * @var array
     */
    protected $resources = [];

    /**
     * Set the resource map that will hold the the search info.
     *
     * @param array $resourceTypes
     * @return $this
     */
    public function searchFor(array $resourceTypes)
    {
        $this->reset();

        $resourceTypes = array_map([$this, 'singular'], $resourceTypes);

        if (in_array('url', $resourceTypes)) {
            $this->resourcesMap['urls'] = [
                'extractor' => new UrlExtractor(),
                'resources' => null
            ];
        }

        if (in_array('email', $resourceTypes)) {
            $this->resourcesMap['emails'] = [
                'extractor' => new EmailExtractor(),
                'resources' => null
            ];
        }

        if (in_array('phone', $resourceTypes)) {
            $this->resourcesMap['phone'] = [
                'extractor' => new PhoneExtractor(),
                'resources' => null
            ];
        }

        return $this;
    }

    /**
     * Set the target url and get dom from this url.
     *
     * @param string $url
     * @return $this
     */
    public function at($url)
    {
        $this->url = $url;
        $this->dom = $this->getDocument($url);

        return $this;
    }

    /**
     * Get the resources via the extractor.
     *
     * @return array
     */
    public function get()
    {
        foreach ($this->resourcesMap as $key => $value) {
            $this->resources[$key] = $value['extractor']->extract($this->dom, $this->url);
        }

        return $this->resources;
    }

    /**
     * Like file_get_contents, but with curl: can handle https.
     *
     * @param string $url
     * @return mixed null|string
     */
    protected function getDocument($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result ? $result : null;
    }

    /**
     * Transform a string to its singular if it is plural.
     *
     * @param string $value
     * @return string
     */
    protected function singular($value)
    {
        return rtrim($value, 's');
    }

    /**
     * An helper that check if a string 'haystack' ends with
     * a string 'needle'.
     *
     * @source laravel
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    protected function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reset the resources map and resources arrays.
     *
     * @return void
     */
    protected function reset()
    {
        $this->resourcesMap = [];
        $this->resources = [];
    }
}