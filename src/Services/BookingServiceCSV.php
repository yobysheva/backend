<?php

namespace App\Services;

class BookingServiceCSV{
    private string $bookingFilePath;

    public function __construct(string $bookingFilePath)
    {
        $this->bookingFilePath = $bookingFilePath;
    }

    public function addBooking($houseId, $phone, $comment) : int{

        if (!file_exists($this->bookingFilePath)) {
            file_put_contents($this->bookingFilePath, "id,houseId,phone,comment\n");
        }

        $currentId = 0;

        if (($file = fopen($this->bookingFilePath, 'r')) !== false) {
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

        $file = fopen($$this->bookingFilePath, 'a');
        $houseId  = mb_convert_encoding($houseId, 'UTF-8');
        $phone    = mb_convert_encoding($phone, 'UTF-8');
        $comment  = mb_convert_encoding($comment, 'UTF-8');

        fputcsv($file, [$nextId, $houseId, $phone, $comment]);
        fclose($file);

        return $nextId;
    }

    public function editBookingCommentById($targetId, $newComment) : void{
        $file = fopen($this->bookingFilePath, 'r+');
        if (!$file) {
            throw new \RuntimeException("Can not open file $this->bookingFilePath for editing.");
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