<?php


namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Models\ActividadProyecto;
use App\Models\Competencias;
use Illuminate\Http\Request;

class CompetenciasController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $actividadProyecto= $request->input('actividadProyecto');
        $AP = Competencias::with('actividadProyecto');


        if($actividadProyecto){
            $AP->whereHas('actividadProyecto',function($q) use ($actividadProyecto){
                return $q->select('id')->where('id',$actividadProyecto)->orWhere('nombreActividadProyecto',$actividadProyecto);
            });
        };

        return response()->json($AP->get());
    }

    // public function index()
    // {
    //     $date = Competencias::with('competenciaRap')->get();
    //     return response()->json($date);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      $data =$request->all();
      $competencia = new Competencias($data);
      $competencia->save();

      return response()->json($competencia);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Competencias  $competencias
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $competencias = Competencias::find($id);

        return response()->json($competencias);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Competencias  $competencias
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Competencias  $competencias
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $competencia = Competencias::findOrFail($id);
        $competencia->delete();
        return response()->json('se elimino');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $competencias = Competencias::findOrFail($id);
        $competencias->fill($data);
        $competencias->save();

        return response()->json($competencias);
    }

}
