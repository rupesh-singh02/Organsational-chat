<?php

namespace App\CustomClasses;

use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;

class CreateStorage
{
    public function getStoragePath($mainFolder)
    {        
        if (Storage::disk('files_disk')->exists('/' . $mainFolder)) {
            
            $year = Carbon::today()->format('Y'); //2024
            $month = Carbon::today()->format('m'); //04
            $date = Carbon::today()->format('d'); //29

            // Check if the main folder exists, create if not
            $mainFolderPath = '/' . $mainFolder . '/' . $year . '/' . $month . '/' . $date;
            
            if (!Storage::disk('files_disk')->exists($mainFolderPath)) {
                Storage::disk('files_disk')->makeDirectory($mainFolderPath, 0777, true);
            }

            return $mainFolderPath;
        }
    }

}
