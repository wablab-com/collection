<?php

namespace WabLab\Tests\Unit;

use WabLab\Tests\AbstractTestCase;
use WabLab\Tests\Factories\HashCollectionFactory;
use WabLab\Collection\Exporter\ArrayExporter;
use WabLab\Collection\Exporter\JsonFileExporter;
use WabLab\Collection\Exporter\JsonStringExporter;
use WabLab\Collection\Importer\ArrayImporter;
use WabLab\Collection\Importer\JsonFileImporter;
use WabLab\Collection\Importer\JsonStringImporter;

class JsonImportExportTest extends AbstractTestCase
{

    private $outputFilePath = '/tmp/json_import_export_test.json';

    public function testEmptyCollectionToJson() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $array = [];
        $arrayExporter = new ArrayExporter($array);
        $arrayExporter->export($collection);
        $this->assertEquals(0, count($array));

        $jsonFileExporter = new JsonFileExporter($this->outputFilePath);
        $jsonFileExporter->export($collection);
        $this->assertEquals('[]', file_get_contents($this->outputFilePath));

        $string = '';
        $jsonStringExporter = new JsonStringExporter($string);
        $jsonStringExporter->export($collection);
        $this->assertEquals('[]', $string);
    }

    public function testEmptyCollectionFromEmptyJson() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $arrayImporter = new ArrayImporter([]);
        $arrayImporter->import($collection);
        $this->assertEquals(0, $collection->count());

        $jsonFileImporter = new JsonFileImporter($this->outputFilePath);
        $jsonFileImporter->import($collection);
        $this->assertEquals(0, $collection->count());

        $jsonStringImporter = new JsonStringImporter('');
        $jsonStringImporter->import($collection);
        $this->assertEquals(0, $collection->count());
    }

    public function testFilledCollectionToJson() {
        $fillCount = 100;
        $collection = HashCollectionFactory::createFilledHashCollection($fillCount);

        $array = [];
        $arrayExporter = new ArrayExporter($array);
        $arrayExporter->export($collection);
        $this->assertEquals($fillCount, count($array));

        $jsonFileExporter = new JsonFileExporter($this->outputFilePath);
        $jsonFileExporter->export($collection);
        $this->assertEquals($fillCount, count(json_decode(file_get_contents($this->outputFilePath))));

        $string = '';
        $jsonStringExporter = new JsonStringExporter($string);
        $jsonStringExporter->export($collection);
        $this->assertEquals($fillCount, count(json_decode($string)));
    }


    /**
     * @depends testFilledCollectionToJson
     */
    public function testEmptyCollectionFromFilledJson() {
        $collection = HashCollectionFactory::createEmptyHashCollection();
        $testArray = [1000 => 1, 2000 => 2, 3000 => 3];
        $arrayImporter = new ArrayImporter($testArray);
        $arrayImporter->import($collection);
        $this->assertEquals(3, $collection->count());

        $jsonFileImporter = new JsonFileImporter($this->outputFilePath);
        $jsonFileImporter->import($collection);
        $this->assertEquals(103, $collection->count());

        $jsonStringImporter = new JsonStringImporter(json_encode($testArray));
        $jsonStringImporter->import($collection);
        $this->assertEquals(103, $collection->count());
    }




}
