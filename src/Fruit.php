<?php
/**
 * Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

namespace Sainsbury;

/**
 * Fruit
 */
class Fruit {
    /** @var  string fruit title */
    public $title;

    /** @var  string fruit description */
    public $description;

    /** @var float unit price */
    public $unitPrice;

    /** @var int page size, in bytes */
    public $size;

    /**
     * Fruit constructor
     *
     * @param string $title fruit title
     * @param string $description fruit description
     * @param float $unitPrice unit price
     * @param int $size page size, in bytes
     */
    public function __construct($title, $description, $unitPrice, $size) {
        $this->title = $title;
        $this->description = $description;
        $this->unitPrice = (float) $unitPrice;
        $this->size = (int) $size;
    }
}
