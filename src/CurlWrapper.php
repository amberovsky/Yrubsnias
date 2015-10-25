<?php
/**
 * Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

namespace Sainsbury;

/**
 * Curl Wrapper
 */
class CurlWrapper {
    /** @var null|resource curl resource */
    private $resource;

    /**
     * CurlWrapper constructor
     */
    public function __construct() {
        $this->resource = null;
    }

    /**
     * @see curl_init
     */
    public function init() {
        $this->resource = curl_init();
    }

    /**
     * @see curl_setopt
     *
     * @param mixed $option
     * @param mixed $value
     *
     * @return mixed
     */
    public function setOption($option, $value) {
        return curl_setopt($this->resource, $option, $value);
    }

    /**
     * @see curl_exec
     *
     * @return mixed
     */
    public function exec() {
        return curl_exec($this->resource);
    }

    /**
     * @see curl_error
     *
     * @return string
     */
    public function error() {
        return curl_error($this->resource);
    }

    /**
     * @see curl_errno
     *
     * @return int
     */
    public function errno() {
        return curl_errno($this->resource);
    }
}
