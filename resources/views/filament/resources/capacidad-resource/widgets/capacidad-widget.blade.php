<x-filament::widget>
    <x-filament::section>
        <div class="p-4 bg-white rounded-lg shadow w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ajuste de Capacidad</h3>
            
            <div class="mb-4">
                <label for="porcentajeOptimismo" class="block text-sm font-medium text-gray-700 mb-2">
                    Porcentaje de Optimismo: {{ $porcentajeOptimismo }}%
                </label>
                <input type="range" wire:model.live="porcentajeOptimismo" min="0" max="100" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
            </div>

            @if(count($capacidades) > 0)
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Tiempo de Jornada Original (100%): {{ $capacidades[0]['tiempo_jornada_original'] }}</p>
                    <p class="text-sm text-gray-600">Tiempo de Jornada Ajustado: {{ $capacidades[0]['tiempo_jornada_ajustado'] }}</p>
                </div>
            @else
                <p class="text-sm text-gray-600 mb-4">No hay datos disponibles</p>
            @endif

            <button wire:click="actualizarTablaCapacidad" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded border border-orange-600 shadow-md transition duration-300 ease-in-out transform hover:scale-105" style="color: #fe890b;">
                Actualizar Capacidad
            </button>
        </div>
    </x-filament::section>
</x-filament::widget>