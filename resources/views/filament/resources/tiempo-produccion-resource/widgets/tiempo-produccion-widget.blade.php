<x-filament-widgets::widget>
    <x-filament::section>
        <div style="
            display: flex; 
            flex-wrap: wrap; 
            gap: 15px; 
            padding: 15px;
            width: 100%;
            box-sizing: border-box;
        ">
            @foreach ($totalTimeByStation as $station => $totalMinutes)
                <div style="
                    flex: 1 1 calc(25% - 15px); /* Ajusta el tamaño de las tarjetas a 25% menos el espacio de separación */
                    border: 1px solid #ddd; 
                    border-radius: 8px; 
                    padding: 15px; 
                    text-align: center; 
                    font-size: 14px; 
                    background-color: #f9f9f9; 
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    box-sizing: border-box;
                ">
                    <strong style="display: block; font-size: 16px;">
                        {{ ucfirst(str_replace('_', ' ', $station)) }}
                    </strong>
                    Tiempo total: {{ $totalMinutes }} minutos
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
