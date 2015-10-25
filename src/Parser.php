<?php
/**
 * Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

namespace Sainsbury;

use DOMDocument;
use DOMXPath;
use DOMNode;
use DOMElement;

/**
 * Parses received data
 */
class Parser {
    /** @var  Fruit[] parsed fruits */
    private $Fruits;

    /**
     * Parser constructor
     */
    public function __construct() {
        $this->Fruits = [];
    }

    /**
     * @param string $rawDetailsData raw detailed fruit data
     *
     * @return string fruit description
     */
    public function parseFruitDescription($rawDetailsData) {
        $FruitDOMDocument = new DOMDocument();
        $FruitDOMDocument->loadHTML($rawDetailsData);

        foreach ($FruitDOMDocument->getElementsByTagName('meta') as $FruitMeta) {
            /** @var DOMElement $FruitMeta */

            if ($FruitMeta->getAttribute('name') == 'description') {
                return $FruitMeta->getAttribute('content');
            }
        }

        return '<NO META DESCRIPTION>';
    }

    /**
     * @param DOMXPath $DOMXPath
     * @param DOMNode $DOMNode
     *
     * @return string fruit title
     */
    protected function getTitle(DOMXPath $DOMXPath, DOMNode $DOMNode) {
        return trim($DOMXPath
            ->query("./div/div[contains(@class, 'productInfo')]/h3/a/text()", $DOMNode)->item(0)->textContent);
    }

    /**
     * @param DOMXPath $DOMXPath
     * @param DOMNode $DOMNode
     *
     * @return string fruit unit price
     */
    protected function getUnitPrice(DOMXPath $DOMXPath, DOMNode $DOMNode) {
        return ltrim(trim(
                $DOMXPath
                    ->query("div/div/div/div/div/p[contains(@class, 'pricePerUnit')]/text()", $DOMNode)
                    ->item(0)
                    ->textContent
            ), "£");
    }

    /**
     * @param DOMXPath $DOMXPath
     * @param DOMNode $DOMNode
     *
     * @return string fruit details url
     */
    protected function getDetailsUrl(DOMXPath $DOMXPath, DOMNode $DOMNode) {
        return trim($DOMXPath
            ->query("div/div[contains(@class, 'productInfo')]/h3/a/@href", $DOMNode)->item(0)->textContent);
    }

    /**
     * @return Fruit[] fruits data
     */
    public function getFruits($rawData, callable $FnLoadFruitDetails) {
        $DOM = new DOMDocument();
        $DOM->loadHTML($rawData);

        $XPath = new DOMXPath($DOM);

        foreach ($XPath->query("//div[contains(@class, 'productInner')]") as $FruitDOM) {
            /** @var DOMNode $FruitDOM */

            $title = $this->getTitle($XPath, $FruitDOM);
            $unitPrice = $this->getUnitPrice($XPath, $FruitDOM);
            $detailsUrl = $this->getDetailsUrl($XPath, $FruitDOM);

            list($description, $size) = $FnLoadFruitDetails($detailsUrl);
            $Fruit = new Fruit($title, $description, $unitPrice, $size);

            $this->Fruits[] = $Fruit;
        }

        return $this->Fruits;
    }
}
