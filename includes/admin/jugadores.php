<?php
// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Consultar la base de datos para obtener la información de los jugadores
global $wpdb;
$table_jugadores = $wpdb->prefix . 'pa_jugadores';
$table_categorias = $wpdb->prefix . 'pa_categorias';
$jugadores = $wpdb->get_results("SELECT j.*, c.nombre as categoria_nombre FROM $table_jugadores j LEFT JOIN $table_categorias c ON j.categoria_id = c.id", ARRAY_A);
// Verificar si hay un parámetro de éxito en la URL
$success = isset($_GET['success']) && $_GET['success'] === 'true';
?>

<div class="wrap">
    <h1>Jugadores</h1>
    <?php if ($success) : ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        // Mostrar mensaje de confirmación con SweetAlert
        Swal.fire({
            title: 'Éxito',
            text: 'El nuevo jugador se ha agregado correctamente.',
            icon: 'success'
        }).then(() => {
            // Redirigir a la página de gestión de jugadores
            window.location.href = "<?php echo admin_url('admin.php?page=pa_manage_players'); ?>";
        });
    </script>
<?php endif; ?>

    <a href="<?php echo admin_url('admin.php?page=nuevo_jugador'); ?>" class="button">Nuevo Jugador</a>
    <!-- Tabla de jugadores -->
    <table id="jugadores-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>

                <th>Nombre</th>
                <th>Apellido</th>
                <th>DNI</th>

                <th>Categoría</th>

                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jugadores as $jugador) : ?>
                <tr>

                    <td><?php echo $jugador['nombre']; ?></td>
                    <td><?php echo $jugador['apellido']; ?></td>
                    <td><?php echo $jugador['dni']; ?></td>

                    <td><?php echo isset($jugador['categoria_nombre']) ? $jugador['categoria_nombre'] : ''; ?></td>

                    <td><?php echo $jugador['estado']; ?></td>
                    <td>
                        <button class="change-status-btn" data-jugador-id="<?php echo $jugador['id']; ?>" data-current-status="<?php echo $jugador['estado']; ?>" title="Cambiar Estado"><i class="fas fa-exchange-alt"></i></button>
                        <button class="delete-player-btn" data-jugador-id="<?php echo $jugador['id']; ?>" title="Eliminar jugador"><i class="fas fa-trash-alt"></i></button>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=ver_detalles&jugador_id=' . $jugador['id']); ?>"><i class="fas fa-eye"></i></a>
                    </td>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function($) {
        // Capturar clics en el botón de cambio de estado
        $('.change-status-btn').on('click', function() {
            var jugadorId = $(this).data('jugador-id');
            var currentStatus = $(this).data('current-status');

            // Mostrar mensaje de confirmación con SweetAlert
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres cambiar el estado del jugador?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
            }).then((result) => {
                // Si el usuario confirma, realizar la acción
                if (result.isConfirmed) {
                    // Realizar la solicitud AJAX para cambiar el estado
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'toggle_status',
                            jugador_id: jugadorId,
                            current_status: currentStatus,
                            nonce: '<?php echo wp_create_nonce('toggle_status_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Recargar la página después de cambiar el estado
                                location.reload();
                            } else {
                                // Mostrar un mensaje de error si falla la solicitud
                                Swal.fire('Error', 'Hubo un error al cambiar el estado del jugador.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire('Error', 'Hubo un error al procesar la solicitud.', 'error');
                        }
                    });
                }
            });
        });

        // Capturar clics en el botón de eliminación
        $('.delete-player-btn').on('click', function() {
            var jugadorId = $(this).data('jugador-id');

            // Mostrar mensaje de confirmación con SweetAlert
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres eliminar este jugador?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
            }).then((result) => {
                // Si el usuario confirma, realizar la acción
                if (result.isConfirmed) {
                    // Realizar la solicitud AJAX para eliminar el jugador
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'delete_player',
                            jugador_id: jugadorId,
                            nonce: '<?php echo wp_create_nonce('delete_player_nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Recargar la página después de eliminar el jugador
                                location.reload();
                            } else {
                                // Mostrar un mensaje de error si falla la solicitud
                                Swal.fire('Error', 'Hubo un error al eliminar el jugador.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire('Error', 'Hubo un error al procesar la solicitud.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>