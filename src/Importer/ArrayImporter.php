<?php

namespace WabLab\Collection\Importer;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionImporter;

class ArrayImporter implements IHashCollectionImporter
{

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function import(IHashCollection $hashCollection)
    {
        foreach ($this->data as $hash => $row) {
            $hashCollection->updateOrInsert($hash, $row);
        }
    }
}