<?php

namespace App\Services;

class ServicesCSV{
    public function getAllHouses() : array{
        $filePath = __DIR__ . '/houses.csv';

        $file = fopen($filePath, 'r');;
        if (!$file) {
            throw new \RuntimeException("Can not open file $filePath for reading.");
    
        }

        $header = fgetcsv($file);

        $data = [];

        while (($row = fgetcsv($file)) !==  false){
            $row = array_map(fn($cell) => mb_convert_encoding($cell, 'UTF-8', 'CP1251'), $row);
            $data[] = array_combine($header, $row);
        }
        
        fclose($file);

        return $data;
    }

    public function addBooking($houseId, $phone, $comment) : int{
        $filePath = __DIR__ . '/booking.csv';

        if (!file_exists($filePath)) {
            file_put_contents($filePath, "id,houseId,phone,comment\n");
        }

        $currentId = 0;

        if (($file = fopen($filePath, 'r')) !== false) {
            fgetcsv($file);
            while (($row = fgetcsv($file)) !== false) {
                $id = (int)$row[0];
                if ($id > $currentId) {
                    $currentId = $id;
                }
            }
            fclose($file);
        }

        $nextId = $currentId + 1;

        $file = fopen($filePath, 'a');
        $houseId  = mb_convert_encoding($houseId, 'UTF-8');
        $phone    = mb_convert_encoding($phone, 'UTF-8');
        $comment  = mb_convert_encoding($comment, 'UTF-8');

        fputcsv($file, [$nextId, $houseId, $phone, $comment]);
        fclose($file);

        return $nextId;
    }

    public function editBookingCommentById($targetId, $newComment) : void{
        $filePath = __DIR__ . '/booking.csv';

        $file = fopen($filePath, 'r+');
        if (!$file) {
            throw new \RuntimeException("Can not open file $filePath for editing.");
        }

        $header = fgetcsv($file);
        $rows = [];
        $found = false;


        while (($row = fgetcsv($file)) !== false) {
                $id = (int)$row[0];
                if ($id == $targetId) {
                    $row[3] = $newComment;
                    $found = true;
                }
                $rows[] = $row;
            }

        

        if (!$found){
            throw new \RuntimeException("Booking Id $targetId not found");
        }

        rewind($file);
        ftruncate($file, 0);

        fputcsv($file, $header);

        foreach ($rows as $row){
            fputcsv($file, $row);
        }


        fclose($file);
    }

}