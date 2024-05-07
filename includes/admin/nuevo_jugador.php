<?php
// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}



// Consultar la base de datos para obtener las categorías disponibles
global $wpdb;
$table_categorias = $wpdb->prefix . 'pa_categorias';
$categorias = $wpdb->get_results("SELECT * FROM $table_categorias", ARRAY_A);
?>

<div class="wrap">
    <h1>Agregar Nuevo Jugador</h1>



    <form id="jugador-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="add_new_player">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>

        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required>

        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" required>
            <option value="">Seleccione una categoría</option>
            <?php foreach ($categorias as $categoria) : ?>
                <option value="<?php echo esc_attr($categoria['id']); ?>"><?php echo esc_html($categoria['nombre']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="partidos_jugados">Partidos Jugados:</label>
        <input type="number" id="partidos_jugados" name="partidos_jugados" min="0" value="0" required>

        <label for="partidos_ganados">Partidos Ganados:</label>
        <input type="number" id="partidos_ganados" name="partidos_ganados" min="0" value="0" required>

        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="Confirmado">Confirmado</option>
            <option value="Pendiente">Pendiente</option>
        </select>

        <button type="submit" class="button">Agregar Jugador</button>
    </form>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar evento al formulario de agregar jugador
    document.getElementById('jugador-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Evitar el envío del formulario por defecto

        // Obtener los datos del formulario
        var formData = new FormData(this);

        // Realizar una solicitud AJAX para enviar los datos del formulario
        fetch('<?php echo admin_url('admin-post.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Verificar si la solicitud fue exitosa
            if (data.success) {
                // Mostrar mensaje de éxito con SweetAlert
                Swal.fire({
                    title: 'Éxito',
                    text: data.data,
                    icon: 'success'
                }).then(() => {
                    // Redireccionar a la página de gestión de jugadores
                    window.location.href = '<?php echo admin_url('admin.php?page=pa_manage_players'); ?>';
                });
            } else {
                // Verificar si se trata de un error de DNI duplicado
                if (data.data.message === 'Ya existe un jugador con el mismo número de DNI.') {
                    // Mostrar mensaje de error con botón adicional para ver el jugador existente
                    Swal.fire({
                        title: 'Error',
                        text: data.data.message,
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: 'Ver Jugador',
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redireccionar a la página de detalles del jugador existente
                            window.location.href = '<?php echo admin_url('admin.php?page=ver_detalles&jugador_id='); ?>' + data.data.existing_player.id;
                        }
                    });
                } else {
                    // Mostrar mensaje de error estándar con SweetAlert
                    Swal.fire({
                        title: 'Error',
                        text: data.data.join('\n'), // Unir los mensajes de error en un solo texto
                        icon: 'error'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});


</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar evento al botón de agregar jugador
        document.getElementById('jugador-form').addEventListener('submit', function(e) {
            // Validar campos antes de enviar el formulario
            var nombre = document.getElementById('nombre').value.trim();
            var apellido = document.getElementById('apellido').value.trim();
            var dni = document.getElementById('dni').value.trim();
            var email = document.getElementById('email').value.trim();
            var telefono = document.getElementById('telefono').value.trim();
            var categoria = document.getElementById('categoria').value.trim();
            var partidosJugados = document.getElementById('partidos_jugados').value.trim();
            var partidosGanados = document.getElementById('partidos_ganados').value.trim();
            var estado = document.getElementById('estado').value.trim();

            // Validar campos
            if (nombre === '' || apellido === '' || dni === '' || email === '' || telefono === '' || categoria === '' || partidosJugados === '' || partidosGanados === '' || estado === '') {
                e.preventDefault();
                alert('Por favor, complete todos los campos.');
            }
        });
    });
</script>



