<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class InsertLastTest extends AbstractTestCase
{

    public function testInsertLast()
    {
        $collection = HashCollectionFactory::createFilledHashCollection(10);
        $this->assertEquals(10, $collection->count());
        $collection->insertLast('new hash', 'new hash data');
        $this->assertEquals(11, $collection->count());
        $counter = 0;
        foreach($collection->yieldAll() as $hash => $value) {
            $counter++;
            if($hash == 'new hash') {
                $this->assertEquals($counter, 11);
            } else {
                $this->assertEquals($counter, $hash);
            }
        }
        $this->assertEquals('new hash', $collection->lastHash());
    }

}
