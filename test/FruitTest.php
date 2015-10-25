<?php
/**
* Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

namespace SainsburyTest;

use PHPUnit_Framework_TestCase;
use Sainsbury\Fruit;

/**
 * Fruit Test
 */
class FruitTest extends PHPUnit_Framework_TestCase {

    public function testConstructorSetAllFieldsProperly() {
        $Fruit = new Fruit('title', 'description', 1.23, 123456);

        $this->assertSame('title', $Fruit->title);
        $this->assertSame('description', $Fruit->description);
        $this->assertEquals(1.23, $Fruit->unitPrice, '', 0.00001);
        $this->assertSame(123456, $Fruit->size);
    }

}
