<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BadgeRequest;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $badges = Badge::all();
        return response()->json([
            'success' => true,
            'msg' => 'تم العثور على قائمة المستويات',
            'data' => $badges
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BadgeRequest $request)
    {
        $badge = Badge::create($request->all());
        return response()->json([
            'success' => true,
            'msg' => 'تمّ إضافة الشارة بنجاح',
            'data' => $badge
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $badge = Badge::findOrFail($id);
        return response()->json([
            'success' => true,
            'msg' => 'تمّ العثور على الشارة بنجاح',
            'data' => $badge
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BadgeRequest $request, $id)
    {
        $badge = Badge::findOrFail($id);
        $badge->update($request->all());
        return response()->json([
            'success' => true,
            'msg' => 'تمّ التعديل على الشارة بنجاح',
            'data' => $badge
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $badge = Badge::findOrFail($id);
        if ($badge->delete()) {
            return response()->json([
                'success' => true,
                'msg' => 'تمّ حذف الشارة بنجاح',
            ], 200);
        }
    }
}
