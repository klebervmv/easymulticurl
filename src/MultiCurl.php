<?php


namespace klebervmv;

/**
 * Class MultiCurl
 * @package App\Sources
 */
class MultiCurl
{
    /**
     * @var resource
     */
    private $multiCurl;
    /** @var array */
    private $arrCurlInit;

    /**
     * MultiCurl constructor.
     */
    public function __construct()
    {
        $this->multiCurl = curl_multi_init();
    }

    /**
     * @param $curlInit
     * @return $this
     */
    public function addCurl(string $instanceName, $curlInit): self
    {
        $this->arrCurlInit[$instanceName] = $curlInit;
        curl_multi_add_handle($this->multiCurl, $curlInit);
        return $this;
    }

    /**
     * @return $this
     */
    public function execute(): self
    {
        do {
            curl_multi_exec($this->multiCurl, $running);
            curl_multi_select($this->multiCurl);
        } while ($running > 0);

        foreach ($this->arrCurlInit as $itemCurl) {
            curl_multi_remove_handle($this->multiCurl, $itemCurl);
        }
        curl_multi_close($this->multiCurl);
        return $this;
    }

    /**
     * @param string $instanceName
     * @return int
     */

    public function getHttpCode(string $instanceName): int
    {
        return curl_getinfo($this->arrCurlInit[$instanceName])['http_code'];
    }

    /**
     * @param string $instanceName
     * @return array|null
     */
    public function getResult(string $instanceName): ?array
    {
        $result = json_decode(curl_multi_getcontent($this->arrCurlInit[$instanceName]), true);
        curl_close($this->arrCurlInit[$instanceName]);
        return $result;
    }

}