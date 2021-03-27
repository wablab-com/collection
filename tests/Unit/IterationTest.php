<?php

namespace WabLab\Tests\Unit;

use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;

class IterationTest extends AbstractTestCase
{

    public function testYieldAll() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $item) {
            $counter++;
            $this->assertEquals($counter, $hash);
        }
        $this->assertEquals(10, $counter);
    }

    public function testYieldAllReverse() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $counter = 10;
        foreach($collection->reverseYieldAll() as $hash => $item) {
            $this->assertEquals($counter, $hash);
            $counter--;
        }
        $this->assertEquals(0, $counter);
    }

}
