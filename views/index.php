<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión - Empleados y Ventas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Sistema de Gestión - Empleados y Ventas</h1>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <!-- Mensajes de éxito/error -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-4 rounded <?= $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Navegación por pestañas -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button type="button" onclick="showTab('empleados'); return false;" id="tab-empleados" class="tab-button py-2 px-1 border-b-2 font-medium text-sm <?= $tabActiva === 'empleados' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        Empleados
                    </button>
                    <button type="button" onclick="showTab('ventas'); return false;" id="tab-ventas" class="tab-button py-2 px-1 border-b-2 font-medium text-sm <?= $tabActiva === 'ventas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        Ventas
                    </button>
                    <button type="button" onclick="showTab('calculadoras'); return false;" id="tab-calculadoras" class="tab-button py-2 px-1 border-b-2 font-medium text-sm <?= $tabActiva === 'calculadoras' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        Calculadoras
                    </button>
                </nav>
            </div>
        </div>

        <!-- Contenido de Empleados -->
        <div id="content-empleados" class="tab-content <?= $tabActiva !== 'empleados' ? 'hidden' : '' ?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Formulario de empleados -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Agregar Empleado</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="add_empleado">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" name="nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Salario</label>
                            <input type="number" name="salario" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Departamento</label>
                            <select name="departamento" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="Recursos Humanos">Recursos Humanos</option>
                                <option value="Tecnología">Tecnología</option>
                                <option value="Ventas">Ventas</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Finanzas">Finanzas</option>
                                <option value="Operaciones">Operaciones</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Agregar Empleado
                        </button>
                    </form>
                </div>

                <!-- Estadísticas de empleados -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Estadísticas</h2>
                    
                    <?php if (!empty($datosEmpleados['departamento_mayor_promedio'])): ?>
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded">
                            <h3 class="font-medium text-green-800">Departamento con Mayor Promedio</h3>
                            <p class="text-green-700">
                                <?= $datosEmpleados['departamento_mayor_promedio']['departamento'] ?>: 
                                $<?= number_format($datosEmpleados['departamento_mayor_promedio']['promedio_salario'], 2) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <h3 class="font-medium mb-2">Promedio por Departamento</h3>
                    <div class="space-y-2">
                        <?php foreach ($datosEmpleados['promedios_departamento'] as $promedio): ?>
                            <div class="flex justify-between p-2 bg-gray-50 rounded">
                                <span><?= $promedio['departamento'] ?></span>
                                <span class="font-medium">$<?= number_format($promedio['promedio_salario'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Lista de empleados -->
            <div class="mt-6 bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Todos los Empleados</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Nombre</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Salario</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Departamento</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($datosEmpleados['empleados'] as $empleado): ?>
                                <?php 
                                $sobrePromedio = false;
                                foreach ($datosEmpleados['empleados_sobre_promedio'] as $empSobrePromedio) {
                                    if ($empSobrePromedio['id'] === $empleado['id']) {
                                        $sobrePromedio = true;
                                        break;
                                    }
                                }
                                ?>
                                <tr class="<?= $sobrePromedio ? 'bg-yellow-50' : '' ?>">
                                    <td class="px-3 py-1.5 text-xs"><?= htmlspecialchars($empleado['nombre']) ?></td>
                                    <td class="px-3 py-1.5 text-xs">$<?= number_format($empleado['salario'], 2) ?></td>
                                    <td class="px-3 py-1.5 text-xs"><?= htmlspecialchars($empleado['departamento']) ?></td>
                                    <td class="px-3 py-1.5 text-xs">
                                        <?php if ($sobrePromedio): ?>
                                            <span class="px-1.5 py-0.5 text-xs bg-yellow-200 text-yellow-800 rounded-full">Sobre promedio</span>
                                        <?php else: ?>
                                            <span class="px-1.5 py-0.5 text-xs bg-gray-200 text-gray-800 rounded-full">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Contenido de Ventas -->
        <div id="content-ventas" class="tab-content <?= $tabActiva !== 'ventas' ? 'hidden' : '' ?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Formulario de ventas -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Agregar Venta</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="add_venta">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                            <input type="text" name="cliente" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Producto</label>
                            <input type="text" name="producto" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                            <input type="number" name="cantidad" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio Unitario</label>
                            <input type="number" name="precio_unitario" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Venta</label>
                            <input type="date" name="fecha_venta" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Agregar Venta
                        </button>
                    </form>
                </div>

                <!-- Estadísticas de ventas -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Estadísticas de Ventas</h2>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded">
                            <h3 class="font-medium text-blue-800">Total de Ventas</h3>
                            <p class="text-2xl font-bold text-blue-700"><?= $datosVentas['total_ventas'] ?></p>
                        </div>

                        <?php if (!empty($datosVentas['cliente_mas_gasto'])): ?>
                            <div class="p-4 bg-green-50 border border-green-200 rounded">
                                <h3 class="font-medium text-green-800">Cliente que Más Gastó</h3>
                                <p class="text-green-700">
                                    <?= $datosVentas['cliente_mas_gasto']['cliente'] ?>
                                    <br>
                                    <span class="text-lg font-bold">$<?= number_format($datosVentas['cliente_mas_gasto']['total_gastado'], 2) ?></span>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($datosVentas['producto_mas_vendido'])): ?>
                            <div class="p-4 bg-purple-50 border border-purple-200 rounded">
                                <h3 class="font-medium text-purple-800">Producto Más Vendido</h3>
                                <p class="text-purple-700">
                                    <?= $datosVentas['producto_mas_vendido']['producto'] ?>
                                    <br>
                                    <span class="text-lg font-bold"><?= $datosVentas['producto_mas_vendido']['total_vendido'] ?> unidades</span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-6">
                        <a href="index.php?action=generar_pdf" target="_blank" class="w-full block text-center bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Generar PDF de Ventas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lista de ventas -->
            <div class="mt-6 bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Últimas Ventas</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Cliente</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Producto</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Cantidad</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Precio Unit.</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Total</th>
                                <th class="px-3 py-1.5 text-left text-xs font-medium text-gray-700">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach (array_slice($datosVentas['ventas'], 0, 10) as $venta): ?>
                                <tr>
                                    <td class="px-3 py-1.5 text-xs"><?= htmlspecialchars($venta['cliente']) ?></td>
                                    <td class="px-3 py-1.5 text-xs"><?= htmlspecialchars($venta['producto']) ?></td>
                                    <td class="px-3 py-1.5 text-xs"><?= $venta['cantidad'] ?></td>
                                    <td class="px-3 py-1.5 text-xs">$<?= number_format($venta['precio_unitario'], 2) ?></td>
                                    <td class="px-3 py-1.5 text-xs">$<?= number_format($venta['cantidad'] * $venta['precio_unitario'], 2) ?></td>
                                    <td class="px-3 py-1.5 text-xs"><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Contenido de Calculadoras -->
        <div id="content-calculadoras" class="tab-content <?= $tabActiva !== 'calculadoras' ? 'hidden' : '' ?>">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Calculadora de Salario Neto -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Calculadora de Salario Neto (Colombia)</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="calcular_salario">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Salario Bruto</label>
                            <input type="number" name="salario_bruto" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                            Calcular
                        </button>
                    </form>
                    
                    <?php if (isset($resultadoSalario)): ?>
                        <div class="mt-4 p-4 bg-gray-50 rounded">
                            <h3 class="font-medium mb-2">Resultado:</h3>
                            <div class="text-sm space-y-1">
                                <p>Salario Bruto: $<?= number_format($resultadoSalario['salario_bruto'], 2) ?></p>
                                <p>Deducción Salud (4%): $<?= number_format($resultadoSalario['deduccion_salud'], 2) ?></p>
                                <p>Deducción Pensión (4%): $<?= number_format($resultadoSalario['deduccion_pension'], 2) ?></p>
                                <p>Retención Fuente: $<?= number_format($resultadoSalario['retencion_fuente'], 2) ?></p>
                                <hr class="my-2">
                                <p class="font-bold text-green-600">Salario Neto: $<?= number_format($resultadoSalario['salario_neto'], 2) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Calculadora de Interés Compuesto -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Calculadora de Interés Compuesto</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="calcular_interes">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Capital Inicial</label>
                            <input type="number" name="capital" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tasa de Interés Anual (%)</label>
                            <input type="number" name="tasa" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo (años)</label>
                            <input type="number" name="tiempo" step="0.1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Períodos por Año</label>
                            <select name="periodos" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="1">Anual</option>
                                <option value="2">Semestral</option>
                                <option value="4">Trimestral</option>
                                <option value="12">Mensual</option>
                                <option value="365">Diario</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700">
                            Calcular
                        </button>
                    </form>
                    
                    <?php if (isset($resultadoInteres)): ?>
                        <div class="mt-4 p-4 bg-gray-50 rounded">
                            <h3 class="font-medium mb-2">Resultado:</h3>
                            <div class="text-sm space-y-1">
                                <p>Capital Inicial: $<?= number_format($resultadoInteres['capital_inicial'], 2) ?></p>
                                <p>Tasa Anual: <?= $resultadoInteres['tasa_anual'] ?></p>
                                <p>Tiempo: <?= $resultadoInteres['tiempo_años'] ?> años</p>
                                <hr class="my-2">
                                <p class="font-bold text-green-600">Monto Final: $<?= number_format($resultadoInteres['monto_final'], 2) ?></p>
                                <p class="font-bold text-blue-600">Interés Ganado: $<?= number_format($resultadoInteres['interes_ganado'], 2) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Convertidor de Temperatura -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Convertidor de Temperatura</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="convertir_temperatura">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valor</label>
                            <input type="number" name="valor_temp" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">De:</label>
                            <select name="origen_temp" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="C">Celsius (°C)</option>
                                <option value="F">Fahrenheit (°F)</option>
                                <option value="K">Kelvin (K)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">A:</label>
                            <select name="destino_temp" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="F">Fahrenheit (°F)</option>
                                <option value="C">Celsius (°C)</option>
                                <option value="K">Kelvin (K)</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700">
                            Convertir
                        </button>
                    </form>
                    
                    <?php if (isset($resultadoTemperatura)): ?>
                        <div class="mt-4 p-4 bg-gray-50 rounded">
                            <h3 class="font-medium mb-2">Resultado:</h3>
                            <p class="text-lg">
                                <?= $resultadoTemperatura['valor_original'] ?>°<?= $resultadoTemperatura['unidad_origen'] ?> = 
                                <span class="font-bold text-purple-600"><?= $resultadoTemperatura['valor_convertido'] ?>°<?= $resultadoTemperatura['unidad_destino'] ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Convertidor de Velocidad -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Convertidor de Velocidad</h2>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="convertir_velocidad">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valor</label>
                            <input type="number" name="valor_vel" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">De:</label>
                            <select name="origen_vel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="m/s">Metros por segundo (m/s)</option>
                                <option value="km/h">Kilómetros por hora (km/h)</option>
                                <option value="mph">Millas por hora (mph)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">A:</label>
                            <select name="destino_vel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="km/h">Kilómetros por hora (km/h)</option>
                                <option value="m/s">Metros por segundo (m/s)</option>
                                <option value="mph">Millas por hora (mph)</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-orange-600 text-white py-2 px-4 rounded-md hover:bg-orange-700">
                            Convertir
                        </button>
                    </form>
                    
                    <?php if (isset($resultadoVelocidad)): ?>
                        <div class="mt-4 p-4 bg-gray-50 rounded">
                            <h3 class="font-medium mb-2">Resultado:</h3>
                            <p class="text-lg">
                                <?= $resultadoVelocidad['valor_original'] ?> <?= $resultadoVelocidad['unidad_origen'] ?> = 
                                <span class="font-bold text-orange-600"><?= $resultadoVelocidad['valor_convertido'] ?> <?= $resultadoVelocidad['unidad_destino'] ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remover estilos activos de todos los botones
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Mostrar el contenido seleccionado
            const contentElement = document.getElementById('content-' + tabName);
            if (contentElement) {
                contentElement.classList.remove('hidden');
            }
            
            // Activar el botón seleccionado
            const activeButton = document.getElementById('tab-' + tabName);
            if (activeButton) {
                activeButton.classList.remove('border-transparent', 'text-gray-500');
                activeButton.classList.add('border-blue-500', 'text-blue-600');
            }
        }

        // Inicializar la primera pestaña
        document.addEventListener('DOMContentLoaded', function() {
            showTab('<?= $tabActiva ?>');
        });
    </script>
</body>
</html>