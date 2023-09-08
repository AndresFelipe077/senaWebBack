<?php

namespace App\Http\Controllers\gestion_configuracion_rap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfiguracionRap;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ConfiguracionRapController extends Controller
{

	private $relations;

	public function __construct()
	{
		$this->relations = [
			'resultados',
			'usuarios',
			'estados',
			'jornadas',
			'infraestructuras',
			'grupos',
			'asistencias'
		];
	}

	/** 
	 * Get information about configuracionRap
	 */
	public function index(Request $request)
	{
		$resultado = $request->input('resultados');
		$instructor = $request->input('usuarios');
		$estado = $request->input('estados');
		$jornada = $request->input('jornadas');
		$grupo = $request->input('grupos');
		$infraestructura = $request->input('infraestructuras');
		$configuracionRap = ConfiguracionRap::with($this->relations);

		if ($resultado) {
			$configuracionRap->whereHas('resultados', function ($q) use ($resultado) {
				return $q->select('id')->where('id', $resultado)->orWhere('rap', $resultado);
			});
		};

		if ($instructor) {
			$configuracionRap->whereHas('usuarios', function ($q) use ($instructor) {
				return $q->select('id')->where('id', $instructor)->orWhere('usuarios', $instructor);
			});
		};

		if ($estado) {
			$configuracionRap->whereHas('estados', function ($q) use ($estado) {
				return $q->select('id')->where('id', $estado)->orWhere('estados', $estado);
			});
		};

		if ($jornada) {
			$configuracionRap->whereHas('jornadas', function ($q) use ($jornada) {
				return $q->select('id')->where('id', $jornada)->orWhere('jornadas', $jornada);
			});
		};

		if ($grupo) {
			$configuracionRap->whereHas('grupos', function ($q) use ($grupo) {
				return $q->select('id')->where('id', $grupo)->orWhere('grupos', $grupo);
			});
		};

		if ($infraestructura) {
			$configuracionRap->whereHas('infraestructuras', function ($q) use ($infraestructura) {
				return $q->select('id')->where('id', $infraestructura)->orWhere('infraestructuras', $infraestructura);
			});
		};


		return response()->json($configuracionRap->get());
	}

	/**
	 * Store information about configuracionRap
	 * @return JsonResponse
	 * @author Andres Felipe Pizo Luligo
	 * 
	 */
	public function store(Request $request): JsonResponse
	{

		$data = $request->all();

		$configuracionRap = new ConfiguracionRap($data);
		$configuracionRap->save();

		return response()->json($configuracionRap, 201);
	}

	public function show($id)
	{
		$configuracionRap = ConfiguracionRap::find($id);
		return response()->json($configuracionRap, 200);
	}

	/**
	 * Hours that are lost due to raps that the competition has depending on the attendance of the instructor
	 * @author Andres Felipe Pizo Luligo
	 */
	public function getHoursLostForRapInCompetenciaByInstructor($idInstructor): JsonResponse
	{

		$usuario = User::with('persona')->find($idInstructor);

		$rapsByCompetencia = ConfiguracionRap::where('idInstructor', $idInstructor)
			->with(['asistencias' => function ($query) use ($idInstructor) {
				$query->whereIn('idConfiguracionRap', function ($subquery) use ($idInstructor) {
					$subquery->select('id')
						->from('configuracionRap')
						->where('idInstructor', $idInstructor);
				})->where('asistencia', 0);
			}, 'jornadas' => function ($query) {
				$query->select('id', 'numeroHoras');
			}, 'resultados.competencia' => function ($query) {
				$query->select('id', 'horas');
			}])
			->get(['id', 'horas', 'idJornada', 'idRap', 'idInstructor']);

		$rapsByCompetencia->each(function ($rap) {
			$calculatedValue = 0;

			if ($rap->jornadas) {
				$calculatedValue = $rap->horas;
			}

			$rap->calculatedValue = $calculatedValue;
		});

		$totalInasistencias = 0; // Agregar un contador para las inasistencias totales
		$hoursLost = 0;

		foreach ($rapsByCompetencia as $rap) {
			$inasistenciasCount = $rap->asistencias->count();
			$totalInasistencias += $inasistenciasCount; // Sumar al contador total
			$hoursLost += $rap->calculatedValue * $inasistenciasCount;
		}

		$result = [
			'hoursLost' => $hoursLost,
			'totalInasistencias' => $totalInasistencias,
			'usuario' => $usuario,
			'inasistenciaRaps' => $rapsByCompetencia->toArray()
		];

		return response()->json($result);
	}

	/**
	 * Update register of configuracion and create a new
	 * @author Andres Felipe Pizo Luligo
	 * 
	 */
	public function update(Request $request, $id): JsonResponse
	{
		$data = $request->all();

		$configuracionRap = ConfiguracionRap::findOrFail($id);

		// Actualiza solo el estado del registro existente
		$configuracionRap->update(['idEstado' => 3]); // TRASLADO

		$this->changeInstructor($data, $configuracionRap->fechaInicio, $configuracionRap->fechaFinal);

		return response()->json(['message' => 'Instructor nuevo creado']);
	}

	/**
	 * Change instructor to new in configuracion rap
	 * @param array $data
	 * @return void
	 * @author Andres Felipe Pizo Luligo
	 */
	public function changeInstructor(array $data, $fechaInicial = null, $fechaFinal = null)
	{

		// Validar instructor con nuevas fechas, si entran nuevas fechas se asignan tambien
		$newConfiguracionRap = new ConfiguracionRap($data);

		$newConfiguracionRap->idInstructor = $data['idInstructor'];

		// Verificar nuevas fechas distintas a las anteriores, si es asi que se guarden tambien
		if ($fechaInicial != $data['fechaInicial'] || $fechaFinal != $data['fechaFinal']) {

			$newConfiguracionRap->fechaInicial = $data['fechaInicial'];
			$newConfiguracionRap->fechaFinal = $data['fechaFinal'];
			
		}

		$newConfiguracionRap->save();

	}

	public function destroy($id)
	{
		$configuracionRap = ConfiguracionRap::findOrFail($id);
		$result = $configuracionRap->delete();
		if ($result) {
			return response()->json(["message" => "delete success"]);
		} else {
			return response()->json(["message" => "delete failed"]);
		}
	}

}
