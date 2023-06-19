<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class QueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $table, string $query)
    {
        if($table){
            if($table == 'area'){
                $result = Area::Where('nombreArea',$query)
                ->orWhere('codigo',$query)
                ->get();
                $search_result = $result!=[];
                var_dump($search_result);
                if(!$search_result){
                  $result = Area::all();
                }

                return response() -> json($result);
            }
        }else{

        }
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
