<?php
/**
 * Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

namespace Sainsbury;

use RuntimeException;

/**
 * Main Application class
 */
class Application {
    /** url with fruits */
    const URL = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=&numlangId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";

    /** @var CurlWrapper Curl wrapper */
    private $CurlWrapper;

    /** @var  Parser  */
    private $Parser;

    /**
     * Application constructor
     *
     * @param CurlWrapper $CurlWrapper
     * @param Parser $Parser
     */
    public function __construct(CurlWrapper $CurlWrapper, Parser $Parser) {
        libxml_use_internal_errors(true);

        $this->CurlWrapper = $CurlWrapper;
        $this->Parser = $Parser;
    }

    /**
     * Init curl with parameters and fetch URL value
     *
     * @param string $url url
     *
     * @return string url value
     *
     * @throws RuntimeException when cURL fails
     */
    public function fetchURL($url) {
        $this->CurlWrapper->init();
        $this->CurlWrapper->setOption(CURLOPT_USERAGENT, "Sainsbury's Test Bot");
        $this->CurlWrapper->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->CurlWrapper->setOption(CURLOPT_FOLLOWLOCATION, true);
        $this->CurlWrapper->setOption(CURLOPT_COOKIEFILE, '');
        $this->CurlWrapper->setOption(CURLOPT_URL, $url);

        $result = $this->CurlWrapper->exec();

        if (($result === false) || ($this->CurlWrapper->errno() != 0)) {
            throw new RuntimeException("cURL failed, message: [" . $this->CurlWrapper->error() . "], code: [" .
                $this->CurlWrapper->errno() . "]");
        }

        return $result;
    }

    /**
     * @param Fruit[] $Fruits fruits data
     *
     * @return string JSON-formatted output string
     */
    protected function getOutput(array $Fruits) {
        $total = 0.0;

        $output = [
            'results'   => array_map(function (Fruit $Fruit) use (&$total) {
                $total += $Fruit->unitPrice;

                return [
                    'title'         => $Fruit->title,
                    'size'          => round($Fruit->size / 1024, 2) . 'kb',
                    'unit_price'    => round($Fruit->unitPrice, 2),
                    'description'   => $Fruit->description,
                ];
            }, $Fruits),
            'total'     => round($total, 2),
        ];

        return json_encode($output, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * @param string $url what URL to parse
     *
     * @return string result in JSON format
     *
     * @throw RuntimeException on cURL failure
     */
    public function run($url = self::URL) {
        return $this->getOutput($this->Parser->getFruits(
            $this->fetchURL($url),
            function ($detailsUrl) {
                $result = $this->fetchURL($detailsUrl);

                return [$this->Parser->parseFruitDescription($result), mb_strlen($result, '8bit')];
            }
        ));
    }
}
