<?php

namespace Tests\Unit;

use PHPUnit\Runner\Exception;
use Tests\AbstractTestCase;
use Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;

class JsonImportExportTest extends AbstractTestCase
{

    private $outputFilePath = '/tmp/json_import_export_test.json';

    public function testEmptyCollectionToJson() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $this->assertEquals('[]', $collection->toJson());
    }

    public function testToJson() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        file_put_contents($this->outputFilePath, $collection->toJson());
        $this->assertFileExists($this->outputFilePath);
    }


    public function testToJsonStream() {
        $collection = HashCollectionFactory::createFilledHashCollection(100);
        $fp = fopen($this->outputFilePath, 'w+');
        $collection->toJsonStream($fp);
        fclose($fp);
        $this->assertFileExists($this->outputFilePath);
    }

    public function testFromJson() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $collection->fromJson(file_get_contents($this->outputFilePath));
        $this->assertEquals(100, $collection->count());
    }

}
