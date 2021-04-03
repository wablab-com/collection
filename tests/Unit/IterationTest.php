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

    public function testYieldAll_InitHash() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $counter = 4;
        foreach($collection->yieldAll(5) as $hash => $item) {
            $counter++;
            $this->assertEquals($counter, $hash);
        }
        $this->assertEquals(10, $counter);
    }

    public function testYieldAllReverse_InitHash() {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $counter = 5;
        foreach($collection->reverseYieldAll(5) as $hash => $item) {
            $this->assertEquals($counter, $hash);
            $counter--;
        }
        $this->assertEquals(0, $counter);
    }

}
