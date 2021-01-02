<?php

namespace WabLab\Collection\Importer;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionImporter;

class JsonFileImporter implements IHashCollectionImporter
{

    private $absoluteFilePath;

    public function __construct($absoluteFilePath)
    {
        $this->absoluteFilePath = $absoluteFilePath;
    }

    public function import(IHashCollection $hashCollection)
    {
        $data = json_decode(file_get_contents($this->absoluteFilePath), true);
        if($data) {
            foreach ($data as $hash => $row) {
                $hashCollection->updateOrInsert($hash, $row);
            }
        }
    }
}