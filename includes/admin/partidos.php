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
<button id="generar-segunda-ronda" style="display: none;" class="button">Generar Segunda Ronda</button>

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
                    verificarGenerarSegundaRonda(torneoId);
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

// Delegar el evento de clic a un elemento que ya está presente en la página
jQuery(document).on('click', '.assign-winner', function() {
    // Obtener el ID del torneo desde el elemento select
    var torneoId = jQuery('#torneo_select').val();
    // Obtener el ID del partido desde el atributo de datos
    var partidoId = jQuery(this).data('partido-id');
    // Obtener el ID de la pareja
    var parejaId = jQuery(this).data('pareja-id');

    // Mostrar el SweetAlert2 para confirmar la asignación del ganador
    Swal.fire({
        title: 'Asignar Ganador',
        text: '¿Está seguro de asignar el ganador para este partido?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        // Verificar la acción del usuario
        if (result.isConfirmed) {
            // El usuario confirmó la asignación, llamar a la función para asignar el ganador
            asignarGanador(torneoId, partidoId, parejaId);
        }
    });
});

// Función para asignar el ganador mediante AJAX
function asignarGanador(torneoId, partidoId, parejaId) {
    // Realizar la solicitud AJAX para guardar el ganador
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        method: 'POST',
        data: {
            action: 'asignar_ganador_partido',
            torneo_id: torneoId,
            partido_id: partidoId,
            ganador_id: parejaId
        },
        success: function(response) {
            // Mostrar un mensaje de éxito o error según la respuesta del servidor
            if (response.success) {
                Swal.fire('Éxito', response.data, 'success');
                // Cambiar el botón de la pareja ganadora por un icono adecuado
                var ganadorButton = jQuery('.assign-winner[data-pareja-id="' + parejaId + '"]');
                ganadorButton.html('<i class="fas fa-trophy"></i>'); // Reemplazar con el icono deseado
                ganadorButton.removeClass('assign-winner'); // Eliminar la clase 'assign-winner' para que no se pueda hacer clic nuevamente
                
                // Actualizar la tabla de partidos después de asignar el ganador
                actualizarTablaPartidos(torneoId);
                verificarGenerarSegundaRonda(torneoId);
            } else {
                Swal.fire('Error', response.data, 'error');
            }
        },
        error: function(xhr, status, error) {
            // Mostrar mensaje de error en la consola del navegador
            console.error(xhr.responseText);
        }
    });
}

function actualizarTablaPartidos(torneoId) {
    // Realizar la solicitud AJAX para obtener la tabla de partidos actualizada
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        method: 'GET',
        data: {
            action: 'get_torneo_partidos',
            torneo_id: torneoId
        },
        success: function(response) {
            // Reemplazar la tabla de partidos con la versión actualizada
            if (response.success) {
                jQuery('#partidos_container').html(response.data);
                verificarGenerarSegundaRonda(torneoId);
            } else {
                console.error('Error al actualizar la tabla de partidos');
            }
        },
        error: function(xhr, status, error) {
            // Mostrar mensaje de error en la consola del navegador
            console.error('Error al actualizar la tabla de partidos:', error);
        }
    });
}

function verificarGenerarSegundaRonda(torneoId) {
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        method: 'GET',
        data: {
            action: 'verificar_generar_segunda_ronda',
            torneo_id: torneoId
        },
        success: function(response) {
            if (response.success) {
                var mostrarBoton = response.data; // true o false
                if (mostrarBoton) {
                    jQuery('#generar-segunda-ronda').show();
                } else {
                    jQuery('#generar-segunda-ronda').hide();
                }
            } else {
                console.error('Error al verificar la generación de la segunda ronda');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al verificar la generación de la segunda ronda:', error);
        }
    });
}


</script>






