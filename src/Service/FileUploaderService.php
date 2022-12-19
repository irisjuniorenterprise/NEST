<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Sponsor;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\FileBag;

class FileUploaderService
{
    public static function uploadImages(mixed $files, $targetDirectory, Product|Sponsor $entity, ProductRepository $repository, ImageRepository $imageRepository) : bool
    {
        $i=0;
        $done=false;
        foreach ($files['name'] as $picture){
            $imageType = pathinfo($picture,PATHINFO_EXTENSION);
            $imageName = str_replace(' ','_','tms').'_'.$i.uniqid('',true).".".$imageType;
            move_uploaded_file($files['tmp_name'][$i],$targetDirectory.$imageName);
            $image=new Image();
            $image->setImageName($imageName);
            if($entity instanceof Product){
                $image->setProduct($entity);
                $done=true;
            }
            $i++;
            $imageRepository->save($image,true);
        }
        return $done;


    } public static function uploaderImages(FileBag $files, $targetDirectory, Product|Sponsor $entity, ProductRepository $repository, ImageRepository $imageRepository) : bool
    {
        $i=0;
        $done=false;
        foreach ($files->all() as $picture){

            $imageType=$picture['imageName']->getClientMimeType();
            $imageName = str_replace(' ','_','tms').'_'.$i.uniqid('',true).".".$imageType;
            move_uploaded_file($picture['imageName']->getClientOriginalName(),$targetDirectory.$imageName);
            $image=new Image();
            $image->setImageName($imageName);
            if($entity instanceof Product){
                $image->setProduct($entity);
                $done=true;
            }
            $i++;
            $imageRepository->save($image);
        }
        return $done;


    }

}