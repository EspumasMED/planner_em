<x-filament-widgets::widget>
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
                    flex: 1 1 calc(25% - 15px); /* Ajusta el tamaño de las tarjetas a 25% menos el espacio de separación */
                    border: 1px solid #ddd; 
                    border-radius: 8px; 
                    padding: 15px; 
                    text-align: center; 
                    font-size: 14px; 
                    background-color: {{ $item['totalMinutes'] <= $item['capacidadDisponible'] ? '#d4edda' : '#f8d7da' }}; /* Verde si es menor o igual, rojo si es mayor */
                    color: {{ $item['totalMinutes'] <= $item['capacidadDisponible'] ? '#155724' : '#721c24' }}; /* Ajuste de color de texto */
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
        <!-- Sección de aviso si el plan no es viable, con filas y columnas -->
        <div style="
            width: 50%; /* Ajusta el ancho al 50% del contenedor */
            padding: 15px; 
            border: 1px solid {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }}; /* Rojo si no es viable, verde si es viable */
            background-color: {{ !empty($estacionesNoViables) ? '#f8d7da' : '#d4edda' }}; /* Rojo si no es viable, verde si es viable */
            color: {{ !empty($estacionesNoViables) ? '#721c24' : '#155724' }}; /* Ajuste de color de texto */
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
                        // Calcular el tiempo extra necesario en minutos
                        $extraMinutes = $item['totalMinutes'] - $item['capacidadDisponible'];
                        // Convertir el tiempo extra a horas y minutos
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
                    background-color: #d4edda; /* Verde claro para indicar viabilidad */
                    color: #155724; /* Verde oscuro para el texto */
                ">El plan es viable para todas las estaciones.</strong>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
