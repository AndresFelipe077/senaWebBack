<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelperService extends Controller
{

    /**
     *  # Experiment relations
     * Obtener relaciones dinámicas para un controlador.
     *
     * Esta función permite obtener relaciones para un controlador desde otro controlador.
     *
     * @param bool $returnRelationsHere Indica si se deben devolver todas las relaciones disponibles.
     * @param string|null $mainRelation La relación principal que se desea seleccionar.
     * @param bool|null $includeSelected Indica si se deben incluir las relaciones seleccionadas.
     * @param array|null $selectedRelations Un array de relaciones adicionales para incluir.
     *
     * @return array|string|array[string]|null
     *   - Si $returnRelationsHere es true, devuelve todas las relaciones disponibles.
     *   - Si $returnRelationsHere es false y $includeSelected es true, devuelve las relaciones seleccionadas anidadas con $mainRelation.
     *   - Si $returnRelationsHere es false y $includeSelected es false, devuelve las relaciones seleccionadas sin anidar.
     *
     * @example Ejemplo
     * ```
     * $configuracionController = new ConfiguracionRapController();
     * $relations = $configuracionController->relations(false, 'configuracionesRaps', true, ['jornadas', 'infraestructuras', 'usuarios', 'resultados']);
     * // Devuelve: ['configuracionesRaps.jornadas', 'configuracionesRaps.infraestructuras', 'configuracionesRaps.usuarios', 'configuracionesRaps.resultados']
     * ```
     *
     * @author Andres Felipe Pizo Luligo
     */
    /*public function relations($returnRelationsHere = false, $mainRelation = null, $includeSelected = true | null, ?array $selectedRelations = null, ?array $relationsNames = null) // relations(false, 'configuracionesRaps', true, ['jornadas', 'usuarios', 'resultados']); => 'configuracionesRaps.jornadas'
	{
		// Definir un array de todas las relaciones disponibles
		$allRelations = [
			'resultados',
			'usuarios',
			'estados',
			'jornadas',
			'infraestructuras',
			'grupos',
			'asistencias',
		];

		if ($returnRelationsHere) {
			return $allRelations;
		}

		// Si $mainRelation está presente y es válida, agrégala a las relaciones seleccionadas
		if ($mainRelation && in_array($mainRelation, $allRelations)) {
			// Concatena $mainRelation a las relaciones seleccionadas
			$selectedRelations[] = $mainRelation;
		}

		// Si $includeSelected es true, incluye las relaciones seleccionadas
		$relationsToReturn = $includeSelected ? $selectedRelations : [];

		// Agrega un punto (.) para anidar las relaciones
		$nestedRelations = array_map(function ($relation) use ($mainRelation) {
			return $mainRelation . '.' . $relation;
		}, $relationsToReturn);

		return $nestedRelations;
	}*/
    public static function relations($returnRelationsHere = false, $mainRelation = null, $includeSelected = true, ?array $relationsNames = null, ?array $selectedRelations = null )
    {
        // Inicializar un array vacío para $allRelations
        $allRelations = [];

        // Si $relationsNames no es nulo, agrégalo a $allRelations
        if (!is_null($relationsNames)) {
            $allRelations = $relationsNames;
        }

        if ($returnRelationsHere) {
            return $allRelations;
        }

        // Si $mainRelation está presente y es válida, agrégala a las relaciones seleccionadas
        if ($mainRelation && in_array($mainRelation, $allRelations)) {
            // Concatena $mainRelation a las relaciones seleccionadas
            $selectedRelations[] = $mainRelation;
        }

        // Si $includeSelected es true, incluye las relaciones seleccionadas
        $relationsToReturn = $includeSelected ? $selectedRelations : [];

        // Agrega un punto (.) para anidar las relaciones
        $nestedRelations = array_map(function ($relation) use ($mainRelation) {
            return $mainRelation . '.' . $relation;
        }, $relationsToReturn);

        return $nestedRelations;
    }



}
