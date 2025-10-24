<?php

namespace App\Services;

class ServicesCSV{
    public function getAllHouses() : array{
        $file = fopen(__DIR__ .'/houses.csv', 'r');

        $header = fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file)) !==  false){
            $data[] = array_combine($header, $row);
        }
        
        fclose($file);

        return $data;
    }

    public function addBooking($houseId, $phone, $comment) : void{
        $file = fopen(__DIR__.'booking.csv', 'a');
        fputcsv($file, [$houseId, $phone, $comment]);
        fclose($file);
    }
}