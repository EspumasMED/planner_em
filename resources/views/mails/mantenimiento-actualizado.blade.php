<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento Programado Actualizado - Espumas Medellín</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f4f4; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="background-color: #FF6633; color: #ffffff; padding: 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Mantenimiento Programado Actualizado</h1>
        </div>
        <div style="padding: 20px;">
            <p style="font-size: 16px; color: #555555;">Se ha actualizado un mantenimiento programado con los siguientes detalles:</p>
            <div style="background-color: #f8f8f8; border-left: 4px solid #FF6633; padding: 15px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #FF6633;">Fecha:&nbsp;</span>
                    <span>{{ $fechaFormateada }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #FF6633;">Hora de inicio:&nbsp;</span>
                    <span>{{ $horaInicio }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #FF6633;">Hora de fin:&nbsp;</span>
                    <span>{{ $horaFin }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #FF6633;">Estación de trabajo:&nbsp;</span>
                    <span>{{ $mantenimiento->estacion_trabajo }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: bold; color: #FF6633;">Máquinas a intervenir:&nbsp;</span>
                    <span>{{ $mantenimiento->numero_maquinas }}</span>
                </div>
            </div>
            <div style="background-color: #f0f0f0; padding: 15px; border-radius: 4px;">
                <h2 style="color: #FF6633; font-size: 18px; margin-top: 0;">Descripción del mantenimiento actualizado&nbsp;</h2>
                <p style="margin-bottom: 0; color: #444444;">{{ $mantenimiento->descripcion }}</p>
            </div>
            <div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #FF6633; color: #856404;">
                <p style="margin: 0;">Por favor, tenga en cuenta esta información actualizada para la planificación de la producción.</p>
            </div>
            <div style="margin-top: 20px; text-align: center;">
                <p style="font-style: italic; color: #666666;">Si tiene alguna pregunta sobre esta actualización, no dude en contactar al equipo de mantenimiento.</p>
            </div>
        </div>
        <div style="background-color: #333333; color: #ffffff; padding: 15px; text-align: center; font-size: 14px;">
            <p style="margin: 0;">Espumas Medellín S.A</p>
            <p style="margin: 5px 0;">Teléfono: (604) 444 14 23 Ext 4034</p>
            <p style="margin: 5px 0;">Cra. 48# 98 Sur - 05 Variante a Caldas, La Estrella, Antioquia</p>
            <p style="margin: 5px 0;">Email del editor: {{ $editor->email }}</p>
        </div>
    </div>
</body>
</html>