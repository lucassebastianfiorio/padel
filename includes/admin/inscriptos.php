<?php
// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Consultar la base de datos para obtener el ID del torneo
$torneo_id = isset($_GET['torneo_id']) ? intval($_GET['torneo_id']) : 0;

// Consultar la base de datos para obtener la lista de inscritos al torneo
global $wpdb;
$table_inscritos = $wpdb->prefix . 'pa_inscritos';
$table_parejas = $wpdb->prefix . 'pa_parejas';
$table_jugadores = $wpdb->prefix . 'pa_jugadores';

$inscritos = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT j1.id AS jugador1_id, j1.nombre AS nombre1, j1.apellido AS apellido1, j2.id AS jugador2_id, j2.nombre AS nombre2, j2.apellido AS apellido2
        FROM $table_inscritos i
        INNER JOIN $table_parejas p ON i.pareja_id = p.id
        INNER JOIN $table_jugadores j1 ON p.jugador1_id = j1.id
        INNER JOIN $table_jugadores j2 ON p.jugador2_id = j2.id
        WHERE i.torneo_id = %d",
        $torneo_id
    ),
    ARRAY_A
);
?>

<div class="wrap">
    <h1>Inscritos al Torneo</h1>

    <?php if ($inscritos) : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Jugador 1</th>
                    <th>Detalles</th>
                    <th>Jugador 2</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscritos as $inscrito) : ?>
                    <tr>
                        <td><?php echo $inscrito['nombre1'] . ' ' . $inscrito['apellido1']; ?></td>
                        <td><a href="<?php echo admin_url('admin.php?page=ver_detalles&jugador_id=' . $inscrito['jugador1_id']); ?>"><i class="fas fa-eye"></i> Ver Detalles</a></td>
                        <td><?php echo $inscrito['nombre2'] . ' ' . $inscrito['apellido2']; ?></td>
                        <td><a href="<?php echo admin_url('admin.php?page=ver_detalles&jugador_id=' . $inscrito['jugador2_id']); ?>"><i class="fas fa-eye"></i> Ver Detalles</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No hay inscritos en este torneo.</p>
    <?php endif; ?>
</div>
