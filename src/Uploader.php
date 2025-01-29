<?php
namespace App;
class Uploader {
    protected static String $uploadPath = '/var/www/html/public/assets/uploads/';

    public static function show($id, $model = 'posts', $mediaId = 0) {
        $result = Database::connect()->query("SELECT media FROM `posts` WHERE `id`='$id';")->fetchColumn();
        $media = json_decode($result, true);
        $file = $media[$mediaId ? $mediaId : 0];
        $filename = self::$uploadPath . $file['path'];
        header("Content-Type: $file[type]");
        header("Content-Length: ".filesize($filename));
        echo file_get_contents($filename);
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
                $this->generateImage($file['tmp_name'], $fullPath . $file['thumb'], $file['type'], 100, 100);
                
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
    public function generateImage(String $source, String $targetFilename, string $type, int $width, int $height, int $quality = 75) {
        $exif = exif_imagetype($source);
        if ($exif === false) {
            return null;
        }
        $targetImage = imagecreatetruecolor($width, $height);
        $sourceImage = $type === 'image/jpeg' ? imagecreatefromjpeg($source) : imagecreatefrompng($source);
        list($w, $h) = getimagesize($source);
        $ratio = $w / $h;
        $newRatio = $width / $height;
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $width, $height, $w, $h);
        return imagejpeg($targetImage, $targetFilename, $quality);
    }
}