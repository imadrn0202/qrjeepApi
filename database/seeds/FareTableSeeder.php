<?php

use Illuminate\Database\Seeder;

class FareTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Bucandala Kanto',
            'fare' => 8,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Sarreal / Shell Station',
            'fare' => 10,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Bayan Luma (Eden)',
            'fare' => 13,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Patindig Araw / South Supermarket',
            'fare' => 15,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Robinson Imus',
            'fare' => 18,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Imus BDO',
            'fare' => 20,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Lancaster Square',
            'destination' => 'Imus LTO',
            'fare' => 22,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Bucandala Kanto',
            'destination' => 'Robinsons Imus',
            'fare' => 8,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Bayan Luma',
            'destination' => 'Robinsons Imus',
            'fare' => 10,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Citi Mall',
            'destination' => 'Robinsons Imus',
            'fare' => 10,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Citi Mall',
            'destination' => 'Bucandala',
            'fare' => 10,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Citi Mall',
            'destination' => 'Kanto Alapan - Flag Pole',
            'fare' => 12,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Citi Mall',
            'destination' => 'Bakery - Tonyas Apartment',
            'fare' => 13,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Citi Mall',
            'destination' => 'Lancaster Square',
            'fare' => 15,
        ]);

        DB::table('fare_matrices')->insert([
            'origin' => 'Citi Mall',
            'destination' => 'St Edwards',
            'fare' => 15,
        ]);





        
    }
}
