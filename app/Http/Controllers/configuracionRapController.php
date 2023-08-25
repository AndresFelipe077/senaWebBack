<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AsignacionParticipante;
use App\Models\Competencias;
use Illuminate\Http\Request;
use App\Models\configuracionRap;
use App\Models\resultadoAprendizaje;
use Illuminate\Http\JsonResponse;

class configuracionRapController extends Controller
{

	public function index(Request $request)
	{
		$resultado = $request->input('resultados');
		$instructor = $request->input('usuarios');
		$estado = $request->input('estados');
		$jornada = $request->input('jornadas');
		$grupo = $request->input('grupos');
		$infraestructura = $request->input('infraestructuras');
		$configuracionRap = configuracionRap::with('resultados', 'usuarios', 'estados', 'jornadas', 'grupos', 'infraestructuras');

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


	public function store(Request $request)
	{
		$data = $request->all();

		$configuracionRap = new configuracionRap($data);
		$configuracionRap->save();
		return response()->json($configuracionRap, 201);
	}


	public function show($id)
	{
		$configuracionRap = configuracionRap::find($id);
		return response()->json($configuracionRap, 200);
	}

	/**
	 * Hours that are lost due to raps that the competition has depending on the attendance of the instructor
	 * @author Andres Felipe Pizo Luligo
	 */
	/*public function getHoursLostForRapInCompetenciaByInstructor($idInstructor): JsonResponse
	{

		$rapsByCompetencia = ConfiguracionRap::where('idInstructor', $idInstructor)
			->with(['asistencias' => function ($query) {
				$query->where('asistencia', 0);
			}, 'jornadas' => function ($query) {
				$query->select('id', 'numeroHoras');
			}])
			->withCount(['asistencias as inasistencias' => function ($query) {
				$query->where('asistencia', 0);
			}])
			->get(['id', 'horas', 'idJornada']);

		$rapsByCompetencia->each(function ($rap) {
			$calculatedValue = 0;

			if ($rap->jornadas) {
				$calculatedValue = $rap->jornadas->numeroHoras;
			}

			$rap->calculatedValue = $calculatedValue;

		});

		$hoursLost = 0;

		$hoursLost = $rapsByCompetencia->calculatedValue * $rapsByCompetencia->inasistencias;

		return response()->json($rapsByCompetencia);
	}*/
	public function getHoursLostForRapInCompetenciaByInstructor($idInstructor)
	{
		$rapsByCompetencia = ConfiguracionRap::where('idInstructor', $idInstructor)
			->with(['asistencias' => function ($query) {
				$query->where('asistencia', 0);
			}, 'jornadas' => function ($query) {
				$query->select('id', 'numeroHoras');
			}])
			->withCount(['asistencias as inasistencias' => function ($query) {
				$query->where('asistencia', 0);
			}])
			->get(['id', 'horas', 'idJornada', 'idRap']);

		$rapsByCompetencia->each(function ($rap) {
			$calculatedValue = 0;

			if ($rap->jornadas) {
				$calculatedValue = $rap->jornadas->numeroHoras;
			}

			$rap->calculatedValue = $calculatedValue;
		});

		$hoursLost = 0;

		foreach ($rapsByCompetencia as $rap) {
			$hoursLost += $rap->calculatedValue * $rap->inasistencias;
		}

		$result = [
			'hoursLost' => $hoursLost,
			'rapsByCompetencia' => $rapsByCompetencia->toArray()
		];

		return response()->json($result);
	}




	public function update(Request $request, $id)
	{
		$data = $request->all();
		$configuracionRap = configuracionRap::findOrFail($id);
		$configuracionRap->fill($data);
		$configuracionRap->save();

		return response()->json($configuracionRap, 203);
	}

	public function destroy($id)
	{
		$configuracionRap = configuracionRap::findOrFail($id);
		$configuracionRap->delete();

		return response()->json([]);
	}
}
