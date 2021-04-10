<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    // public function upload(UploadedFile $file)
    // {
    //     $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //     $safeFilename = $this->slugger->slug($originalFilename);
    //     $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

    //     try {
    //         $file->move($this->getTargetDirectory(), $fileName);
    //     } catch (FileException $e) {
    //         // ... handle exception if something happens during file upload
    //     }

    //     return $fileName;
    // }

    public function uploadImage($filename, $base64Data, $folderPath)
    {
        ob_start();
        // $newFileName = $filename.'.jpg'; //need to change?
        //check and get extension
        $splitted = explode(';', $base64Data);
        $dataType = $splitted[0];
        $pos = strpos($dataType, "/");
        $getExtension = substr($dataType, ($pos+1));

        $fullFolderPath = $this->getTargetDirectory() . "\\".$folderPath;
        if (!is_dir($fullFolderPath)) {
            mkdir($fullFolderPath,0755,true);
        }

        try {
            $fullPath = $fullFolderPath."\\".$filename.".".$getExtension;
            file_put_contents( $fullPath, file_get_contents($base64Data) );
        } catch (FileException $e) {
            // do something
        }

        return $filename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}