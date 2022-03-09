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
if (!function_exists('quantity_cheked')) {

    /**
     * quantity_cheked
     *
     * @param  mixed $quantity
     * @param  mixed $price
     * @return void
     */
    function quantity_cheked($quantity, $price)
    {
        switch ($price) {
            case $price >= 5 && $price <= 100:
                return $quantity <= 10;
                break;

            case $price >= 101 && $price <= 500:
                return $quantity <= 2;
                break;
            default:
                return $quantity == 1;
                break;
        }
    }
}
// فحص التطويرات المدخلة من قبل المستخدم
if (!function_exists('check_found_developments')) {
}

// slug arabic
if (!function_exists('slug_with_arabic')) {
    function slug_with_arabic($string, $separator = '-')
    {
        if (is_null($string)) {
            return "";
        }

        $string = trim($string);

        $string = mb_strtolower($string, "UTF-8");

        $string = preg_replace("/[^a-z0-9_\sءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "", $string);

        $string = preg_replace("/[\s-]+/", " ", $string);

        $string = preg_replace("/[\s_]/", $separator, $string);

        return $string;
    }
}
