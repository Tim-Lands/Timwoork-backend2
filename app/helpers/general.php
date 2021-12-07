<?php

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

// دالة رفع الملفات
if (!function_exists('UploadImage')) {
    function UploadImage($file, $image, $folder, $size, $defaut = '')
    {
        if ($image != $defaut && $image != null) {
            Storage::disk('public_images')->delete('/' . $folder . '/' . $image);
        }

        Image::make($file)->resize($size, null, function ($constrait) {
            $constrait->aspectRatio();
        })->save(public_path('images/' . $folder . '/' . $file->hashName()));
    }
}
// دالة حذف الملفات
if (!function_exists('DeleteImage')) {
    function DeleteImage($image, $folder, $defaut = "")
    {
        if ($image != $defaut) {
            Storage::disk('public_images')->delete('/' . $folder . '/' . $image);
        }
    }
}

// حساب السعر الكامل للسلة
if (!function_exists('calculate_price_with_tax')) {
    function calculate_price_with_tax($price)
    {
        $calculate = [];
        switch ($price) {
            case $price > 20 || $price <= 200:
                $calculate['price_with_tax'] = $price + ($price * 0.05);
                $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;

            case $price > 200 || $price <= 1000:
                $calculate['price_with_tax'] = $price + ($price * 0.07);
                $calculate['tax'] = $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;

            case $price > 200 || $price <= 1000:
                $calculate['price_with_tax'] = $price + ($price * 0.1);
                $calculate['tax'] = $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;

            default:
                $price =
                    $calculate['price_with_tax'] =  $price + 1;
                $calculate['tax'] = $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;
        }
        return $calculate;
    }
}
