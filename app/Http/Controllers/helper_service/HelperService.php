<?php

namespace App\Http\Controllers\helper_service;

use App\Http\Controllers\Controller;

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
  public static function relations($mainRelation = null, $includeSelected = true, ?array $relationsNames = null, ?array $selectedRelations = null)
  {
    // Inicializar un array vacío para $allRelations
    $allRelations = [];

    // Si $relationsNames no es nulo, agrégalo a $allRelations
    if (!is_null($relationsNames)) { // LLenando el array de relations
      $allRelations = $relationsNames;
    }

    /**
     * relations('', ['usuarios', 'infraestructuras']); => ['usuarios', 'infraestructuras'] => retorna relaciones del mismo controlador pero solo las que se quieran
     */
    if (!is_null($mainRelation) && $selectedRelations && $mainRelation == '') { // Retornar relaciones que se quieran para el mismo controlador

      // Si $mainRelation está presente y es válida, agrégala a las relaciones seleccionadas
      if ($mainRelation && in_array($mainRelation, $allRelations)) {
        // Concatena $mainRelation a las relaciones seleccionadas
        $selectedRelations[] = $mainRelation;
      }

      // Si $includeSelected es true, incluye las relaciones seleccionadas
      $relationsToReturn = $includeSelected ? $selectedRelations : [];

      // Agrega un punto (.) para anidar las relaciones
      $nestedRelations = array_map(function ($relation) use ($mainRelation) {
        return $mainRelation . $relation;
      }, $relationsToReturn);

      return $nestedRelations;
    }

    /**
     * relations('configuracionesRaps'); => Retorna todas las relaciones que esten en el otro controlador tomando como indice este parametro
     */
    if ($selectedRelations && $mainRelation != '') { // Retornar relaciones que se quieran desde otro controlador

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

    /**
    * $this->relations() esto te retorna todo lo que tengas dentro de la function
    * @example Ejemplo
    * ```
    * public static function relations($nameMainRelation = '', ?array $selectedRelations = null)
    * {
    *                                        // 1					2													3			4
    *  $relations = HelperService::relations('' | $nameMainRelation, true, [
    *    'resultados',
    *    'usuarios',
    *    'estados',
    *    'jornadas',
    *    'infraestructuras',
    *    'grupos',
    *    'asistencias',
    *  ], $selectedRelations);
    *
    *  return $relations;
    *  
    * }
    * 
    * ``` => Te devuelve exactemente todas
    */
    if (!is_null($mainRelation) && $mainRelation == '') { // Retornar todas las relaciones en el mismo controlador
      // Agregar $mainRelation como prefijo a todas las relaciones en $allRelations
      $allRelations = array_map(function ($relation) use ($mainRelation) {
        return $mainRelation . $relation;
      }, $allRelations);

      return $allRelations;
    }

    /**
     * relations('configuracionesRaps'); si en el controlador estan las relaciones del modelo principal => [configuracionesRaps.'relacionDelmodelo', etc...]
     */
    if (!is_null($mainRelation) && $mainRelation != '') { // Retornar todas las relaciones desde otro controlador
      // Agregar $mainRelation como prefijo a todas las relaciones en $allRelations
      $allRelations = array_map(function ($relation) use ($mainRelation) {
        return $mainRelation . '.' . $relation;
      }, $allRelations);

      return $allRelations;
    }

  }



}
