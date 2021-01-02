<?php

namespace WabLab\Collection\Exporter;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionExporter;

class JsonStreamExporter implements IHashCollectionExporter
{

    /**
     * @var resource
     */
    protected $streamResource;

    public function __construct($streamResource)
    {
        $this->streamResource = $streamResource;
    }

    public function export(IHashCollection $hashCollection)
    {
        $listCount = $hashCollection->count();
        $counter = 0;
        fwrite($this->streamResource, '[');
        foreach ($hashCollection->yieldAll() as $hash => $row) {
            $counter++;
            fwrite($this->streamResource, json_encode([$hash => $row]) );
            if($counter < $listCount) {
                fwrite($this->streamResource, ',' );
            }
        }
        fwrite($this->streamResource, ']');
    }
}