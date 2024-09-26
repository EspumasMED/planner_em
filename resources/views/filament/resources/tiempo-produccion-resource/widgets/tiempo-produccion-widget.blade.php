<x-filament-widgets::widget>

<x-filament::section>
    <x-filament::card class="bg-white p-4 sm:p-6 border border-gray-200 rounded-lg shadow-md max-w-4xl mx-auto">
        <form wire:submit.prevent="filterResults" class="flex flex-col sm:flex-row items-start sm:items-end justify-end gap-4 sm:gap-6">
            <div class="flex flex-col sm:flex-row items-center justify-end gap-4 sm:gap-6">
                <!-- Rango de fechas -->
                <div class="sm:w-auto flex flex-col items-center gap-2">
                    <span class="text-sm font-bold" style="color: #fe890b;">Rango de fechas</span>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">
                        <x-filament::input
                            wire:model="startDate"
                            id="startDate"
                            type="date"
                            class="sm:w-36 p-2 rounded-md font-bold"
                            style="background-color: #fe890b87; border: none; outline: none;"
                        />
                        <x-filament::input
                            wire:model="endDate"
                            id="endDate"
                            type="date"
                            class="sm:w-36 p-2 rounded-md font-bold"
                            style="background-color: #fe890b87; border: none; outline: none;"
                        />
                    </div>
                </div>

                <div class="flex gap-6">
                    <!-- Incluir Clientes -->
                    <div class="sm:w-auto flex flex-col items-end gap-2">
                        <span class="text-sm font-bold mb-1" style="color: #fe890b;">Clientes</span>
                        <x-filament::input
                            wire:model="includeClientes"
                            id="includeClientes"
                            type="checkbox"
                            class="w-8 h-9 rounded cursor-pointer"
                            style="border: 2px solid #28a745; background-color: #fe890b;"
                        />
                    </div>

                    <!-- Incluir Stock -->
                    <div class="sm:w-auto flex flex-col items-end gap-2">
                        <span class="text-sm font-bold mb-1" style="color: #fe890b;">Stock</span>
                        <x-filament::input
                            wire:model="includeStock"
                            id="includeStock"
                            type="checkbox"
                            class="w-8 h-9 rounded cursor-pointer"
                            style="border: 2px solid #28a745; background-color: #fe890b;"
                        />
                    </div>
                </div>

                <!-- Botón de filtrar -->
                <x-filament::button
                    type="submit"
                    class="px-6 py-2 text-white rounded-lg cursor-pointer text-base font-bold whitespace-nowrap"
                    style="background-color: #fe890b; margin-top: 30px; padding:8px; margin-left: 15px;"
                >
                    Filtrar
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament::section>


    <x-filament::section>
        <!-- Sección de tarjetas que muestran la capacidad y el tiempo necesario -->
        <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 15px; width: 100%; box-sizing: border-box;">
            @foreach ($data['stationData'] as $item)
                <div 
                    wire:click="openModal('{{ $item['station'] }}', {{ $item['totalMinutes'] }}, {{ $item['capacidadDisponible'] }})"
                    style="flex: 1 1 calc(25% - 15px); border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; font-size: 14px; background-color: {{ $item['totalMinutes'] <= $item['capacidadDisponible'] ? '#d4edda' : '#f8d7da' }}; color: {{ $item['totalMinutes'] <= $item['capacidadDisponible'] ? '#155724' : '#721c24' }}; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); box-sizing: border-box; cursor: pointer;"
                >
                    <strong style="display: block; font-size: 20px;">
                        {{ ucfirst(strtolower($item['station'])) }}
                    </strong>
                    @if($item['station'] === 'Acolchadora Gribetz' || $item['station'] === 'Acolchadora China')
                        T. Necesario: {{ number_format($item['totalMinutes']) }} min<br>
                        T. Disponible: {{ number_format($item['capacidadDisponible']) }} min<br>
                        M. Lineales: {{ number_format($item['totalMetrosLineales'], 2) }} m
                    @else
                        T. Necesario: {{ number_format($item['totalMinutes']) }} min<br>
                        T. Disponible: {{ number_format($item['capacidadDisponible']) }} min
                    @endif
                </div>
            @endforeach
        </div>
    </x-filament::section>
    {{--
    @if($isModalOpen)
        <div style="position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
            <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px;">
                <span wire:click="closeModal" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                <h2 style="color: #fe890b; margin-bottom: 15px;">{{ $modalData['station'] ?? '' }}</h2>
                <div>
                    <h1>Informacion</h1>
                    {{--<p>Tiempo Necesario: {{ number_format($modalData['totalMinutes'] ?? 0) }} minutos</p>
                    <p>Tiempo Disponible: {{ number_format($modalData['capacidadDisponible'] ?? 0) }} minutos</p>
                    <p>Diferencia: {{ number_format(($modalData['totalMinutes'] ?? 0) - ($modalData['capacidadDisponible'] ?? 0)) }} minutos</p> 
                </div>
            </div>
        </div>
    @endif
    --}}
    <x-filament::section>
        <div class="flex flex-col lg:flex-row gap-5 mt-5">
            <!-- Sección de aviso si el plan no es viable -->
            <div class="flex-1 p-4 rounded-lg shadow-sm text-center" style="background-color: {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }}; border: 1px solid {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }};">
                @php
                    $estacionesNoViables = [];
                    foreach ($data['stationData'] as $item) {
                        if ($item['totalMinutes'] > $item['capacidadDisponible']) {
                            $extraMinutes = $item['totalMinutes'] - $item['capacidadDisponible'];
                            $extraHours = floor($extraMinutes / 60);
                            $remainingMinutes = $extraMinutes % 60;
                            $estacionesNoViables[] = [
                                'station' => $item['station'],
                                'extraHours' => $extraHours,
                                'remainingMinutes' => $remainingMinutes
                            ];
                        }
                    }
                @endphp
    
                @if (!empty($estacionesNoViables))
                    <strong class="block mb-4" style="color: #721c24;">Este plan NO es viable para las siguientes estaciones:</strong>
                    <div class="overflow-x-auto">
                        <table class="w-full" style="background-color: #f8d7da; color: #721c24;">
                            <thead>
                                <tr style="background-color: #f5c6cb;">
                                    <th class="p-2 border" style="border-color: #721c24;">Estación</th>
                                    <th class="p-2 border" style="border-color: #721c24;">Tiempo Extra Necesario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estacionesNoViables as $estacion)
                                    <tr>
                                        <td class="p-2 border" style="border-color: #721c24;">{{ $estacion['station'] }}</td>
                                        <td class="p-2 border" style="border-color: #721c24;">
                                            {{ $estacion['extraHours'] }} horas, {{ $estacion['remainingMinutes'] }} minutos
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <strong class="block p-2 rounded-lg" style="background-color: #d4edda; color: #155724;">
                        El plan es viable para todas las estaciones.
                    </strong>
                @endif
            </div>

            <!-- Resumen de Producción -->
            <div class="flex-1 p-4 bg-white border border-gray-200 rounded-lg shadow-sm" style="padding-left: 20px;">
                <h3 class="text-lg font-bold mb-4" style="color: #fe890b;">Resumen de Producción</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <p class="font-bold" style="color: #fe890b;">Total Cierres:</p>
                        <p>{{ number_format($data['totalClosures']) }}</p>
                    </div>
                    <div>
                        <p class="font-bold" style="color: #fe890b;">Pedidos clientes:</p>
                        <p>{{ number_format($clientOrderQuantity) }} ({{ number_format($clientOrderPercentage, 2) }}%)</p>
                    </div>
                    <div>
                        <p class="font-bold" style="color: #fe890b;">Pedidos para stock:</p>
                        <p>{{ number_format($stockOrderQuantity) }} ({{ number_format($stockOrderPercentage, 2) }}%)</p>
                    </div>
                    <div>
                        <p class="font-bold" style="color: #fe890b;">Total de pedidos:</p>
                        <p>{{ number_format($clientOrderQuantity + $stockOrderQuantity) }}</p>
                    </div>
                    <div>
                        <p class="font-bold" style="color: #fe890b;">Colchones:</p>
                        <p>{{ number_format($colchonesCantidad) }} ({{ number_format($colchonesPercentage, 2) }}%)</p>
                    </div>
                    <div>
                        <p class="font-bold" style="color: #fe890b;">Colchonetas:</p>
                        <p>{{ number_format($colchonetasCantidad) }} ({{ number_format($colchonetasPercentage, 2) }}%)</p>
                    </div>
                </div>
            </div>
    
            
        </div>
    </x-filament::section>
    <x-filament::section>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 md:p-6">
            <h3 class="text-lg md:text-xl font-bold text-orange-500 mb-4" style="font-weight: bold; color: #fe890b;">Informe de acolchados</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <div class="mb-4">
                    <h4 class="text-base md:text-lg font-semibold text-orange-500 mb-2" style="font-weight: bold; color: #fe890b;">Colchones por Calibre de Acolchado</h4>
                    <ul class="grid grid-cols-2 gap-2">
                        <li><span class="text-orange-500">Calibre 1:</span> {{ number_format($cantidadColchonesCalibre1) }} COL</li>
                        <li><span class="text-orange-500">Calibre 2:</span> {{ number_format($cantidadColchonesCalibre2) }} COL</li>
                        <li><span class="text-orange-500">Calibre 3:</span> {{ number_format($cantidadColchonesCalibre3) }} COL</li>
                        <li><span class="text-orange-500">Calibre 4:</span> {{ number_format($cantidadColchonesCalibre4) }} COL</li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h4 class="text-base md:text-lg font-semibold text-orange-500 mb-2" style="font-weight: bold; color: #fe890b;">Distribución de Calibre 2</h4>
                    <p><span class="text-orange-500">China:</span> <strong>{{ number_format($distribucionCalibre2China) }} COL, ({{ number_format($porcentajeCalibre2China, 1) }}% Acolchado)</strong></p>
                    <p><span class="text-orange-500">Gribetz:</span> <strong>{{ number_format($distribucionCalibre2Gribetz) }} COL, ({{ number_format($porcentajeCalibre2Gribetz, 1) }}% Acolchado)</strong></p>
                </div>
                
                <div class="mb-4">
                    <h4 class="text-base md:text-lg font-semibold text-orange-500 mb-2" style="font-weight: bold; color: #fe890b;">Acolchados</h4>
                    <ul class="space-y-2">
                        <li><span class="text-orange-500">Total para China:</span> <strong>{{ number_format($totalColchonesChina) }} COL, {{ number_format($metrosLinealesChina) }}m. lineales</strong></li>
                        <li><span class="text-orange-500">Total para Gribetz:</span> <strong>{{ number_format($totalColchonesGribetz) }} COL, {{ number_format($metrosLinealesGribetz) }}m. lineales</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>