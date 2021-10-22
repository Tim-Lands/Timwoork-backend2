<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LevelRequest;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->query('type')) {
            $type = $request->query('type');
            $levels = Level::where('type', $type)->get();
        } else {
            $levels = Level::all();
        }
        return response()->json($levels, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LevelRequest $request)
    {
        $level = Level::create($request->all());
        return response()->json([
            'success' => true,
            'msg' => 'تمّ إضافة المستوى بنجاح'
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
        $level = Level::findOrFail($id);
        return response()->json([
            'success' => true,
            'msg' => 'تمّ العثور على المستوى بنجاح',
            'data' => $level
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $level = Level::findOrFail($id);
        $level->update($request->all());
        return response()->json([
            'success' => true,
            'msg' => 'تمّ التعديل على المستوى بنجاح',
            'data' => $level
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
        $level = Level::findOrFail($id);
        if ($level->delete()) {
            return response()->json([
                'success' => true,
                'msg' => 'تمّ حذف المستوى بنجاح',
                'data' => $level
            ], 200);
        }
    }
}
