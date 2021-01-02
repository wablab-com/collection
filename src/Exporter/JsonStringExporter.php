<?php

namespace WabLab\Collection\Exporter;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionExporter;

class JsonStringExporter implements IHashCollectionExporter
{

    private string $string;

    public function __construct(string &$string)
    {
        $this->string = &$string;
    }

    public function export(IHashCollection $hashCollection)
    {
        $listCount = $hashCollection->count();
        $counter = 0;
        $this->string .= '[';
        foreach ($hashCollection->yieldAll() as $hash => $row) {
            $counter++;
            $this->string .= json_encode([$hash => $row]);
            if($counter < $listCount) {
                $this->string .= ',';
            }
        }
        $this->string .= ']';
    }
}