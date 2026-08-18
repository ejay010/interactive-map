<?php

namespace Ekelly\InteractiveMap\Import;

class CsvReader 
{
    public function read(string $file): array
    {
        if (!file_exists($file)){
            throw new \Exception('CSV file not found.');
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            throw new \Exception('Unable to open CSV.');
        }

        $headers = fgetcsv($handle);
        $headers = array_map(function ($header) {
            return trim(preg_replace('/^\xEF\xBB\xBF/', '', $header));
        }, $headers);

        if (!$headers) {
            fclose($handle);
            throw new \Exception('CSV contains no headers');
        }


        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            
            if (count($row) !== count($headers)){
                continue;
            }
            
            $rows[] = array_combine($headers, $row);

            }

            fclose($handle);
        

        return $rows;
    } 

    public function validateHeaders() {
        
    }
}