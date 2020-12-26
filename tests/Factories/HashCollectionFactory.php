<?php

namespace Tests\Factories;

use Faker\Factory;
use WabLab\Collection\HashCollection;


class HashCollectionFactory
{
    public static function createFilledHashCollection($recordsCount):HashCollection {
        $collection = new HashCollection();
        for($id = 1; $id <= $recordsCount; $id++) {
            $faker = Factory::create();
            $collection->insert($id, [
                'id' => $id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'address' => $faker->address,
                'birth_date' => rand(time() - (60 * 60 * 24 * 365 * 90), time()),
                'weight' => rand(50, 150)
            ]);
        }
        return $collection;
    }


    public static function createEmptyHashCollection():HashCollection {
        return new HashCollection();
    }

}