<?php

namespace WabLab\Tests\Unit;

use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;

class SeekerTest extends AbstractTestCase
{

    public function testNext() {
        $collection = HashCollectionFactory::createFilledHashCollection(4);

        // basic
        $this->assertEquals(1, $collection->seeker()->hash());
        $this->assertEquals(2, $collection->seeker()->next()->hash());
        $this->assertEquals(3, $collection->seeker()->next()->hash());
        $this->assertEquals(4, $collection->seeker()->next()->hash());
        $this->assertNull($collection->seeker()->next()->hash());

        // init hash
        $this->assertEquals(3, $collection->seeker()->first()->next()->next()->hash());
        $this->assertEquals(4, $collection->seeker()->first()->next()->next()->next()->hash());
        $this->assertNull($collection->seeker(1)->next()->next()->next()->next()->hash());

        // check value
        $this->assertEquals(4, $collection->seeker(1)->next()->next()->next()->value()['id']);

    }

    public function testPrev() {
        $collection = HashCollectionFactory::createFilledHashCollection(4);

        // basic
        $this->assertEquals(4, $collection->seeker()->last()->hash());
        $this->assertEquals(3, $collection->seeker()->prev()->hash());
        $this->assertEquals(2, $collection->seeker()->prev()->hash());
        $this->assertEquals(1, $collection->seeker()->prev()->hash());
        $this->assertNull($collection->seeker()->prev()->hash());

        // init hash
        $this->assertEquals(2, $collection->seeker()->last()->prev()->prev()->hash());
        $this->assertEquals(1, $collection->seeker()->last()->prev()->prev()->prev()->hash());
        $this->assertNull($collection->seeker(4)->prev()->prev()->prev()->prev()->hash());

        // check value
        $this->assertEquals(1, $collection->seeker(4)->prev()->prev()->prev()->value()['id']);
    }

}
