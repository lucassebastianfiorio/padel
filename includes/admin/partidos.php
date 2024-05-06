<?php

// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Consultar la base de datos para obtener los torneos
global $wpdb;
$table_torneos = $wpdb->prefix . 'pa_torneos';
$table_partidos = $wpdb->prefix . 'pa_partidos';

$torneos = $wpdb->get_results(
    "SELECT * FROM $table_torneos",
    ARRAY_A
);

// Obtener el ID del torneo seleccionado (si hay alguno)
$torneo_id = isset($_GET['torneo_id']) ? intval($_GET['torneo_id']) : null;

// Consultar la base de datos para obtener los partidos del torneo seleccionado
$partidos = array();
if ($torneo_id !== null) {
    $partidos = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_partidos WHERE torneo_id = %d",
            $torneo_id
        ),
        ARRAY_A
    );
}

?>
<div class="wrap">
    <h1>Partidos del Torneo</h1>

    <!-- Selector de torneos -->
    <label for="torneo_select">Selecciona un Torneo:</label>
    <select name="torneo_id" id="torneo_select">
        <option value="">Selecciona un Torneo</option>
        <?php foreach ($torneos as $torneo) : ?>
            <option value="<?php echo $torneo['id']; ?>" <?php selected($torneo_id, $torneo['id']); ?>>
                <?php echo $torneo['nombre']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Mostrar los partidos del torneo seleccionado -->
    <div id="partidos_container">
        
    </div>
</div>

<script>
    // Capturar el cambio en la selección de torneos
    document.getElementById('torneo_select').addEventListener('change', function() {
        var torneoId = this.value; // Obtener el ID del torneo seleccionado
        if (torneoId) {
            // Realizar una solicitud AJAX para obtener los partidos del torneo seleccionado
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=get_torneo_partidos&torneo_id=' + torneoId);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Insertar los partidos devueltos por la solicitud AJAX en el contenedor de partidos
                        document.getElementById('partidos_container').innerHTML = response.data;
                    } else {
                        // Mostrar mensaje de error si no se encontraron partidos
                        document.getElementById('partidos_container').innerHTML = '<p>No se encontraron partidos para este torneo.</p>';
                    }
                }
            };
            xhr.send();
        } else {
            // Limpiar el contenedor de partidos si no se ha seleccionado ningún torneo
            document.getElementById('partidos_container').innerHTML = '';
        }
    });
</script>

<script>
    // Delegar el evento de clic a un elemento que ya está presente en la página
    jQuery(document).on('click', '.assign-winner', function() {
        // Obtener el ID del partido desde el atributo de datos
        var partidoId = jQuery(this).data('partido-id');

        // Mostrar el SweetAlert2
        Swal.fire({
            title: 'Asignar Ganador',
            text: 'Selecciona el ganador para este partido:',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Pareja Local',
            cancelButtonText: 'Pareja Visitante'
        }).then((result) => {
            // Verificar la acción del usuario
            if (result.isConfirmed) {
                // El usuario seleccionó "Pareja Local"
                asignarGanador(partidoId, 'local');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // El usuario seleccionó "Pareja Visitante"
                asignarGanador(partidoId, 'visitante');
            }
        });
    });

    // Función para asignar el ganador mediante AJAX
    function asignarGanador(partidoId, ganador) {
        // Realizar la solicitud AJAX para guardar el ganador
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            method: 'POST',
            data: {
                action: 'asignar_ganador_partido',
                partido_id: partidoId,
                ganador: ganador // Aquí se pasa el ID de la pareja como ganador
            },
            success: function(response) {
                // Manejar la respuesta del servidor si es necesario
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX si es necesario
                console.error(xhr.responseText);
            }
        });
    }
</script>


