<x-filament::widget>
    <x-filament::card>
        <div class="space-y-4">
            @forelse ($capacities as $capacidad)
                <div>
                    <p>Estación {{ $capacidad->estacion_trabajo }} tiene {{ $capacidad->result }} de capacidad.</p>
                </div>
            @empty
                <p>No hay datos disponibles.</p>
            @endforelse
        </div>
    </x-filament::card>
</x-filament::widget>
