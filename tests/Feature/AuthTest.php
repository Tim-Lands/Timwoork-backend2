<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void 
     */
    public function setUp():void{
        parent::setUp();
        $user = User::where('email','eldeenshraf2@gmail.com')->first();
        $user->forceDelete();
        $country = new Country([
            'name_ar'=>"مصر",
            "name_en"=>'egypt',
            "name_fr"=>'egyptee',
            'code_phone'=>'+20'
        ]);
        $country->save();
        
    }
    public function test_register()
    {
            $payload = [
                "code_phone" => "+20",
                "email" => "eldeenshraf2@gmail.com",
                "password" => "123456aA",
                "password_confirmation" => "123456aA",
                "phone" => "1061445745",
                "username" => "sharafeldeen1"
            ];
            $response = $this->post('/api/register', $payload);
            $response->assertStatus(200);
        
    }

  /*   public function test_login()
    {
        try {
            $payload=[
                "email"=>"eldeenshraf2@gmail.com",
                "password123456aA"
            ];
            $response = $this->post("/api/login", $payload);
            $response->assertStatus(200);
        } catch (Exception $exc) {
            echo $exc;
        }
    }

 */
    public function tearDown(): void
    {
        
    }
}
