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
