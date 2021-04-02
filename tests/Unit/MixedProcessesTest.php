<?php

namespace WabLab\Tests\Unit;

use PHPUnit\Runner\Exception;
use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class MixedProcessesTest extends AbstractTestCase
{

    public function testDelete2Insert1InsertAfter3() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);

        // delete
        $collection->delete(1);
        $collection->delete(83);
        $this->assertKeysDoesnotExists($collection, [1, 83]);
        $this->assertEquals(2, $collection->firstHash());


        // move
        //$collection->mo
    }


    private function assertKeysDoesnotExists(IHashCollection $collection, array $keys)
    {
        // assert by isset method
        foreach($keys as $key) {
            $this->assertFalse($collection->isset($key));
        }

        // assert by yieldAll
        foreach($collection->yieldAll() as $key => $value) {
            if(in_array($key, $keys)) {
                $this->assertFalse(true);
            }
        }

        // assert by reverseYieldAll
        foreach($collection->reverseYieldAll() as $key => $value) {
            if(in_array($key, $keys)) {
                $this->assertFalse(true);
            }
        }

    }



}
