<?php


namespace App\Http\Controllers\gestion_programas;

use App\Http\Controllers\Controller;
use App\Models\Competencias;
use Illuminate\Http\Request;

class CompetenciasController extends Controller
{
    public function index(Request $request)
    {

        $programa = $request->input('programas');
        $competencias = Competencias::with('programas');


        if($programa){
            $competencias->whereHas('programas',function($q) use ($programa){
                return $q->select('id')->where('id',$programa)->orWhere('nombrePrograma',$programa);
            });
        };

        return response()->json($competencias->get());
    }

    
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

    public function showByIdActividadP(int $id){
        $competencias = Competencias::with('actividadProyecto')
        ->where('idActividadProyecto',$id)->get();

        return response() -> json($competencias);
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
