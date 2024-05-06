<?php
// Obtenemos la ruta del archivo wp-config.php
$wp_config_path = '';
$current_directory = dirname(__FILE__);
while (!file_exists($wp_config_path . 'wp-config.php') && strlen($current_directory) > 1) {
    $wp_config_path = dirname($current_directory) . '/';
    $current_directory = dirname($current_directory);
}

// Si no se encuentra wp-config.php, terminamos la ejecución
if (!file_exists($wp_config_path . 'wp-config.php')) {
    die('No se puede encontrar wp-config.php');
}

// Cargamos WordPress
require_once($wp_config_path . 'wp-load.php');

// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Consultar la base de datos para obtener la información de los torneos
global $wpdb;
$table_torneos = $wpdb->prefix . 'pa_torneos';
$torneos = $wpdb->get_results("SELECT * FROM $table_torneos", ARRAY_A);

?>

<div class="wrap">
    <h1>Torneos</h1>

    <!-- Tabla de torneos -->
    <table id="torneos-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Categoría</th>
                <th>Mixto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($torneos as $torneo) : ?>
                <tr>
                    <td><?php echo $torneo['id']; ?></td>
                    <td><?php echo $torneo['nombre']; ?></td>
                    <td><?php echo $torneo['fecha_inicio']; ?></td>
                    <td><?php echo $torneo['fecha_fin']; ?></td>
                    <td>
                        <?php
                        // Obtener categorías correspondientes
                        $categorias = [];
                        if (!empty($torneo['categoria_damas'])) {
                            $categorias[] = $torneo['categoria_damas'];
                        }
                        if (!empty($torneo['categoria_caballeros'])) {
                            $categorias[] = $torneo['categoria_caballeros'];
                        }
                        echo implode(', ', $categorias);
                        ?>
                    </td>
                    <td><?php echo $torneo['mixto'] == 1 ? 'Sí' : 'No'; ?></td>
                    
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=ver_inscriptos&torneo_id=' . $torneo['id']); ?>">Ver Inscritos</a>
                        <a href="<?php echo admin_url('admin.php?page=generar_partidos&torneo_id=' . $torneo['id']); ?>">Generar Partidos</a>
                    </td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



<!-- Agregar jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.js"></script>

<!-- Configuración de DataTables -->
<script>
    jQuery(document).ready(function($) {
        $('#torneos-table').DataTable();
    });
</script>
