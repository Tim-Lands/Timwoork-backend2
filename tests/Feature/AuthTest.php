<?php

namespace Tests\Feature;

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
    public function test_register()
    {
        try{
        $payload=[
        "code_phone"=>"+20",
        "email"=>"eldeenshraf2@gmail.com",
        "password"=>"123456aA",
        "password_confirmation"=>"123456aA",
        "phone"=>"1061445745",
        "username"=>"sharafeldeen1"];
        $response = $this->post('/api/register', $payload);
        echo(json_encode($response->json(), JSON_PRETTY_PRINT));
        $response->assertStatus(200);
    }
    catch(Exception $exc){
        echo $exc;
    }
}

    public function tearDown(): void
    {
        parent::tearDown();
        echo "sharafl eldeen end";
    }
}
