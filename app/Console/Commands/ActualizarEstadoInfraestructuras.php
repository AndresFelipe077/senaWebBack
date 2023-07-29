<?php

namespace App\Console\Commands;

use App\Models\HorarioInfraestructuraGrupo;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ActualizarEstadoInfraestructuras extends Command
{
    
    protected $signature = 'infraestructuras:actualizar-estado';

    protected $description = 'Actualiza el estado de las infraestructuras automáticamente';

    public function handle()
    {
        $infraestructuras = HorarioInfraestructuraGrupo::where('idEstado', 2) // ID 2 representa el estado "PENDIENTE"
            ->where('fechaInicial', '<=', now())
            ->where('fechaFinal', '>', now())
            ->get();

        foreach ($infraestructuras as $infraestructura) {
            $infraestructura->update(['idEstado' => 1]); // Actualiza el campo idEstado a 1 (EN CURSO)
        }

        $this->info('Se han actualizado los estados de las infraestructuras.');
    }

}
