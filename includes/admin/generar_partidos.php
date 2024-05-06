<?php
// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}
$success = isset($_GET['success']) && $_GET['success'] === 'true';

// Obtener el ID del torneo desde la URL
$torneo_id = isset($_GET['torneo_id']) ? intval($_GET['torneo_id']) : 0;

// Consultar la base de datos para obtener las parejas inscritas en el torneo
global $wpdb;
$table_inscritos = $wpdb->prefix . 'pa_inscritos';
$table_parejas = $wpdb->prefix . 'pa_parejas';
$table_jugadores = $wpdb->prefix . 'pa_jugadores';

$parejas_inscritas = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.id AS pareja_id, j1.nombre AS jugador1_nombre, j1.apellido AS jugador1_apellido, j2.nombre AS jugador2_nombre, j2.apellido AS jugador2_apellido
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

<?php if ($success) : ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        // Mostrar mensaje de confirmación con SweetAlert
        Swal.fire({
            title: 'Completado',
            text: 'Se han generado los partidos para el torneo',
            icon: 'success'
        }).then(() => {
            // Redirigir a la página de generar_partidos con el ID del torneo
            window.location.href = '<?php echo admin_url("admin.php?page=generar_partidos&torneo_id=" . $torneo_id); ?>';
        });
    </script>
<?php endif; ?>


<div class="wrap">
    <h1>Generar Partidos</h1>

    <!-- Tabla de parejas inscritas -->
    <?php if ($parejas_inscritas) : ?>
        <h2>Parejas Inscritas en el Torneo</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Pareja ID</th>
                    <th>Jugador 1</th>
                    <th>Jugador 2</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parejas_inscritas as $pareja) : ?>
                    <tr>
                        <td><?php echo $pareja['pareja_id']; ?></td>
                        <td><?php echo $pareja['jugador1_nombre'] . ' ' . $pareja['jugador1_apellido']; ?></td>
                        <td><?php echo $pareja['jugador2_nombre'] . ' ' . $pareja['jugador2_apellido']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No hay parejas inscritas en este torneo.</p>
    <?php endif; ?>

    <!-- Formulario para generar partidos -->
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="generate_matches">
        <input type="hidden" name="torneo_id" value="<?php echo $torneo_id; ?>">
        <button type="submit" name="generar_partidos" class="button" id="generar_partidos_btn">Generar Partidos</button>
    </form>
</div>


