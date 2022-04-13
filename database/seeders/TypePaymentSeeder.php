<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'name_ar' => 'بايبال',
                'name_en' => 'Paypal',
                'precent_of_payment' => 8,
                'value_of_cent' => 0,
                'status' => 1,
            ],
            [
                'name_ar' => 'سترايب',
                'name_en' => 'Stripe',
                'precent_of_payment' => 5,
                'value_of_cent' => 0.5,
                'status' => 1,
            ]
        ];
        if (DB::table('type_payments')->count() == 0) {
            DB::table('type_payments')->insert($types);
        }
    }
}
