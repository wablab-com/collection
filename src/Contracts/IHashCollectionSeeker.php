<?php

namespace WabLab\Collection\Contracts;


interface IHashCollectionSeeker
{
    public function current(string $hash):?IHashCollectionSeeker;

    public function next():?IHashCollectionSeeker;

    public function prev():?IHashCollectionSeeker;

    public function first():?IHashCollectionSeeker;

    public function last():?IHashCollectionSeeker;

    public function hash():?string;

    public function value();

}