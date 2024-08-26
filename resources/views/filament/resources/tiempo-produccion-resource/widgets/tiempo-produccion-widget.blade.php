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
</x-filament-widgets::widget>
