<?php

namespace WabLab\Collection\Importer;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionImporter;

class JsonStringImporter implements IHashCollectionImporter
{

    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function import(IHashCollection $hashCollection)
    {
        $data = json_decode($this->string, true);
        if($data) {
            foreach ($data as $hash => $row) {
                $hashCollection->updateOrInsert($hash, $row);
            }
        }
    }
}