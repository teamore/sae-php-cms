<?php
namespace App;
class Uploader {
    protected static String $uploadPath = '/var/www/html/public/assets/uploads/';

    public static function show($id, $model = 'posts', $mediaId = 0) {
        $sql = "SELECT `media` FROM `$model` WHERE `id`='$id';";
        $result = Database::connect()->query($sql)->fetchColumn();
        if ($result) {
            $media = json_decode($result, true);
            $file = $media[$mediaId ? $mediaId : 0] ?? null;
        } 
        if ($file ?? null) {
            $filename = self::$uploadPath . $file['path'];
            header("Content-Type: $file[type]");
            header("Content-Length: ".$file['size']);
            echo file_get_contents($filename);   
        } else {
            throw new \Exception("No Media File available for this resource", 404);
        }
        die();

    }
    public function handleFileUploads($path):Array {
        $media = [];
        foreach($_FILES as $file) {
            if ($file['name'] && $file['error']) {
                throw new \Exception("File ".$file['name']." could not be uploaded. (error $file[error])", 422);
            }
            if ($file['tmp_name'] && !$file['error']) {
                $fullPath = self::$uploadPath . $path;

                # Create Directory
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0700, true);
                }

                # Generate unique filename
                $file['target'] = $this->generateUniqueFilename($fullPath);
                
                # Generate and store thumbnail
                $file['thumb'] = 't_' . $file['target'];
                $this->generateImage($file['tmp_name'], $fullPath . $file['thumb'], 100, 100);
                
                # Move temporary file to final destination
                $fileDestination = $fullPath . $file['target'];
                if (!file_exists($fileDestination)) {
                    move_uploaded_file($file['tmp_name'], $fileDestination);
                }

                # Create and append entry to $media array
                $media[] = [
                    'path'=>$path.$file['target'], 
                    'type'=> $file['type'],
                    'thumb'=>$path . $file['thumb'],
                    'size'=>filesize($fileDestination), 
                    'original'=>$file['name']
                ];           
            }
        }
        return $media;        
    }
    public function generateUniqueFilename($path, $prefix = '') {
        $uniqueName = tempnam($path, $prefix);
        unlink($uniqueName);
        return substr($uniqueName, strlen($path));
    }
    public function generateImage(String $source, String $targetFilename, int $width, int $height, int $quality = 75) {
        $exif = exif_imagetype($source);
        if ($exif === false) {
            return null;
        }
        $targetImage = imagecreatetruecolor($width, $height);
        $sourceImage = imagecreatefromjpeg($source);
        list($w, $h) = getimagesize($source);
        $ratio = $w / $h;
        $newRatio = $width / $height;
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $width, $height, $w, $h);
        return imagejpeg($targetImage, $targetFilename, $quality);
    }
}