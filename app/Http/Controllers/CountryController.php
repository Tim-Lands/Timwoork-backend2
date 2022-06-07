<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Exception;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Country::with('currency')->get()->all();
        return response()->success('success', $data);
        //
    }

    public function get_phone_codes()
    {
        try {
            $data = Country::all()->groupBy('code_phone')->values()->toArray();
            $temp_arr = array();
            /*  $data->sort(function($a, $b){
                return substr($a->code_phone,1)-substr($b->code_phone,1);
            }); */
            $data = array_merge(...array_values($data));
            usort($data, function ($a, $b) {
                return substr($a['code_phone'], 1) - substr($b['code_phone'], 1);
            });
            $data = array_filter($data, function ($val) use (&$temp_arr) {
                $is_unique = !in_array($val['code_phone'], $temp_arr);
                $temp_arr[] = $val['code_phone'];
                return $is_unique;
            });
            return response()->success('success', $data);
        } catch (Exception $e) {
            echo $e;
            return response()->json("err", 500);
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function show(countries $countries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function edit(countries $countries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, countries $countries)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\countries  $countries
     * @return \Illuminate\Http\Response
     */
    public function destroy(countries $countries)
    {
        //
    }
}
