<?php
// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Obtener el ID del jugador desde la URL
$jugador_id = isset($_GET['jugador_id']) ? intval($_GET['jugador_id']) : 0;

// Consultar la base de datos para obtener la información del jugador
global $wpdb;
$table_jugadores = $wpdb->prefix . 'pa_jugadores';
$jugador = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_jugadores WHERE id = %d", $jugador_id), ARRAY_A);

// Verificar si hay un parámetro de éxito en la URL
$success = isset($_GET['success']) && $_GET['success'] === 'true';

// Consultar la base de datos para obtener las categorías disponibles
$table_categorias = $wpdb->prefix . 'pa_categorias';
$categorias = $wpdb->get_results("SELECT * FROM $table_categorias", ARRAY_A);
?>

<div class="wrap">
    <h1>Detalles del Jugador</h1>

    <?php if ($success) : ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            // Mostrar mensaje de confirmación con SweetAlert
            Swal.fire({
                title: 'Éxito',
                text: 'Los detalles del jugador se han guardado correctamente.',
                icon: 'success'
            }).then(() => {
                // Obtener la URL base sin parámetros
                var baseUrl = window.location.href.split('?')[0];
                // Concatenar el ID del jugador a la URL base
                var playerId = <?php echo $jugador_id; ?>;
                var finalUrl = baseUrl + '?page=ver_detalles&jugador_id=' + playerId;
                // Reemplazar la URL actual
                window.history.replaceState({}, document.title, finalUrl);
            });
        </script>
    <?php endif; ?>

    <form id="jugador-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="save_player_details">
        <input type="hidden" name="jugador_id" value="<?php echo $jugador_id; ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo esc_attr($jugador['nombre']); ?>" disabled>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo esc_attr($jugador['apellido']); ?>" disabled>

        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" value="<?php echo esc_attr($jugador['dni']); ?>" disabled>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo esc_attr($jugador['email']); ?>" disabled>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo esc_attr($jugador['telefono']); ?>" disabled>

        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" disabled>
            <?php foreach ($categorias as $categoria) : ?>
                <option value="<?php echo esc_attr($categoria['id']); ?>" <?php selected($categoria['id'], $jugador['categoria_id']); ?>><?php echo esc_html($categoria['nombre']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="partidos_jugados">Partidos Jugados:</label>
        <input type="number" id="partidos_jugados" name="partidos_jugados" value="<?php echo esc_attr($jugador['partidos_jugados']); ?>" disabled>

        <label for="partidos_ganados">Partidos Ganados:</label>
        <input type="number" id="partidos_ganados" name="partidos_ganados" value="<?php echo esc_attr($jugador['partidos_ganados']); ?>" disabled>

        <label for="estado">Estado:</label>
        <select id="estado" name="estado" <?php echo $success ? '' : 'disabled'; ?>>
            <option value="Confirmado" <?php selected($jugador['estado'], 'Confirmado'); ?>>Confirmado</option>
            <option value="Pendiente" <?php selected($jugador['estado'], 'Pendiente'); ?>>Pendiente</option>
        </select>

        <a id="edit-btn" class="button">Editar Detalles</a>
        <button type="submit" id="save-btn" style="display: none;" class="button">Guardar Cambios</button>

        <!-- Botón para cancelar -->
        <a href="<?php echo admin_url('admin.php?page=pa_manage_players'); ?>" class="button">Volver al listado</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para habilitar los campos de edición
        function enableFields() {
            document.querySelectorAll('#jugador-form input, #jugador-form select').forEach(function(input) {
                input.removeAttribute('disabled');
            });
            document.getElementById('edit-btn').style.display = 'none';
            document.getElementById('save-btn').style.display = 'inline-block';
        }

        // Habilitar campos al hacer clic en el botón de editar
        document.getElementById('edit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            enableFields();
            // Mostrar el botón de cancelar
            document.querySelector('.cancel-btn').style.display = 'inline-block';
        });
    });
</script>
