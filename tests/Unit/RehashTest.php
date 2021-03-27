<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class RehashTest extends AbstractTestCase
{

    public function testRehash() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertTrue($collection->reHash(5, 500));
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $item) {
            $counter++;
            if($counter == 5) {
                $this->assertEquals(500, $hash);
            } else {
                $this->assertEquals($counter, $hash);
            }
        }
        $this->assertEquals(10, $counter);

        $this->assertFalse($collection->reHash('invalid hash', 'anything'));
    }

}
