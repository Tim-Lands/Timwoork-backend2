<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Exception;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(ContactRequest $request)
    {
        // code
    }
}
