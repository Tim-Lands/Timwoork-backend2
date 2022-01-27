<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageAttachmentRequest;
use App\Http\Requests\UpdateMessageAttachmentRequest;
use App\Models\MessageAttachment;

class MessageAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMessageAttachmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessageAttachmentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MessageAttachment  $messageAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(MessageAttachment $messageAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MessageAttachment  $messageAttachment
     * @return \Illuminate\Http\Response
     */
    public function edit(MessageAttachment $messageAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMessageAttachmentRequest  $request
     * @param  \App\Models\MessageAttachment  $messageAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMessageAttachmentRequest $request, MessageAttachment $messageAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MessageAttachment  $messageAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(MessageAttachment $messageAttachment)
    {
        //
    }
}
