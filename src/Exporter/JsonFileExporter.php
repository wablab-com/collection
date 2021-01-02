<?php

namespace WabLab\Collection\Exporter;

use WabLab\Collection\Contracts\IHashCollection;

class JsonFileExporter extends JsonStreamExporter
{

    public function __construct($absoluteFilePath)
    {
        $streamResource = fopen($absoluteFilePath, 'w+');
        parent::__construct($streamResource);
    }

    public function export(IHashCollection $hashCollection)
    {
        parent::export($hashCollection);
        fclose($this->streamResource);
    }


}