<?php

namespace App\Http\Controllers\gestion_configuracion_rap;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helper_service\HelperService;
use Illuminate\Http\Request;
use App\Models\ConfiguracionRap;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;

class ConfiguracionRapController extends Controller
{

	public static function relations($nameMainRelation = '', ?array $selectedRelations = null)
	{
		// 1										2			3			4
		$relations = HelperService::relations('' | $nameMainRelation, true, [
			'resultados',
			'usuarios',
			'estados',
			'jornadas',
			'infraestructuras',
			'grupos',
			'asistencias',
		], $selectedRelations);

		return $relations;
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
		$configuracionRap = ConfiguracionRap::with($this->relations()); // Traeme todas las relaciones

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

		$rapInSameDate = $this->validateConfiguracionRapByDate($data['idInfraestructura'], $data['idJornada'], $data['idGrupo'], $data['fechaInicial'], $data['fechaFinal']);

		if (!$rapInSameDate) {
			return response()->json(['error' => 'No puedes asignar esta configuracion porque ya esta ocupada'], 400);
		}

		$configuracionRap = new ConfiguracionRap($data);
		$configuracionRap->save();

		return response()->json($configuracionRap, 201);
	}

	/**
	 * Validate assign new configuracion by date
	 * @return bool
	 * @author Andres Felipe Pizo Luligo
	 */

	private function validateConfiguracionRapByDate($infraestructura, $jornada, $ficha, $fechaInicio, $fechaFin): bool
	{
		// Consulta registros existentes que se superponen o están dentro del rango dado
		$matchingRecords = ConfiguracionRap::where('idInfraestructura', $infraestructura)
			->where('idJornada', $jornada)
			->where('fechaInicial', '<=', $fechaFin)
			->where('fechaFinal', '>=', $fechaInicio)
			->where(function ($query) use ($ficha) {
				$query->where('idGrupo', $ficha);
			})
			->count();

		// Si se encuentra al menos un registro que coincide o no se encuentra
		// ningún registro para la misma infraestructura, jornada y ficha, la validación falla
		return $matchingRecords === 0;
	}


	/**
	 * Show configuracionRap by id
	 */
	public function show($id): JsonResponse
	{
		$configuracionRap = ConfiguracionRap::with($this->relations())->find($id);
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

		$this->changeInstructor($data, $configuracionRap->fechaInicial, $configuracionRap->fechaFinal);

		return response()->json(['message' => 'Instructor nuevo creado']);
	}

	/**
	 * Change instructor to new in configuracion rap
	 * @param array $data
	 * @return void
	 * @author Andres Felipe Pizo Luligo
	 */
	private function changeInstructor(array $data, $fechaInicial = null, $fechaFinal = null): void
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

	/**
	 * This function delete register of configuracionRap
	 *
	 * @param int $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse
	{
		$configuracionRap = ConfiguracionRap::findOrFail($id);
		$result = $configuracionRap->delete();
		if ($result) {
			return response()->json(["message" => "delete success"]);
		} else {
			return response()->json(["message" => "delete failed"]);
		}
	}


	// Validations news
	/**
	 * Count sessions depending of fechaInicial, numbers of days and hours of the day(Jornada)
	 * @return void
	 */
	public function countSessions($idConfiguracionRap): JsonResponse // Get all object of configuracion by Id
	{

		$data = ConfiguracionRap::findOrFail($idConfiguracionRap);

		// get fechaInicial
		$fechaInicial = new DateTime($data->fechaInicial);
		$fechaFinal = new DateTime($data->fechaFinal);

		// Calcula la diferencia en días
		$diferencia = $fechaInicial->diff($fechaFinal);
		$numeroDeDias = $diferencia->days;

		$cantDays = $this->validateCantWeeksOfDates($fechaInicial, $fechaFinal, $numeroDeDias);

		// Calcula la cantidad de semanas
		$numeroDeSemanas = ceil($cantDays / 7);

		// get cant days of jornada
		$cantDiasByJornada = $data->jornadas->diaJornada->count();

		$sessions = $numeroDeSemanas * $cantDiasByJornada;

		// get hours total of configuracionRap
		$cantHoursTotal = $sessions * $data->horas;

		return response()->json([
			'cantWeeks' 				=> $numeroDeSemanas,
			'cantDaysByJornada' => $cantDiasByJornada,
			'sessions'  				=> $sessions,
			'cantHorasByDay'		=> $data->horas,
			'cantHorasTotal'		=> $cantHoursTotal,
		]);

	}

	/**
	 * This function validate cant of weeks
	 *
	 * @param DateTime $fechaInicial
	 * @param DateTime $fechaFinal
	 * @return integer
	 */
	private function validateCantWeeksOfDates($fechaInicial = null, $fechaFinal = null, $numeroDeDias = null): int
	{

		// // Ajuste para contar semanas completas si la fecha inicial es un viernes o la fecha final es un lunes
		// if (
		// 	$fechaInicial->format('N') == 5 ||
		// 	$fechaInicial->format('N') == 4 ||
		// 	$fechaInicial->format('N') == 3 ||
		// 	$fechaInicial->format('N') == 2 ||
		// 	$fechaInicial->format('N') == 1
		// 	) {  // Si la fecha inicial es un viernes (5)
		// 	$numeroDeDias += 7;
		// }

		// if (
		// 	$fechaFinal->format('N') == 1 ||
		// 	$fechaFinal->format('N') == 2 ||
		// 	$fechaFinal->format('N') == 3 ||
		// 	$fechaFinal->format('N') == 4 ||
		// 	$fechaFinal->format('N') == 5
		// 	) {  // Si la fecha final es un lunes (1)
		// 	$numeroDeDias += 7;
		// }

		if ($fechaInicial->format('N') <= 5) {  // Si la fecha inicial es de lunes a viernes (1-5)
			$numeroDeDias += (5 - $fechaInicial->format('N')); // Agrega días para llegar al viernes
		}

		if ($fechaFinal->format('N') >= 1) {  // Si la fecha final es de lunes a domingo (1-7)
			$numeroDeDias += (1 - $fechaFinal->format('N')); // Agrega días para llegar al lunes
		}

		return $numeroDeDias;
		
	}

}
