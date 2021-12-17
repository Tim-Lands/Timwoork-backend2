<?php

use App\Models\Product;

// حساب السعر الكامل للسلة
if (!function_exists('calculate_price_with_tax')) {
    /**
     * calculate_price_with_tax => دالة تقوم بحساب السعر الاجمالي للسلة مع الرسوم
     *
     * @param  mixed $price
     * @return array
     */
    function calculate_price_with_tax($price)
    {
        $calculate = [];
        switch ($price) {
            case $price > 20 && $price <= 200:
                $calculate['price_with_tax'] = $price + ($price * 0.05);
                $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;

            case $price > 200 && $price <= 1000:
                $calculate['price_with_tax'] = $price + ($price * 0.07);
                $calculate['tax'] = $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;

            case $price > 1000:
                $calculate['price_with_tax'] = $price + ($price * 0.1);
                $calculate['tax'] = $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;

            default:
                if ($price == 0) {
                    $calculate['price_with_tax'] =  0;
                    $calculate['tax'] = 0;
                    break;
                }
                $calculate['price_with_tax'] =  $price + 1;
                $calculate['tax'] = $calculate['price_with_tax'] - $price;
                break;
        }
        return $calculate;
    }
}
// فحص التطويرات المدخلة من قبل المستخدم
if (!function_exists('check_found_developments')) {
}
