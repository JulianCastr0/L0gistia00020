<?php
//  CONFIGURA TUS CREDENCIALES DE BASE DE DATOS 
$servidor = "82.197.82.63";  // Cambia esto por el host de tu base de datos
$usuario = "u496558612_Utopia";      // Usuario de la base de datos
$clave = "Dev#D24@";     
$baseDatos = "u496558612_Utopia";       // Nombre de la base de datos

// CONEXIÓN A LA BASE DE DATOS
$conexion = new mysqli($servidor, $usuario, $clave, $baseDatos);

// Verificar errores de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Inicializar los filtros
$maq = isset($_GET['maq']) ? $_GET['maq'] : '';
$turn = isset($_GET['turn']) ? $_GET['turn'] : '';
$dia = isset($_GET['dia']) ? $_GET['dia'] : '';
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Si no se ha establecido el filtro de día, mes o año, usar la fecha actual
if (!$dia) {
    $dia = date('d');
}
if (!$mes) {
    $mes = date('m');
}
if (!$anio) {
    $anio = date('Y');
}

// Construir la parte WHERE de la consulta con los filtros
$condiciones = [];
if ($maq) {
    $condiciones[] = "`Máquina` = '$maq'";
}
if ($turn) {
    $condiciones[] = "`Turno` = '$turn'";
}
if ($dia) {
    $condiciones[] = "DAY(`Fecha Turno`) = '$dia'";
}
if ($mes) {
    $condiciones[] = "MONTH(`Fecha Turno`) = '$mes'";
}
if ($anio) {
    $condiciones[] = "YEAR(`Fecha Turno`) = '$anio'";
}

// Si hay filtros, añadirlos a la consulta
$filtro = (count($condiciones) > 0) ? ' WHERE ' . implode(' AND ', $condiciones) : '';

// Consulta SQL con los filtros
$sql = "SELECT `Hora`, `Máquina`, `Fecha Turno`, `Turno`, SUM(`Cantidad`) as total_cantidad
        FROM `Reporte Prod`
        $filtro
        GROUP BY `Máquina`, `Fecha Turno`, `Turno`
        ORDER BY `Fecha Turno` DESC, `Hora` DESC";

if (isset($_GET['ajax'])) {
    $resultado = $conexion->query($sql);

    $datos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }

    echo json_encode($datos);
    exit(); // Evita que el HTML se cargue en la respuesta AJAX
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos en Tiempo Real</title>
    <!-- Incluir Bootstrap desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .container {
            display: flex;
        }
        .left {
            width: 15%; /* 20% del ancho */
            align-items: left;
        }
        .right {
            width: 85%; /* 80% del ancho */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>

    
    <meta http-equiv="refresh" content="300"> <!-- Refresca la página cada 30 segundos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
<script>
        function autoScroll() {
            let velocidad = 2; // Velocidad del desplazamiento
            let intervalo = setInterval(() => {
                let alturaTotal = document.documentElement.scrollHeight;
                let alturaVisible = window.innerHeight;
                let posicionActual = window.scrollY;

                // Si llega al final, hacer un reinicio suave
                if (posicionActual + alturaVisible >= alturaTotal - 5) {
                    clearInterval(intervalo); // Detiene el desplazamiento
                    setTimeout(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' }); // Regresa suavemente al inicio
                        setTimeout(autoScroll, 800); // Espera y vuelve a iniciar
                    }, 500);
                } else {
                    window.scrollBy(0, velocidad);
                }
            }, 50); // Ajuste de tiempo entre desplazamientos
        }

        window.onload = autoScroll; // Inicia el scroll al cargar la página
    </script>

    <div>
        <form method="GET">
            <table style="border: none;">
                <td style="border: none;"> 
                    <label class="form-label" for="Maquina">Máquina:</label>
                    <select id="maq" name="maq" class="form-select">
                        <option value="">Todas las máquinas</option>
                        <option value="FANG BANG">FANG BANG</option>
                        <option value="CURIONI 2">CURIONI 2</option>
                        <option value="CURIONI 3">CURIONI 3</option>
                        <option value="CURIONI 4">CURIONI 4</option>
                        <option value="CURIONI 5">CURIONI 5</option>
                        <option value="CURIONI 6">CURIONI 6</option>
                        <option value="CURIONI 7">CURIONI 7</option>
                        <option value="J08">J08</option>
                        <option value="MAQ 550 3">MAQ 550 3</option>
                        <option value="RZFD-330">RZFD-330 </option>
                        <option value="RZFD-450">RZFD-450</option>
                        <option value="RZFD-450 2">RZFD 450 #2</option>
                        <option value="WFD-430">WFD 430</option>
                        <option value="FLEXO BICOLOR">FLEXO BICOLOR</option>
                        <option value="FLEXO MAF ANTARES">FLEXO MAF ANTARES</option>
                        <option value="FLEXO 6">FLEXO 6</option>
                        <option value="FLEXO 7">FLEXO 7</option>
                        <option value="FLEXO MAF">FLEXO MAF</option>
                        <option value="FLEXO WX4 #1">FLEXO WX4 #1</option>
                        <option value="FLEXO WX4 #2">FLEXO WX4 #2</option>
                        <option value="FLEXO ">FLEXO </option>
                    </select>
                </td>

                <td style="border: none;">
                    <label class="form-label" for="turno">Turno:</label>
                    <select id="turn" name="turn" class="form-select">
                        <option value="">Ambos</option>
                        <option value="Diurno">Diurno</option>
                        <option value="Nocturno">Nocturno</option>
                    </select>
                </td>

                <td style="border: none;">
                    <label class="form-label" for="dia">Día:</label>
                    <select id="dia" name="dia" class="form-select">
                        <option value="">Ninguno</option>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= ($dia == str_pad($i, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </td>

                <td style="border: none;">
                    <label class="form-label" for="mes">Mes:</label>
                    <select id="mes" name="mes" class="form-select">
                        <option value="">Ninguno</option>
                        <option value="01" <?= ($mes == '01') ? 'selected' : '' ?>>Enero</option>
                        <option value="02" <?= ($mes == '02') ? 'selected' : '' ?>>Febrero</option>
                        <option value="03" <?= ($mes == '03') ? 'selected' : '' ?>>Marzo</option>
                        <option value="04" <?= ($mes == '04') ? 'selected' : '' ?>>Abril</option>
                        <option value="05" <?= ($mes == '05') ? 'selected' : '' ?>>Mayo</option>
                        <option value="06" <?= ($mes == '06') ? 'selected' : '' ?>>Junio</option>
                        <option value="07" <?= ($mes == '07') ? 'selected' : '' ?>>Julio</option>
                        <option value="08" <?= ($mes == '08') ? 'selected' : '' ?>>Agosto</option>
                        <option value="09" <?= ($mes == '09') ? 'selected' : '' ?>>Septiembre</option>
                        <option value="10" <?= ($mes == '10') ? 'selected' : '' ?>>Octubre</option>
                        <option value="11" <?= ($mes == '11') ? 'selected' : '' ?>>Noviembre</option>
                        <option value="12" <?= ($mes == '12') ? 'selected' : '' ?>>Diciembre</option>
                    </select>
                </td>

                <td style="border: none;">
                    <label class="form-label" for="anio">Año:</label>
                    <select id="anio" name="anio" class="form-select">
                        <option value="2025" <?= ($anio == '2025') ? 'selected' : '' ?>>2025</option>
                        <option value="2024" <?= ($anio == '2024') ? 'selected' : '' ?>>2024</option>
                        <option value="2023" <?= ($anio == '2023') ? 'selected' : '' ?>>2023</option>
                        <option value="2022" <?= ($anio == '2022') ? 'selected' : '' ?>>2022</option>
                        <option value="2021" <?= ($anio == '2021') ? 'selected' : '' ?>>2021</option>
                        <option value="2020" <?= ($anio == '2020') ? 'selected' : '' ?>>2020</option>
                        <option value="2019" <?= ($anio == '2019') ? 'selected' : '' ?>>2019</option>
                        <option value="2018" <?= ($anio == '2018') ? 'selected' : '' ?>>2018</option>
                    </select>
                </td>
            </table>

            <button type="submit" class="btn" style="background-color:#E8DDCA; color:#4d5645; width:100%;">Filtrar</button>
        </form>
    </div><br>
    <div><h3 style="text-align: center;">__________________________________________________________________________________________________________________________________________________________________________</h3></div>
    <div><h3 style="text-align: center;">FORMADO Y PLEGADO</h3></div>
<div  style="display: flex;">
    <div class="left" style="max-height: 800px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
        <table  class="table table-bordered" style="font-size: x-small; align-items: self-start; width:15%">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Máquina</th>
                    <th>Turno</th>
                    <th>Fecha</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $resultado = $conexion->query($sql);
                while ($row = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Hora']}</td>";
                    echo "<td>{$row['Máquina']}</td>";
                    echo "<td>{$row['Turno']}</td>";
                    echo "<td>{$row['Fecha Turno']}</td>";
                    echo "<td>{$row['total_cantidad']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="right">
             <?php $fechaSeleccionada = $anio."-".$mes."-".$dia; // Puedes cambiar esta fecha dinámicamente
         
         $sqlTotal = "SELECT SUM(`Cantidad`) as total_cantidad
         FROM `Reporte Prod`
         WHERE `Fecha Turno` = '$fechaSeleccionada'"; 
         
         $resultadoTotal = $conexion->query($sqlTotal);
         $totalDia = 0; // Variable para almacenar el total
         
         if ($rowTotal = $resultadoTotal->fetch_assoc()) {
             $totalDia = $rowTotal['total_cantidad'];
         }
         ?>
         
         <h4 style="text-align: end;">Total Producción para <?php echo $fechaSeleccionada; ?>:</h4> 
         <h1 style="text-align: end;"><b><?php echo number_format($totalDia, 0, ',', '.'); ?></b></h1>
         
        <!-- Contenedor para la leyenda personalizada -->
<div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 10px;">
    <div style="display: flex; align-items: center; gap: 5px;">
        <div style="width: 15px; height: 15px; background-color: rgba(152, 203, 237, 0.7);"></div>
        <span>Diurno</span>
    </div>
    <div style="display: flex; align-items: center; gap: 5px;">
        <div style="width: 15px; height: 15px; background-color: rgba(167, 117, 184, 0.7);"></div>
        <span>Nocturno</span>
    </div>
</div>
<canvas style="width: 80%;" id="myChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<!-- Lienzo para el gráfico -->
<canvas style="width: 1%;" id="myChart"></canvas>

<script>
    let chartData = <?php
    $resultado = $conexion->query($sql);
    $data = [];
    while ($row = $resultado->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
?>;

let labels = chartData.map(item => `${item['Máquina']} - ${item['Fecha Turno']}`);
let data = chartData.map(item => item['total_cantidad']);
let turnos = chartData.map(item => item['Turno']);

// Ordenar las etiquetas y los datos alfabéticamente
const sortedData = labels.map((label, index) => ({
    label: label,
    data: data[index],
    turno: turnos[index]
})).sort((a, b) => a.label.localeCompare(b.label));

// Reasignar los valores ordenados
labels = sortedData.map(item => item.label);
data = sortedData.map(item => item.data);
turnos = sortedData.map(item => item.turno);

/// Asignar colores basados en el turno
const backgroundColors = turnos.map(turno => {
    return turno === "Diurno" ? 'rgba(54, 162, 235, 0.2)' : 'rgba(52, 5, 68, 0.2)';
});
const borderColors = turnos.map(turno => {
    return turno === "Diurno" ? 'rgba(54, 162, 235, 1)' : 'rgba(52, 5, 68, 1)';
});

const ctx = document.getElementById('myChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: '',
            data: data,
            backgroundColor: backgroundColors,  // Asignar colores de fondo
            borderColor: borderColors,          // Asignar colores de borde
            borderWidth: 1,
            fill: true,
        }]
    }
});
</script>

<div><h3 style="text-align: center;">__________________________________________________________________________________________________________________________________________________________________________</h3></div>

<div><h3 style="text-align: center;">FLEXOGRAFIA</h3></div>

<?php 

$sql2 = "SELECT `Hora`, `Máquina`, `Fecha Turno`, `Turno`, SUM(`Cantidad`) as total_cantidad
FROM `Reporte Prod Flexo`
$filtro
GROUP BY `Máquina`, `Fecha Turno`, `Turno`
ORDER BY `Fecha Turno` DESC, `Hora` DESC";

// Segunda tabla y gráfico
?>

<!-- Segunda tabla -->
<div  style="display: flex; margin-top: 50px;">
    <div class="left" style="max-height: 800px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
        <table class="table table-bordered" style="font-size: x-small; align-items: self-start; width:15%">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Máquina</th>
                    <th>Turno</th>
                    <th>Fecha</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $resultado2 = $conexion->query($sql2);
                while ($row2 = $resultado2->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row2['Hora']}</td>";
                    echo "<td>{$row2['Máquina']}</td>";
                    echo "<td>{$row2['Turno']}</td>";
                    echo "<td>{$row2['Fecha Turno']}</td>";
                    echo "<td>{$row2['total_cantidad']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="right">
            <?php $fechaSeleccionada = $anio."-".$mes."-".$dia; // Puedes cambiar esta fecha dinámicamente

             $sqlTotal = "SELECT SUM(`Cantidad`) as total_cantidad
             FROM `Reporte Prod Flexo`
             WHERE `Fecha Turno` = '$fechaSeleccionada'"; 
             
             $resultadoTotal = $conexion->query($sqlTotal);
             $totalDia = 0; // Variable para almacenar el total
             
             if ($rowTotal = $resultadoTotal->fetch_assoc()) {
                 $totalDia = $rowTotal['total_cantidad'];
             }
            ?>

             <h4 style="text-align: end;">Total Producción para <?php echo $fechaSeleccionada; ?>:</h4> 
             <h1 style="text-align: end;"><b><?php echo number_format($totalDia, 0, ',', '.'); ?></b></h1>
    
        <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 5px;">
                <div style="width: 15px; height: 15px; background-color: rgba(224, 173, 213, 0.7);"></div>
                <span>Diurno</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <div style="width: 15px; height: 15px; background-color: rgba(124, 103, 176, 0.7);"></div>
                <span>Nocturno</span>
            </div>
        </div>
        <canvas style="width: 80%;" id="myChart2"></canvas>
    </div>
</div>

<!-- Script para el segundo gráfico -->
<script>
    let chartData2 = <?php
    $resultado2 = $conexion->query($sql2);
    $data2 = [];
    while ($row2 = $resultado2->fetch_assoc()) {
        $data2[] = $row2;
    }
    echo json_encode($data2);
?>;

let labels2 = chartData2.map(item => item['Máquina'] + ' - ' + item['Fecha Turno']);
let data2 = chartData2.map(item => item['total_cantidad']);
let turnos2 = chartData2.map(item => item['Turno']);

// Ordenar las etiquetas y los datos alfabéticamente
const sortedData2 = labels2.map((label, index) => ({
    label: label,
    data: data2[index],
    turno: turnos2[index]
})).sort((a, b) => a.label.localeCompare(b.label));

// Reasignar los valores ordenados
labels2 = sortedData2.map(item => item.label);
data2 = sortedData2.map(item => item.data);
turnos2 = sortedData2.map(item => item.turno);

// Asignar colores basados en el turno
const backgroundColors2 = turnos2.map(turno => {
    return turno === "Diurno" ? 'rgba(220, 54, 235, 0.2)' : 'rgba(30, 5, 68, 0.2)';
});
const borderColors2 = turnos2.map(turno => {
    return turno === "Diurno" ? 'rgb(235, 54, 151)' : 'rgb(68, 5, 11)';
});

const ctx2 = document.getElementById('myChart2').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: labels2,
        datasets: [{
            label: '',
            data: data2,
            backgroundColor: backgroundColors2,
            borderColor: borderColors2,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { 
                beginAtZero: true
            },
            y: { 
                beginAtZero: true
            }
        }
    }
});
</script>

<div><h3 style="text-align: center;">__________________________________________________________________________________________________________________________________________________________________________</h3></div>

<div><h3 style="text-align: center;">MAQUINAS BOLSAS</h3></div>

</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div>

<?php 
$maquinas = [
    'CURIONI 2', 'CURIONI 3', 'CURIONI 4', 'CURIONI 5', 'CURIONI 6', 
    'CURIONI 7', 'FANG BANG', 'J08', 'MAQ 550 3', 'RZFD-330',
    'RZFD-450', 'RZFD-450 2', 'WFD-430', 'ZNEP-450'
];

$maquinas2 = ['FLEXO BICOLOR', 'FLEXO MAF ANTARES', 'FLEXO 6', 'FLEXO 7', 'FLEXO MAF','FLEXO WX4 #1','FLEXO WX4 #2'];
?>

<!-- Primera tabla -->
<table>
    <?php for ($i = 0; $i < count($maquinas); $i += 2) { ?>
        <tr>
            <?php for ($j = 0; $j < 2; $j++) { 
                if ($i + $j < count($maquinas)) {
                    $id = $i + $j + 1;
                    $maquina = $maquinas[$i + $j];
            ?>
            <td style="width: 50%;">
                <?php 
                $sql_barras = "SELECT DATE(`Fecha Turno`) as fecha, `Turno`, SUM(`Cantidad`) as total_cantidad FROM `Reporte Prod`
                               WHERE `Máquina` = '$maquina' AND `Fecha Turno` >= CURDATE() - INTERVAL 15 DAY 
                               GROUP BY DATE(`Fecha Turno`), `Turno` ORDER BY `Fecha Turno` DESC";
                $resultado_barras = $conexion->query($sql_barras);
                
                $fechas = [];
                $cantidades_diurno = [];
                $cantidades_nocturno = [];
                
                while ($row = $resultado_barras->fetch_assoc()) {
                    $fecha = $row['fecha'];
                    if (!in_array($fecha, $fechas)) {
                        $fechas[] = $fecha;
                        $cantidades_diurno[] = 0;
                        $cantidades_nocturno[] = 0;
                    }
                    
                    $index = array_search($fecha, $fechas);
                    if ($row['Turno'] == 'Diurno') {
                        $cantidades_diurno[$index] = $row['total_cantidad'];
                    } else {
                        $cantidades_nocturno[$index] = $row['total_cantidad'];
                    }
                }
                ?>
                <div class="right">
                    <h3><?php echo $maquina; ?></h3>
                    <canvas id="myCharti<?php echo $id; ?>"></canvas>
                </div>

                <script>
                    var ctxi<?php echo $id; ?> = document.getElementById('myCharti<?php echo $id; ?>').getContext('2d');
                    var myChart<?php echo $id; ?> = new Chart(ctxi<?php echo $id; ?>, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode(array_reverse($fechas)); ?>,
                            datasets: [
                                {
                                    label: 'Diurno',
                                    data: <?php echo json_encode(array_reverse($cantidades_diurno)); ?>,
                                    backgroundColor: 'rgba(185, 255, 252, 0.45)',
                                    borderColor: 'rgb(0, 120, 100)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Nocturno',
                                    data: <?php echo json_encode(array_reverse($cantidades_nocturno)); ?>,
                                    backgroundColor: 'rgba(145, 172, 255, 0.47)',
                                    borderColor: 'rgb(9, 1, 123)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </td>
            <?php } } ?>
        </tr>
    <?php } ?>
</table>
<div><h3 style="text-align: center;">__________________________________________________________________________________________________________________________________________________________________________</h3></div>

<div><h3 style="text-align: center;">MAQUINAS FLEXO</h3></div>

<!-- Segunda tabla -->
<table>
    <?php for ($i = 0; $i < count($maquinas2); $i += 2) { ?>
        <tr>
            <?php for ($j = 0; $j < 2; $j++) { 
                if ($i + $j < count($maquinas2)) {
                    $id = $i + $j + 1;
                    $maquina2 = $maquinas2[$i + $j];
            ?>
            <td style="width: 50%;">
                <?php 
                $sql_barras = "SELECT DATE(`Fecha Turno`) as fecha, `Turno`, SUM(`Cantidad`) as total_cantidad FROM `Reporte Prod Flexo`
                               WHERE `Máquina` = '$maquina2' AND `Fecha Turno` >= CURDATE() - INTERVAL 15 DAY 
                               GROUP BY DATE(`Fecha Turno`), `Turno` ORDER BY `Fecha Turno` DESC";
                $resultado_barras = $conexion->query($sql_barras);
                
                $fechas = [];
                $cantidades_diurno = [];
                $cantidades_nocturno = [];
                
                while ($row = $resultado_barras->fetch_assoc()) {
                    $fecha = $row['fecha'];
                    if (!in_array($fecha, $fechas)) {
                        $fechas[] = $fecha;
                        $cantidades_diurno[] = 0;
                        $cantidades_nocturno[] = 0;
                    }
                    
                    $index = array_search($fecha, $fechas);
                    if ($row['Turno'] == 'Diurno') {
                        $cantidades_diurno[$index] = $row['total_cantidad'];
                    } else {
                        $cantidades_nocturno[$index] = $row['total_cantidad'];
                    }
                }
                ?>
                <div class="right">
                    <h3><?php echo $maquina2; ?></h3>
                    <canvas id="myChartf<?php echo $id; ?>"></canvas>
                </div>

                <script>
                    var ctxf<?php echo $id; ?> = document.getElementById('myChartf<?php echo $id; ?>').getContext('2d');
                    var myChart<?php echo $id; ?> = new Chart(ctxf<?php echo $id; ?>, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode(array_reverse($fechas)); ?>,
                            datasets: [
                                {
                                    label: 'Diurno',
                                    data: <?php echo json_encode(array_reverse($cantidades_diurno)); ?>,
                                    backgroundColor: 'rgba(240, 170, 230, 0.6)',
                                    borderColor: 'rgb(123, 38, 188)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Nocturno',
                                    data: <?php echo json_encode(array_reverse($cantidades_nocturno)); ?>,
                                    backgroundColor: 'rgba(162, 151, 185, 0.6)',
                                    borderColor: 'rgb(21, 4, 98)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </td>
            <?php } } ?>
        </tr>
    <?php } ?>
</table>

</div>

    


<meta http-equiv="refresh" content="300">

    



</body>

</html>
