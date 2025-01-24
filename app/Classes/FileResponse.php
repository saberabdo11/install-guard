<?php


namespace App\Classes;

use ZipArchive;

class FileResponse {
    private $filesDirectory = __DIR__ . '/../../files/';
    private $outputZip = __DIR__ . '/../../output/files_packages.zip';


    public function prepareFiles() {
        $zip = new ZipArchive();

        if($zip->open($this->outputZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) ===  TRUE) {
            $files = [
                'routes.zip',
                'RouteServiceProvider.txt',
                'database_backup.sql',
                'migrations.zip'
            ];

            foreach($files as $file) {
                $filePath = $this->filesDirectory . $file;
                if(file_exists($filePath)) {
                    $zip->addFile($filePath, $file);
                }
            }

            $zip->close(); 
            return $this->outputZip;
        }

        return false;
    }

}