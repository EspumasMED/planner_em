<x-filament-widgets::widget>
<!-- Sección de formulario en una tarjeta -->
<!-- Sección de formulario en una tarjeta -->
<div style="
    display: flex; 
    justify-content: flex-end; /* Alinea el formulario a la derecha */
">
    <div style="
        width: 100%; 
        max-width: 500px; 
        padding: 10px; 
        border: 1px solid #ddd; 
        border-radius: 8px; 
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        background-color: #fff; /* Fondo blanco */
        display: flex; 
        flex-direction: column; 
        gap: 10px;
    ">
        <h3 style="font-size: 16px; margin-bottom: 10px; color: #28a745; font-weight: bold;">Rango de Fechas</h3>
        <form wire:submit.prevent="filterResults" style="
            display: flex; 
            align-items: center; 
            gap: 10px;
        ">
            <div style="
                display: flex; 
                flex-direction: column;
                border: 1px solid #ddd; 
                border-radius: 4px; 
                padding: 5px;
            ">
                <x-filament::input wire:model="startDate" id="startDate" type="date" style="border: none; padding: 5px; width: 120px; font-weight: bold; color: #28a745;" />
            </div>
            <div style="
                display: flex; 
                flex-direction: column;
                border: 1px solid #ddd; 
                border-radius: 4px; 
                padding: 5px;
            ">
                <x-filament::input wire:model="endDate" id="endDate" type="date" style="border: none; padding: 5px; width: 120px; font-weight: bold; color: #28a745;" />
            </div>
            <x-filament::button type="submit" style="
                padding: 5px 10px; 
                background-color: #3490dc; 
                color: white; 
                border: none; 
                border-radius: 5px; 
                cursor: pointer;
                font-size: 14px;
            ">
                Filtrar
            </x-filament::button>
        </form>
    </div>
</div>



    <x-filament::section>
        <!-- Sección de tarjetas que muestran la capacidad y el tiempo necesario -->
        <div style="
            display: flex; 
            flex-wrap: wrap; 
            gap: 15px; 
            padding: 15px;
            width: 100%;
            box-sizing: border-box;
        ">
            @foreach ($data as $item)
                <div style="
                    flex: 1 1 calc(25% - 15px); 
                    border: 1px solid #ddd; 
                    border-radius: 8px; 
                    padding: 15px; 
                    text-align: center; 
                    font-size: 14px; 
                    background-color: {{ $item['totalMinutes'] <= $item['capacidadDisponible'] ? '#d4edda' : '#f8d7da' }};
                    color: {{ $item['totalMinutes'] <= $item['capacidadDisponible'] ? '#155724' : '#721c24' }};
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    box-sizing: border-box;
                ">
                    <strong style="display: block; font-size: 20px;">
                        {{ ucfirst(strtolower($item['station'])) }}
                    </strong>
                    T. Necesario: {{ $item['totalMinutes'] }} min<br>
                    T. Disponible: {{ $item['capacidadDisponible'] }} min
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section>
        <!-- Sección de aviso si el plan no es viable -->
        <div style="
            width: 50%;
            padding: 15px; 
            border: 1px solid {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }};
            background-color: {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }};
            color: {{ !empty($estacionesNoViables) ? '#721c24' : '#155724' }};
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
            box-sizing: border-box;
        ">
            @php
                $estacionesNoViables = [];
            @endphp

            @foreach ($data as $item)
                @if ($item['totalMinutes'] > $item['capacidadDisponible'])
                    @php
                        $extraMinutes = $item['totalMinutes'] - $item['capacidadDisponible'];
                        $extraHours = floor($extraMinutes / 60);
                        $remainingMinutes = $extraMinutes % 60;

                        $estacionesNoViables[] = [
                            'station' => ucfirst(strtolower($item['station'])),
                            'extraHours' => $extraHours,
                            'remainingMinutes' => $remainingMinutes
                        ];
                    @endphp
                @endif
            @endforeach

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
    </x-filament::section>
</x-filament-widgets::widget>
