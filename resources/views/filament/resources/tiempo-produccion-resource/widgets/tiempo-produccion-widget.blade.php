<x-filament-widgets::widget>

    <x-filament::section>
        <!-- Sección de formulario en una tarjeta unificada -->
        <x-filament::card
            style="
                display: flex; 
                flex-direction: column;
                gap: 20px;
                background-color: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                max-width: 1000px;
                width: 100%;
            "
        >
            <!-- Formulario de filtros -->
            <form wire:submit.prevent="filterResults" style="
                display: flex; 
                align-items: center; 
                justify-content: flex-end;
                gap: 20px;
            ">
                <!-- Rango de fechas -->
                <div style="
                    display: flex; 
                    flex-direction: column;
                    align-items: center;
                    gap: 5px;
                ">
                    <span style="font-size: 14px; color: #fe890b; font-weight: bold;">Rango de fechas</span>
                    <div style="display: flex; gap: 20px;">
                        <x-filament::input wire:model="startDate" id="startDate" type="date" style="
                            border: none; 
                            outline: none; 
                            padding: 10px; 
                            width: 150px; 
                            background-color: #fe890b87;
                            border-radius: 5px;
                            font-weight: bold;
                        " />
                        <x-filament::input wire:model="endDate" id="endDate" type="date" style="
                            border: none; 
                            outline: none; 
                            padding: 10px; 
                            width: 150px; 
                            background-color: #fe890b87;
                            border-radius: 5px;
                            font-weight: bold;
                        " />
                    </div>
                </div>

                <!-- Incluir Clientes -->
                <div style="
                    display: flex; 
                    flex-direction: column;
                    align-items: center;
                    gap: 5px;
                ">
                    <span style="font-size: 14px; color: #fe890b; font-weight: bold;">Clientes</span>
                    <label style="
                        display: flex; 
                        justify-content: center;
                        align-items: center;
                    ">
                        <x-filament::input wire:model="includeClientes" id="includeClientes" type="checkbox" style="
                            width: 42px; 
                            height: 42px;
                            border: 2px solid #28a745;
                            border-radius: 4px;
                            background-color: #fe890b;
                            cursor: pointer;
                        " />
                    </label>
                </div>

                <!-- Incluir Stock -->
                <div style="
                    display: flex; 
                    flex-direction: column;
                    align-items: center;
                    gap: 5px;
                ">
                    <span style="font-size: 14px; color: #fe890b; font-weight: bold;">Stock</span>
                    <label style="
                        display: flex; 
                        justify-content: center;
                        align-items: center;
                    ">
                        <x-filament::input wire:model="includeStock" id="includeStock" type="checkbox" style="
                            width: 42px; 
                            height: 42px;
                            border: 2px solid #28a745;
                            border-radius: 4px;
                            background-color: #fe890b;
                            cursor: pointer;
                        " />
                    </label>
                </div>

                <!-- Botón de filtrar -->
                <x-filament::button type="submit" style="
                    padding: 15px 25px; 
                    background-color: #fe890b; 
                    color: white; 
                    border: none; 
                    border-radius: 8px; 
                    cursor: pointer;
                    font-size: 20px;
                    margin-top: 25px;
                ">
                    Filtrar
                </x-filament::button>
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
                    T. Necesario: {{ number_format($item['totalMinutes']) }} min<br>
                    T. Disponible: {{ number_format($item['capacidadDisponible']) }} min
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <!-- Modal -->
    @if($isModalOpen)
        <div style="position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
            <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px;">
                <span wire:click="closeModal" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                <h2 style="color: #fe890b; margin-bottom: 15px;">{{ $modalData['station'] ?? '' }}</h2>
                <div>
                    <p>Tiempo Necesario: {{ number_format($modalData['totalMinutes'] ?? 0) }} minutos</p>
                    <p>Tiempo Disponible: {{ number_format($modalData['capacidadDisponible'] ?? 0) }} minutos</p>
                    <p>Diferencia: {{ number_format(($modalData['totalMinutes'] ?? 0) - ($modalData['capacidadDisponible'] ?? 0)) }} minutos</p>
                </div>
            </div>
        </div>
    @endif

    <x-filament::section>
        <!-- Contenedor flex para alinear las dos tarjetas lado a lado -->
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <!-- Nueva sección para mostrar el resumen de producción -->
            <div style="
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            ">
            <h3 style="font-size: 18px; font-weight: bold; color: #fe890b; margin-bottom: 15px;">Resumen de Producción</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div>
                    <p style="font-weight: bold; color: #fe890b;">Total Cierres:</p>
                    <p>{{ number_format($data['totalClosures']) }}</p>
                </div>
                <div>
                    <p style="font-weight: bold; color: #fe890b;">Pedidos clientes:</p>
                    <p>{{ number_format($clientOrderQuantity) }} ({{ number_format($clientOrderPercentage, 2) }}%)</p>
                </div>
                <div>
                    <p style="font-weight: bold; color: #fe890b;">Pedidos para stock:</p>
                    <p>{{ number_format($stockOrderQuantity) }} ({{ number_format($stockOrderPercentage, 2) }}%)</p>
                </div>
                <div>
                    <p style="font-weight: bold; color: #fe890b;">Total de pedidos:</p>
                    <p>{{ number_format($clientOrderQuantity + $stockOrderQuantity) }}</p>
                </div>
                <div>
                    <p style="font-weight: bold; color: #fe890b;">Colchones:</p>
                    <p>{{ number_format($colchonesCantidad) }} ({{ number_format($colchonesPercentage, 2) }}%)</p>
                </div>
                <div>
                    <p style="font-weight: bold; color: #fe890b;">Colchonetas:</p>
                    <p>{{ number_format($colchonetasCantidad) }} ({{ number_format($colchonetasPercentage, 2) }}%)</p>
                </div>
            </div>
            </div>

            <!-- Sección de aviso si el plan no es viable -->
            <div style="
                flex: 1;
                padding: 15px; 
                border: 1px solid {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }};
                background-color: {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }};
                color: {{ !empty($estacionesNoViables) ? '#721c24' : '#155724' }};
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                text-align: center;
            ">
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
                    <strong style="color: #721c24;">Este plan NO es viable para las siguientes estaciones:</strong>
                    <table style="width: 100%; margin-top: 15px; border-collapse: collapse; background-color: #f8d7da; color: #721c24;">
                        <thead>
                            <tr style="background-color: #f5c6cb;">
                                <th style="padding: 10px; border: 1px solid #721c24;">Estación</th>
                                <th style="padding: 10px; border: 1px solid #721c24;">Horas Extra Necesarias</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estacionesNoViables as $estacion)
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #721c24;">{{ $estacion['station'] }}</td>
                                    <td style="padding: 10px; border: 1px solid #721c24;">
                                        {{ $estacion['extraHours'] }} horas, {{ $estacion['remainingMinutes'] }} minutos
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <strong style="
                        display: block;
                        padding: 10px;
                        border-radius: 8px;
                        background-color: #d4edda;
                        color: #155724;
                    ">El plan es viable para todas las estaciones.</strong>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>