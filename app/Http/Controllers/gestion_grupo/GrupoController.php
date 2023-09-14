<?php

namespace App\Http\Controllers\gestion_grupo;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gestion_configuracion_rap\ConfiguracionRapController;
use App\Models\AsignacionJornadaGrupo;
use App\Models\ConfiguracionRap;
use App\Models\Grupo;
use App\Models\HorarioInfraestructuraGrupo;
use App\Models\Programa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class GrupoController extends Controller
{
  private $relations;

  public function __construct()
  {
    $this->relations = [
      'tipoGrupo',
      'proyectoFormativo.programas.tipoPrograma',
      'tipoFormacion',
      'estadoGrupo',
      'tipoOferta',
      'jornadas.diaJornada',
      'participantes',
      'infraestructuras',
      'infraestructuras.sede'
    ];
  }

  /**
   * Get all data of table grupo
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

    $grupos = Grupo::with($this->relations)->get();

    //quitar pivots
    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      $grupo['participantes'] = $grupo['participantes']->map(function ($participante) {
        $pivot = $participante['pivot'];
        unset($participante['pivot']);
        $participante['participantes_asignados'] = $pivot;
        return $participante;
      });

      $grupo['jornadas'] = $grupo['jornadas']->map(function ($jornada) {
        $pivot = $jornada['pivot'];
        unset($jornada['pivot']);
        $jornada['jornada_grupo'] = $pivot;
        return $jornada;
      });
      return $grupo;
    });
    return response()->json($newGrupos);
  }

  /**
   * Get grupos by idTipoGrupo equal especial
   */
  public function getGruposByEspecial()
  {
    $grupos = Grupo::with($this->relations)->where('idTipoGrupo', '2')->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      $grupo['participantes'] = $grupo['participantes']->map(function ($participante) {
        $pivot = $participante['pivot'];
        unset($participante['pivot']);
        $participante['participantes_asignados'] = $pivot;
        return $participante;
      });

      $grupo['jornadas'] = $grupo['jornadas']->map(function ($jornada) {
        $pivot = $jornada['pivot'];
        unset($jornada['pivot']);
        $jornada['jornada_grupo'] = $pivot;
        return $jornada;
      });
      return $grupo;
    });

    return response()->json($newGrupos);
  }

  /**
   * Get grupos by idTipoGrupo equal ficha
   */
  public function getGruposByFicha()
  {
    $grupos = Grupo::with($this->relations)->where('idTipoGrupo', '1')->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      $grupo['participantes'] = $grupo['participantes']->map(function ($participante) {
        $pivot = $participante['pivot'];
        unset($participante['pivot']);
        $participante['participantes_asignados'] = $pivot;
        return $participante;
      });

      $grupo['jornadas'] = $grupo['jornadas']->map(function ($jornada) {
        $pivot = $jornada['pivot'];
        unset($jornada['pivot']);
        $jornada['jornada_grupo'] = $pivot;
        return $jornada;
      });
      return $grupo;
    });

    return response()->json($newGrupos);
  }

  /**
   * Crear Ficha con sus relaciones
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $data = $request->all();

    $existingGrupo = Grupo::where('nombre', $data['nombre'])->first();
    if ($existingGrupo) {
      return response()->json(['error' => 'Número de grupo existente!!!.'], 422);
    }

    $grupo = new Grupo([
      'nombre'              => $data['nombre'],
      'fechaInicialGrupo'   => $data['fechaInicialGrupo'],
      'fechaFinalGrupo'     => $data['fechaFinalGrupo'],
      'observacion'         => $data['observacion'],
      'idTipoGrupo'         => $data['idTipoGrupo'],
      'idProyectoFormativo' => $data['idProyectoFormativo'],
      'idTipoFormacion'     => $data['idTipoFormacion'],
      'idEstado'            => $data['idEstado'],
      'idTipoOferta'        => $data['idTipoOferta'],
    ]);

    $grupo->save();

    $infraestructuras = $data['infraestructuras'];

    foreach ($infraestructuras as $infraItem) {

      $existeAsignacion = $this->verificarAsignacionInfraestructura($infraestructuras, $data['jornadas']);

      if ($existeAsignacion) {
        return response()->json(['error' => 'Infraestructura ocupada en la misma jornada.'], 422);
      } else {
        $this->guardarHorarioInfra($infraItem, $grupo->id);
      }
    }


    foreach ($request->jornadas as $jornadaItem) {
      foreach ($jornadaItem as $jItem) {
        $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
        $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
        $asignacionJornadaGrupo->save();
      }
    }

    // Validation of ficha for create configuraciones raps
    $nombreTipoGrupo = DB::table('tipoGrupo')
      ->where('id', $grupo->idTipoGrupo)
      ->value('nombreTipoGrupo');

    if ($nombreTipoGrupo === "FICHA") {
      $this->createConfiguracionRapByGrupo($grupo->id);
    }

    $grupo = Grupo::with($this->relations)->findOrFail($grupo->id);


    return response()->json($grupo, 201);
  }

  /**
   * Crear Especial con sus relaciones
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storeEspecial(Request $request)
  {

    $data = $request->all();

    $grupo = new Grupo([
      'nombre'              => $data['nombre'],
      'fechaInicialGrupo'   => $data['fechaInicialGrupo'],
      'fechaFinalGrupo'     => $data['fechaFinalGrupo'],
      'observacion'         => $data['observacion'],
      'idTipoGrupo'         => $data['idTipoGrupo'],
      'idProyectoFormativo' => $data['idProyectoFormativo'],
      'idTipoFormacion'     => $data['idTipoFormacion'],
      'idEstado'            => $data['idEstado'],
      'idTipoOferta'        => $data['idTipoOferta'],
    ]);

    if ($request->hasFile('imagenIcon')) {

      $imagen = $request->file('imagenIcon');
      $nombreArchivo = uniqid() . '_' . $imagen->getClientOriginalName();
      $rutaAlmacenamiento = 'public/imagenes/especial/';
      $imagen->storeAs($rutaAlmacenamiento, $nombreArchivo);
      $rutaImagen = storage_path('app/' . $rutaAlmacenamiento . '/' . $nombreArchivo);

      Image::make($rutaImagen)
        ->resize(300, 200) // Cambia las dimensiones según tus necesidades
        ->save(storage_path('app/' . $rutaAlmacenamiento . $nombreArchivo)); // Guardar la imagen redimensionada

      $rutaImagenGuardada = 'storage/imagenes/especial/' . $nombreArchivo;

      $grupo->imagenIcon = $rutaImagenGuardada; // Asignar a el campo imagenIcon
    }

    $grupo->save();

    // Verifica y asegura que la propiedad 'infraestructuras' sea un arreglo o un arreglo vacío
    if (!isset($data['infraestructuras']) || !is_array($data['infraestructuras'])) {
      $data['infraestructuras'] = [];
    }

    // Verifica y asegura que la propiedad 'jornadas' sea un arreglo o un arreglo vacío
    if (!isset($data['jornadas']) || !is_array($data['jornadas'])) {
      $data['jornadas'] = [];
    }

    foreach ($data['infraestructuras'] as $infraItem) {

      $existeAsignacion = $this->verificarAsignacionInfraestructura($data['infraestructuras'], $data['jornadas']);

      if ($existeAsignacion) {
        return response()->json(['error' => 'Infraestructura ocupada en la misma jornada.'], 422);
      } else {
        $this->guardarHorarioInfra($infraItem, $grupo->id);
      }
    }

    foreach ($data['jornadas'] as $jornadaItem) {
      foreach ($jornadaItem as $jItem) {
        $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
        $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
        $asignacionJornadaGrupo->save();
      }
    }

    $especial = Grupo::with($this->relations)->findOrFail($grupo->id);

    return response()->json($especial, 201);
  }


  /**
   * Mostrar un grupo por su Id
   *
   * @param  \App\Models$grupo  $grupo
   * @return \Illuminate\Http\Response
   */
  public function show(int $id)
  {
    $dato = Grupo::with($this->relations)->find($id);

    if (!$dato) {
      return response()->json(['error' => 'El dato no fue encontrado'], 404);
    }

    $dato['infraestructuras'] = $dato['infraestructuras']->map(function ($infr) {
      $pivot = $infr['pivot'];
      unset($infr['pivot']);
      $infr['horario_infraestructura'] = $pivot;
      return $infr;
    });

    $dato['jornadas'] = $dato['jornadas']->map(function ($jornada) {
      $pivot = $jornada['pivot'];
      unset($jornada['pivot']);
      $jornada['jornada_grupo'] = $pivot;
      return $jornada;
    });

    return response()->json($dato);
  }

  /**
   * Show infraestructura by id
   * @param int $id
   */
  public function showByIdInfra(int $id)
  {

    $grupos = Grupo::whereHas('infraestructuras', function ($query) use ($id) {
      $query->where('idInfraestructura', $id);
    })->with($this->relations)->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      return $grupo;
    });

    return response()->json($newGrupos);
  }

  /**
   * Show sede by id
   * @param int $id
   */
  public function showByIdSede(int $id)
  {

    $grupos = Grupo::whereHas('infraestructuras', function ($query) use ($id) {
      $query->where('idSede', $id);
    })->with($this->relations)->get();

    $newGrupos = $grupos->map(function ($grupo) {
      $grupo['infraestructuras'] = $grupo['infraestructuras']->map(function ($infr) {
        $pivot = $infr['pivot'];
        unset($infr['pivot']);
        $infr['horario_infraestructura'] = $pivot;
        return $infr;
      });

      return $grupo;
    });

    return response()->json($newGrupos);
  }


  /**
   * Actualizar estado de las infraestructuras y crear una nueva infraestructura con su grupo cuando se afecte esta
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models$grupo  $grupo
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request, $id)
  {
    $data = $request->all();
    $grupo = Grupo::with($this->relations)->findOrFail($id);

    // Validar infraestructuras y jornadas
    $existeAsignacion = $this->verificarAsignacionInfraestructuraUpdate($data['infraestructuras'], $request->jornadas, $grupo->id);

    if ($existeAsignacion) {
      return response()->json(['error' => 'Infraestructura ocupada en la misma jornada.'], 400);
    }

    $grupo->update([
      'nombre' => $data['nombre'],
      'fechaInicialGrupo' => $data['fechaInicialGrupo'],
      'fechaFinalGrupo' => $data['fechaFinalGrupo'],
      'observacion' => $data['observacion'],
      'idTipoGrupo' => $data['idTipoGrupo'],
      'idProyectoFormativo' => $data['idProyectoFormativo'],
      'idTipoFormacion' => $data['idTipoFormacion'],
      'idEstado' => $data['idEstado'],
      'idTipoOferta' => $data['idTipoOferta'],
    ]);

    $currentInfraestructuras = $grupo->infraestructuras()->whereDate('fechaFinal', '>=', now())->pluck('idInfraestructura');
    $grupo->infraestructuras()->detach($currentInfraestructuras); // No poder eliminar grupos que ya pasaron su fecha final

    $infraestructura = $data['infraestructuras'];

    foreach ($infraestructura as $infraItem) { // Guardar infraestructura actualizada
      $this->guardarHorarioInfra($infraItem, $grupo->id);
    }

    AsignacionJornadaGrupo::where('idGrupo', $grupo->id)->delete();

    foreach ($request->jornadas as $jornaItem) {
      foreach ($jornaItem as $jItem) {
        $info = ['idGrupo' => $grupo->id, 'idJornada' => $jItem];
        $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
        $asignacionJornadaGrupo->save();
      }
    }

    $grupo->save(); // Guardar el grupo actualizado

    return response()->json($grupo, 200);
  }

  /**
   * Update register of especial
   */
  public function updateEspecial(Request $request, $idEspecial)
  {
    $data = $request->all();
    $especial = Grupo::with($this->relations)->findOrFail($idEspecial);

    $especial->update([
      'nombre' => $data['nombre'],
      'fechaInicialGrupo' => $data['fechaInicialGrupo'],
      'fechaFinalGrupo' => $data['fechaFinalGrupo'],
      'observacion' => $data['observacion'],
      'idTipoGrupo' => $data['idTipoGrupo'],
      'idProyectoFormativo' => $data['idProyectoFormativo'],
      'idTipoFormacion' => $data['idTipoFormacion'],
      'idEstado' => $data['idEstado'],
      'idTipoOferta' => $data['idTipoOferta'],
    ]);

    if ($request->hasFile('imagenIcon')) {

      $imagen = $request->file('imagenIcon');
      $nombreArchivo = uniqid() . '_' . $imagen->getClientOriginalName();
      $rutaAlmacenamiento = 'public/imagenes/especial/'; // Ajusta la ruta según tu configuración
      $imagen->storeAs($rutaAlmacenamiento, $nombreArchivo);
      $rutaImagen = storage_path('app/' . $rutaAlmacenamiento . '/' . $nombreArchivo);

      Image::make($rutaImagen)
        ->resize(300, 200) // Cambia las dimensiones según tus necesidades
        ->save(storage_path('app/' . $rutaAlmacenamiento . $nombreArchivo)); // Guardar la imagen redimensionada

      $rutaImagenGuardada = 'storage/' . $rutaAlmacenamiento . $nombreArchivo;

      $especial->imagenIcon = $rutaImagenGuardada; // Asignar a el campo imagenIcon
    }

    $currentInfraestructuras = $especial->infraestructuras()->whereDate('fechaFinal', '>=', now())->pluck('idInfraestructura');
    $especial->infraestructuras()->detach($currentInfraestructuras); // No poder eliminar grupos que ya pasaron su fecha final

    if (!isset($data['infraestructuras']) || !is_array($data['infraestructuras'])) {
      $data['infraestructuras'] = [];
    }

    // Verifica y asegura que la propiedad 'jornadas' sea un arreglo o un arreglo vacío
    if (!isset($data['jornadas']) || !is_array($data['jornadas'])) {
      $data['jornadas'] = [];
    }

    foreach ($data['infraestructuras'] as $infraItem) { // Guardar infraestructura actualizada
      $this->guardarHorarioInfra($infraItem, $especial->id);
    }

    AsignacionJornadaGrupo::where('idGrupo', $especial->id)->delete();

    foreach ($data['jornadas'] as $jornaItem) {
      foreach ($jornaItem as $jItem) {
        $info = ['idGrupo' => $especial->id, 'idJornada' => $jItem];
        $asignacionJornadaGrupo = new AsignacionJornadaGrupo($info);
        $asignacionJornadaGrupo->save();
      }
    }

    $especial->save(); // Guardar el grupo actualizado

    return response()->json($especial, 200);
  }

  /**
   * Eliminar el grupo con sus relaciones
   *
   * @param  \App\Models$grupo  $grupo
   * @return \Illuminate\Http\Response
   */

  /*public function destroy(int $id)
  {
    $newjornada = Grupo::findOrFail($id);
    $newjornada->delete();
    return response()->json([
      'eliminada'
    ]);
  }*/

  public function destroy($id)
  {
    $ficha_especial = Grupo::findOrFail($id);

    if (!$ficha_especial) {
      return response()->json(['message' => 'Registro no encontrado'], 404);
    }

    $this->deleteImage($ficha_especial);

    // Eliminar el registro
    $ficha_especial->delete();

    return response()->json(['message' => 'Registro eliminado exitosamente'], 200);
  }

  private function deleteImage($ficha_especial): void {

    $rutaImagen = $ficha_especial->imagenIcon;

    $imageUrl = str_replace('storage/', 'public/', $rutaImagen); // Reemplazar ruta de storage por public

    if ($ficha_especial->imagenIcon != '') {
      Storage::delete($imageUrl);
    }

  }


  /**
   * Guarda el horario de infraestructura para un grupo.
   *
   * @param array $data Los datos del horario de infraestructura.
   * @param int $idGrupo El ID del grupo al que se asignará el horario de infraestructura.
   * @return void
   */
  private function guardarHorarioInfra(array $data, int $idGrupo)
  {

    $fechaInicial = $data['horario_infraestructura']['fechaInicial'];
    $fechaFinal = $data['horario_infraestructura']['fechaFinal'];

    if (strtotime($fechaFinal) < strtotime('today')) {
      // Si la "fechaFinal" ya ha pasado, no se guarda por infraestructuras que han terminado.
      return; // No se guarda la infraestructura y se sale de la función.
    }

    // Verificar si existe una infraestructura anterior no actualizada
    $existingInfraestructura = HorarioInfraestructuraGrupo::where('idGrupo', $idGrupo)
      ->where('idEstado', '<>', 5) // Excluir infraestructuras ya actualizadas
      ->first();

    if ($existingInfraestructura) {
      $existingInfraestructura->update(['idEstado' => 5]); // 5 = ACTUALIZADO
    }

    $estadoId = ($fechaInicial > now()) ? 2 : 1; // 2 = PENDIENTE, 1 = EN CURSO

    // Crear la nueva infraestructura
    $horarioInfraestructura = new HorarioInfraestructuraGrupo([
      'idGrupo' => $idGrupo,
      'idInfraestructura' => $data['horario_infraestructura']['idInfraestructura'],
      'fechaInicial' => $fechaInicial,
      'fechaFinal' => $fechaFinal,
      'idEstado' => $estadoId
    ]);

    $horarioInfraestructura->save();
  }


  /**
   * Actualiza el estado de las infraestructuras de un grupo de "PENDIENTE" a "EN CURSO".
   *
   * @param int $idGrupo El ID del grupo.
   * @return void
   */
  private function infraestructuraEnCurso($idGrupo)
  {
    HorarioInfraestructuraGrupo::where('idGrupo', $idGrupo)
      ->where('idEstado', 2) // ID 2 representa el estado "PENDIENTE"
      ->where('fechaInicial', '<=', now())
      ->where('fechaFinal', '>', now()) // Fecha final mayor que la fecha actual
      ->update(['idEstado' => 1]); // Actualiza el campo idEstado a 1 (EN CURSO)
  }


  /**
   * Verifica si hay asignación de infraestructuras en las jornadas y horarios especificados.
   *
   * @param array $infraestructuras Un array de infraestructuras a verificar.
   * @param array $jornadas Un array de jornadas a considerar.
   * @return bool Devuelve true si existe una asignación de infraestructuras en las jornadas y horarios especificados, de lo contrario devuelve false.
   */
  private function verificarAsignacionInfraestructura($infraestructuras, $jornadas)
  {
    foreach ($infraestructuras as $infraestructura) {
      $infraestructuraId = $infraestructura['horario_infraestructura']['idInfraestructura'];
      $fechaInicial = $infraestructura['horario_infraestructura']['fechaInicial'];
      $fechaFinal = $infraestructura['horario_infraestructura']['fechaFinal'];

      $existeAsignacion = HorarioInfraestructuraGrupo::where('idInfraestructura', $infraestructuraId)
        ->where(function ($query) use ($fechaInicial, $fechaFinal, $jornadas) {
          $query->where(function ($query) use ($fechaInicial, $fechaFinal) {
            $query->where('fechaInicial', '<=', $fechaFinal)
              ->where('fechaFinal', '>=', $fechaInicial);
          })
            ->whereHas('grupo.jornadas', function ($query) use ($jornadas) {
              $query->whereIn('jornada.id', $jornadas);
            });
        })
        ->exists();

      if ($existeAsignacion) {
        return true;
      }
    }

    return false;
  }


  /**
   * Verifica si existe alguna asignación de infraestructura en conflicto para la actualización del grupo.
   *
   * @param array $infraestructuras Un arreglo de infraestructuras.
   * @param array $jornadas Un arreglo de jornadas.
   * @param int $grupoId El ID del grupo que se está actualizando.
   * @return bool Devuelve true si existe una asignación en conflicto, de lo contrario, devuelve false.
   */
  private function verificarAsignacionInfraestructuraUpdate($infraestructuras, $jornadas, $grupoId)
  {
    // Verificar si no se han realizado cambios en las infraestructuras y jornadas
    if (empty($infraestructuras) && empty($jornadas)) {
      return false;
    }

    foreach ($infraestructuras as $infraestructura) {
      $infraestructuraId = $infraestructura['horario_infraestructura']['idInfraestructura'];
      $fechaInicial = $infraestructura['horario_infraestructura']['fechaInicial'];
      $fechaFinal = $infraestructura['horario_infraestructura']['fechaFinal'];

      $existeAsignacion = HorarioInfraestructuraGrupo::where('idInfraestructura', $infraestructuraId)
        ->where(function ($query) use ($fechaInicial, $fechaFinal, $jornadas) {
          $query->where(function ($query) use ($fechaInicial, $fechaFinal) {
            $query->where('fechaInicial', '<=', $fechaFinal)
              ->where('fechaFinal', '>=', $fechaInicial);
          })
            ->whereHas('grupo.jornadas', function ($query) use ($jornadas) {
              $query->whereIn('jornada.id', $jornadas);
            });
        })
        ->where('idGrupo', '<>', $grupoId) // Excluir el grupo que se está actualizando
        ->exists();

      if ($existeAsignacion) {
        return true;
      }
    }

    return false;
  }

  public function showByIdProyectoFor(int $id)
  {
    $grupos = Grupo::with($this->relations)
      ->where('idProyectoFormativo', $id)->get();
    return response()->json($grupos);
  }


  /**
   * Get Fichas depending by parameter
   *
   * @param String $nombreTipoGrupo
   * @return \Illuminate\Http\JsonResponse A JSON response containing the participant mappings.
   * @author Andres Felipe Pizo Luligo
   */
  public function getTipoGrupoByParameter($nombreTipoGrupo)
  {
    $grupo = Grupo::whereHas('tipoGrupo', function ($query) use ($nombreTipoGrupo) {
      $query->where('nombreTipoGrupo', $nombreTipoGrupo);
    })->get();

    return $grupo;
  }


  /**
   * Create registers about configuracionRaps by this idGrupo(ficha)
   * @author Andres Felipe Pizo Luligo
   */
  public function createConfiguracionRapByGrupo($idFicha)
  {
    $ficha = Grupo::find($idFicha);

    if (!$ficha) {
      return response()->json(['message' => 'Ficha not found']);
    }

    // Programa
    $idPrograma = $ficha->proyectoFormativo->idPrograma;

    // Competencias
    $programa = Programa::find($idPrograma);
    $competencias = $programa->competencias;

    if (!$competencias) {
      return response()->json(['message' => 'Competencias not found']);
    }

    $resultadosAprendizaje = [];

    foreach ($competencias as $competencia) {
      $resultadosAprendizaje[] = $competencia->resultadosAprendizaje;
    }


    foreach ($resultadosAprendizaje as $resultado) {
      foreach ($resultado as $rap) {
        ConfiguracionRap::create([
          'idRap'             => $rap->id,
          'idInstructor'      => null,
          'idJornada'         => null,
          'idGrupo'           => $idFicha,
          'idInfraestructura' => null,
          'idEstado'          => 1,
          'horas'             => 0,
          'fechaInicial'      => null,
          'fechaFinal'        => null,
          'observacion'       => '',
        ]);
      }
    }
  }

  /**
   * Get configuracionesRaps by id ficha
   * @param int $idFicha
   * @author Andres Felipe Pizo Luligo
   */
  public function getConfiguracionRapByidFicha($idFicha): JsonResponse
  {

    //relations of configuracionRap
    $configuracionController = new ConfiguracionRapController();
    $relations = $configuracionController->relations('configuracionesRaps', ['usuarios', 'resultados']); // Traeme solo estas relaciones

    $ficha = Grupo::with($relations)->find($idFicha);

    if (!$ficha) {
      return response()->json(['message' => 'Ficha not found'], 404);
    }

    $configuracionesRaps = $ficha->configuracionesRaps;

    return response()->json($configuracionesRaps);
  }
}
