<?php
namespace App;
class Uploader {
    protected static String $uploadPath = '/var/www/html/public/assets/uploads/';

    public static function getMedia($id, $model) {
        $sql = "SELECT `media` FROM `$model` WHERE `id`='$id';";
        $result = Database::connect()->query($sql)->fetchColumn();
        if ($result) {
            return json_decode($result, true);
        }
        return null;
    }
    public static function show($id, $model = 'posts', $mediaId = 0) {
        $media = self::getMedia($id, $model);
        if ($media) {
            $file = $media[$mediaId ? $mediaId : 0];
            $filename = self::$uploadPath . $file['path'];
            header("Content-Type: $file[type]");
            header("Content-Length: ".$file['size']);
            echo file_get_contents($filename);   
        } else {
            throw new \Exception("No Media File available for this resource", 404);
        }
        die();
    }
    public static function delete($id, $model = 'posts', $mediaId = null) {
        $media = self::getMedia($id, $model);
        if ($media) {
            foreach ($media as $i => $file) {
                if ($i === $mediaId || is_null($mediaId)) {
                    $filename = self::$uploadPath . $file['path'];
                    $thumbFilename = self::$uploadPath . $file['thumb'];
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                    if (file_exists($thumbFilename)) {
                        unlink($thumbFilename);
                    }
                }
            }           
        }
    }
    private function rearrangeFileArray(array $files) {
        foreach( $files as $context => $fileList ) {
            foreach( $fileList as $key => $all ){
                if (is_array($all)) {
                    foreach( $all as $i => $val ){
                        $new[$context][$i][$key] = $val;    
                    }
                } else {
                    $new[$context] = [$fileList];
                }
            }     
        } 
        return $new;
    }
    public function handleFileUploads($path, $files = null):Array {
        $files =$files ?? $this->rearrangeFileArray($_FILES);
        $media = [];
        foreach($files as $context => $fileList) {
            foreach($fileList as $file) {
                if ($file['name'] && $file['error']) {
                    throw new \Exception("File ".$file['name']." could not be uploaded. (error $file[error])", 422);
                }
                if ($file['tmp_name'] && !$file['error']) {
                    $fullPath = self::$uploadPath . $path;

                    # Create Directory
                    if (!file_exists($fullPath)) {
                        mkdir($fullPath, 0700, true);
                    }

                    $ext = in_array($file['type'], ['image/jpeg', 'image/jpg']) ? ".jpg" : ".png";
                    # Generate unique filename
                    $file['target'] = $this->generateUniqueFilename($fullPath) . $ext;
                    
                    # Generate and store thumbnail
                    $file['thumb'] = 't_' . $file['target'];
                    $this->generateImage($file['tmp_name'], $fullPath . $file['thumb'], $file['type'], 100, 100);
                    
                    # Move temporary file to final destination
                    $fileDestination = $fullPath . $file['target'];
                    
                    if (!file_exists($fileDestination)) {
                        if (!move_uploaded_file($file['tmp_name'], $fileDestination)) {
                            if (!rename($file['tmp_name'], $fileDestination)) {
                                throw new \Exception('Uploaded file could not be moved to designated destination', 500);
                            } 
                        }
                    }
                                            # Create and append entry to $media array
                    $media[$context][] = [
                        'path'=>$path.$file['target'], 
                        'type'=> $file['type'],
                        'thumb'=>$path . $file['thumb'],
                        'size'=>filesize($fileDestination), 
                        'original'=>$file['name']
                    ];          
                }
            }
        }
        return $media;        
    }
    public static function store($data, $id, $model = 'posts', $mediaId = 0) {
        $data = explode( ',', $data );

        // we could add validation here with ensuring count( $data ) > 1
        $binaryData = base64_decode( $data[ 1 ] );
        $tempFileName = tempnam('/tmp/', 'upload_');

        file_put_contents($tempFileName, $binaryData);

        $files = [
            "media" => [ 0 =>
                [
                    'tmp_name'=>$tempFileName,
                    'name'=>$tempFileName,
                    'error'=>'',
                    'type'=>'image/jpg'
                ]
            ]
        ];  
        self::delete($id, $model, $mediaId);
        $uploader = new self();
        return $uploader->handleFileUploads("$model/$id/", $files);
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
        $sourceImage = (in_array($type, ['image/jpeg', 'image/jpg'])) ? imagecreatefromjpeg($source) : imagecreatefrompng($source);
        list($w, $h) = getimagesize($source);
        $ratio = $w / $h;
        $newRatio = $width / $height;
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $width, $height, $w, $h);
        return imagejpeg($targetImage, $targetFilename, $quality);
    }
}