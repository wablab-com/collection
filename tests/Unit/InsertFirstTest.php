<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class InsertFirstTest extends AbstractTestCase
{

    public function testInsertFirst()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10, $collection->count());
        $collection->insertFirst('new hash', 'new hash data');
        $this->assertEquals(11, $collection->count());
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $value) {
            $counter++;
            if($hash == 'new hash') {
                $this->assertEquals($counter, 1);
            } else {
                $this->assertEquals($counter -1 , $hash);
            }
        }
        $this->assertEquals('new hash', $collection->firstHash());
    }

    public function testInsertFirst_EmptyCollection()
    {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertEquals(0, $collection->count());
        $collection->insertFirst('new hash', 'new hash data');
        $this->assertEquals(1, $collection->count());
        $this->assertEquals('new hash', $collection->firstHash());
        $this->assertEquals('new hash', $collection->lastHash());
    }

}
