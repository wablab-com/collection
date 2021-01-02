<?php

namespace WabLab\Collection\Exporter;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionExporter;

class ArrayExporter implements IHashCollectionExporter
{

    private array $arrayToFill;

    public function __construct(array &$arrayToFill)
    {
        $this->arrayToFill = &$arrayToFill;
    }

    public function export(IHashCollection $hashCollection)
    {
        foreach ($hashCollection->yieldAll() as $hash => $row) {
            $this->arrayToFill[$hash] = $row;
        }
    }
}