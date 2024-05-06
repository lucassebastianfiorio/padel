<?php

/**
 * Plugin Name: Padel Admin
 * Description: Plugin para la gestión de torneos de pádel.
 * Version: 2.1
 * Author: Lucas S. Fiorio
 * Author URI: https://codesign.ar
 * Text Domain: padel-admin
 */

// Si este archivo es llamado directamente, aborta la ejecución.
if (!defined('WPINC')) {
    die;
}

// Define la ruta del directorio principal del plugin.
define('PADEL_ADMIN_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Activar el plugin: crear tablas en la base de datos
function pa_activate_plugin()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tabla de categorias
    $table_categorias = $wpdb->prefix . 'pa_categorias';
    $sql_categorias = "CREATE TABLE IF NOT EXISTS $table_categorias (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(50) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_categorias);

    // Tabla de jugadores
    $table_jugadores = $wpdb->prefix . 'pa_jugadores';
    $sql_jugadores = "CREATE TABLE IF NOT EXISTS $table_jugadores (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(50) NOT NULL,
        apellido varchar(50) NOT NULL,
        dni varchar(20) NOT NULL,
        email varchar(100) NOT NULL,
        telefono varchar(20) NOT NULL,
        categoria_id mediumint(9) NOT NULL,
        partidos_jugados int(11) NOT NULL DEFAULT 0,
        partidos_ganados int(11) NOT NULL DEFAULT 0,
        foto_perfil varchar(255),
        estado varchar(20) NOT NULL DEFAULT 'Pendiente',
        PRIMARY KEY  (id),
        UNIQUE KEY dni_unique (dni),
        FOREIGN KEY (categoria_id) REFERENCES $table_categorias(id)
    ) $charset_collate;";
    dbDelta($sql_jugadores);

    // Tabla de torneos
    $table_torneos = $wpdb->prefix . 'pa_torneos';
    $sql_torneos = "CREATE TABLE IF NOT EXISTS $table_torneos (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(100) NOT NULL,
        fecha_inicio date NOT NULL,
        fecha_fin date NOT NULL,
        categoria_damas varchar(50),
        categoria_caballeros varchar(50),
        mixto tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    dbDelta($sql_torneos);

    // Tabla de parejas de jugadores
    $table_parejas = $wpdb->prefix . 'pa_parejas';
    $sql_parejas = "CREATE TABLE IF NOT EXISTS $table_parejas (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        jugador1_id mediumint(9) NOT NULL,
        jugador2_id mediumint(9) NOT NULL,
        torneo_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY (jugador1_id) REFERENCES $table_jugadores(id),
        FOREIGN KEY (jugador2_id) REFERENCES $table_jugadores(id),
        FOREIGN KEY (torneo_id) REFERENCES $table_torneos(id)
    ) $charset_collate;";
    dbDelta($sql_parejas);

    // Tabla de inscritos a torneos
    $table_inscritos = $wpdb->prefix . 'pa_inscritos';
    $sql_inscritos = "CREATE TABLE IF NOT EXISTS $table_inscritos (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    torneo_id mediumint(9) NOT NULL,
    pareja_id mediumint(9) NOT NULL,
    PRIMARY KEY  (id),
    FOREIGN KEY (torneo_id) REFERENCES $table_torneos(id),
    FOREIGN KEY (pareja_id) REFERENCES $table_parejas(id)
) $charset_collate;";
    dbDelta($sql_inscritos);

    // Tabla de partidos
$table_partidos = $wpdb->prefix . 'pa_partidos';
$sql_partidos = "CREATE TABLE IF NOT EXISTS $table_partidos (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    torneo_id mediumint(9) NOT NULL,
    ronda int NOT NULL,
    pareja_local_id mediumint(9) NOT NULL,
    pareja_visitante_id mediumint(9) NOT NULL,
    ganador_id mediumint(9),
    resultado VARCHAR(255),
    PRIMARY KEY  (id),
    FOREIGN KEY (torneo_id) REFERENCES $table_torneos(id),
    FOREIGN KEY (pareja_local_id) REFERENCES $table_inscritos(pareja_id),
    FOREIGN KEY (pareja_visitante_id) REFERENCES $table_inscritos(pareja_id),
    FOREIGN KEY (ganador_id) REFERENCES $table_inscritos(pareja_id)
) $charset_collate;";
dbDelta($sql_partidos);



    // Otras tablas (organizadores, resultados, etc.) podrían agregarse aquí
}

// Registrar la función de activación
register_activation_hook(__FILE__, 'pa_activate_plugin');

// Función para incluir archivos CSS y JS
function pa_enqueue_scripts()
{
    // Agregar jQuery
    wp_enqueue_script('jquery');

    // Agregar DataTables
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css');
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.6/js/jquery.dataTables.js', array('jquery'), null, true);

    // Agregar FontAwesome
    wp_enqueue_style('fontawesome-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

    // Agregar SweetAlert
    wp_enqueue_script('sweetalert2-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@10', array(), false, true);
}
add_action('admin_enqueue_scripts', 'pa_enqueue_scripts');


// Agregar menús del plugin
function pa_add_admin_menus()
{
    // Página principal del plugin
    add_menu_page(
        'Padel Admin',
        'Padel Admin',
        'manage_options',
        'pa_main_menu',
        'pa_main_menu_callback',
        'dashicons-admin-generic',
        20
    );

    // Submenú Jugadores
    add_submenu_page(
        'pa_main_menu',
        'Jugadores',
        'Jugadores',
        'manage_options',
        'pa_manage_players',
        'pa_manage_players_callback'
    );
    add_submenu_page(
        'pa_main_menu',
        'Categorias',
        'Categorias',
        'manage_options',
        'pa_manage_categorias',
        'pa_manage_categorias_callback'
    );

    // Submenú Torneos
    add_submenu_page(
        'pa_main_menu',
        'Torneos',
        'Torneos',
        'manage_options',
        'pa_manage_torneos',
        'pa_manage_torneos_callback'
    );


    add_submenu_page(
        'pa_main_menu', // Slug del menú padre
        'Partidos',      // Título de la página
        'Partidos',      // Título en el menú
        'manage_options',      // Capacidad requerida para ver la página
        'ver_partidos',   // Slug de la página
        'mostrar_pagina_ver_partidos' // Función para mostrar el contenido de la página
    );

    // Otros submenús aquí
}
add_action('admin_menu', 'pa_add_admin_menus');

// Callback para la página principal del plugin
function pa_main_menu_callback()
{
    // Contenido de la página principal
}

// Callback para la subpágina de administrar jugadores
function pa_manage_players_callback()
{
    // Cargar el archivo jugadores.php
    require_once(PADEL_ADMIN_PLUGIN_DIR . 'includes/admin/jugadores.php');
}

// Callback para la subpágina de administrar categorias
function pa_manage_categorias_callback()
{
    // Cargar el archivo jugadores.php
    require_once(PADEL_ADMIN_PLUGIN_DIR . 'includes/admin/categorias.php');
}

// Callback para la subpágina de administrar torneos
function pa_manage_torneos_callback()
{
    // Cargar el archivo torneos.php
    require_once(PADEL_ADMIN_PLUGIN_DIR . 'includes/admin/torneos.php');
}

function mostrar_pagina_ver_partidos()
{
    include_once(plugin_dir_path(__FILE__) . 'includes/admin/partidos.php');
}

add_action('admin_menu', 'ver_inscriptos');

function ver_inscriptos()
{
    add_submenu_page(
        'slug', // Slug del menú padre
        'Ver Inscriptos',      // Título de la página
        'Ver Inscriptos',      // Título en el menú
        'manage_options',      // Capacidad requerida para ver la página
        'ver_inscriptos',   // Slug de la página
        'mostrar_pagina_inscriptos' // Función para mostrar el contenido de la página
    );
}

function mostrar_pagina_inscriptos()
{
    include_once(plugin_dir_path(__FILE__) . 'includes/admin/inscriptos.php');
}



// Agregar esto al archivo principal del plugin, fuera del código HTML
add_action('wp_ajax_delete_player', 'delete_player_callback');
add_action('wp_ajax_toggle_status', 'toggle_status_callback');

function delete_player_callback()
{
    // Verificar la solicitud AJAX y el nonce
    check_ajax_referer('delete_player_nonce', 'nonce');
    
    // Verificar si se proporciona el ID del jugador
    if (isset($_POST['jugador_id'])) {
        $jugador_id = intval($_POST['jugador_id']);
        global $wpdb;
        $players_table_name = $wpdb->prefix . 'pa_jugadores';
        // Eliminar el jugador de la base de datos
        $wpdb->delete($players_table_name, array('id' => $jugador_id));
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
    wp_die(); // Esto es importante para finalizar la ejecución
}

function toggle_status_callback()
{
    // Verificar la solicitud AJAX y el nonce
    check_ajax_referer('toggle_status_nonce', 'nonce');

    // Verificar si se proporciona el ID del jugador
    if (isset($_POST['jugador_id'])) {
        $jugador_id = intval($_POST['jugador_id']);
        global $wpdb;
        $players_table_name = $wpdb->prefix . 'pa_jugadores';
        // Obtener el estado actual del jugador
        $current_status = $wpdb->get_var($wpdb->prepare("SELECT estado FROM $players_table_name WHERE id = %d", $jugador_id));
        // Cambiar el estado del jugador
        $new_status = ($current_status == 'Pendiente') ? 'Confirmado' : 'Pendiente';
        $wpdb->update($players_table_name, array('estado' => $new_status), array('id' => $jugador_id));
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
    wp_die(); // Esto es importante para finalizar la ejecución
}


add_action('admin_menu', 'ver_detalles');

function ver_detalles()
{
    add_submenu_page(
        'slug', // Slug del menú padre
        'Ver Detalles',      // Título de la página
        'Ver Detalles',      // Título en el menú
        'manage_options',      // Capacidad requerida para ver la página
        'ver_detalles',   // Slug de la página
        'mostrar_pagina_detalles' // Función para mostrar el contenido de la página
    );
}

function mostrar_pagina_detalles()
{
    include_once(plugin_dir_path(__FILE__) . 'includes/admin/detalles_jugador.php');
}


// Enganchar la función save_player_details_callback a la acción admin_post_save_player_details
add_action('admin_post_save_player_details', 'save_player_details_callback');

// Enganchar la función save_player_details_callback a la acción admin_post_nopriv_save_player_details
add_action('admin_post_nopriv_save_player_details', 'save_player_details_callback');

function save_player_details_callback()
{
    // Verificar si se recibieron los datos del formulario
    if (!empty($_POST['nombre']) && !empty($_POST['apellido']) && !empty($_POST['dni']) && !empty($_POST['email']) && !empty($_POST['telefono']) && !empty($_POST['categoria']) && !empty($_POST['partidos_jugados']) && !empty($_POST['partidos_ganados']) && !empty($_POST['estado']) && isset($_POST['jugador_id'])) {
        // Obtener y sanitizar los datos del formulario
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $dni = sanitize_text_field($_POST['dni']);
        $email = sanitize_email($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $categoria_id = intval($_POST['categoria']);
        $partidos_jugados = intval($_POST['partidos_jugados']);
        $partidos_ganados = intval($_POST['partidos_ganados']);
        $estado = sanitize_text_field($_POST['estado']);
        $jugador_id = intval($_POST['jugador_id']);

        // Actualizar los datos del jugador en la base de datos
        global $wpdb;
        $players_table_name = $wpdb->prefix . 'pa_jugadores';

        // Crear un array para almacenar los datos actualizados
        $datos_actualizados = array();

        // Verificar y actualizar cada campo si se ha modificado
        if (!empty($nombre)) {
            $datos_actualizados['nombre'] = $nombre;
        }
        if (!empty($apellido)) {
            $datos_actualizados['apellido'] = $apellido;
        }
        if (!empty($dni)) {
            $datos_actualizados['dni'] = $dni;
        }
        if (!empty($email)) {
            $datos_actualizados['email'] = $email;
        }
        if (!empty($telefono)) {
            $datos_actualizados['telefono'] = $telefono;
        }
        if (!empty($categoria_id)) {
            $datos_actualizados['categoria_id'] = $categoria_id;
        }
        if (!empty($partidos_jugados)) {
            $datos_actualizados['partidos_jugados'] = $partidos_jugados;
        }
        if (!empty($partidos_ganados)) {
            $datos_actualizados['partidos_ganados'] = $partidos_ganados;
        }
        if (!empty($estado)) {
            $datos_actualizados['estado'] = $estado;
        }

        // Verificar si se han actualizado algunos campos
        if (!empty($datos_actualizados)) {
            // Actualizar los datos del jugador en la base de datos
            $result = $wpdb->update(
                $players_table_name,
                $datos_actualizados,
                array('id' => $jugador_id),
                null,
                array('%d') // donde
            );

            // Verificar si la actualización fue exitosa
            if ($result !== false) {
                // Agregar el parámetro de éxito a la URL de redirección
                $redirect_url = add_query_arg(array('success' => 'true'), admin_url('admin.php?page=ver_detalles&jugador_id=' . $jugador_id));
                
                // Redirigir a la página original de los detalles del jugador con el parámetro de éxito en la URL
                wp_redirect($redirect_url);
                exit; // ¡Es importante salir del script después de la redirección!
            } else {
                // Mostrar un mensaje de error con SweetAlert si falla la actualización
                echo json_encode(array('success' => false, 'message' => 'Hubo un error al guardar los detalles del jugador.'));
            }
        } else {
            // Mostrar un mensaje de error con SweetAlert si no se han actualizado campos
            echo json_encode(array('success' => false, 'message' => 'No se realizaron cambios en los detalles del jugador.'));
        }
    } else {
        // Mostrar un mensaje de error con SweetAlert si faltan datos del formulario
        echo json_encode(array('success' => false, 'message' => 'Faltan datos del formulario.'));
    }

    // Finalizar la ejecución
    wp_die();
}



add_action('admin_menu', 'nuevo_jugador');

function nuevo_jugador()
{
    add_submenu_page(
        'slug', // Slug del menú padre
        'Nuevo Jugador',      // Título de la página
        'Nuevo Jugador',      // Título en el menú
        'manage_options',      // Capacidad requerida para ver la página
        'nuevo_jugador',   // Slug de la página
        'mostrar_pagina_nuevo_jugador' // Función para mostrar el contenido de la página
    );
}

function mostrar_pagina_nuevo_jugador()
{
    include_once(plugin_dir_path(__FILE__) . 'includes/admin/nuevo_jugador.php');
}



// Función para procesar el formulario y agregar un nuevo jugador
function add_new_player_callback()
{
    // Inicializar un array para almacenar los mensajes de error
    $errors = array();

    // Verificar si se recibieron los datos del formulario
    if (isset($_POST['nombre'], $_POST['apellido'], $_POST['dni'], $_POST['email'], $_POST['telefono'], $_POST['categoria'], $_POST['partidos_jugados'], $_POST['partidos_ganados'], $_POST['estado'])) {
        // Obtener y sanitizar los datos del formulario
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $dni = sanitize_text_field($_POST['dni']);
        $email = sanitize_email($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $categoria_id = intval($_POST['categoria']);
        $partidos_jugados = intval($_POST['partidos_jugados']);
        $partidos_ganados = intval($_POST['partidos_ganados']);
        $estado = sanitize_text_field($_POST['estado']);

        // Verificar si algún campo requerido está vacío y agregar un mensaje de error correspondiente
        if (empty($nombre) || empty($apellido) || empty($dni) || empty($email) || empty($telefono) || empty($categoria_id) || empty($partidos_jugados) || empty($partidos_ganados) || empty($estado)) {
            $errors[] = 'Faltan datos del formulario.';
        }

        // Insertar el nuevo jugador en la base de datos
        global $wpdb;
        $players_table_name = $wpdb->prefix . 'pa_jugadores';

        // Verificar si el DNI ya existe en la base de datos y agregar un mensaje de error correspondiente
        $existing_player = $wpdb->get_row($wpdb->prepare("SELECT * FROM $players_table_name WHERE dni = %s", $dni));
        if ($existing_player) {
            // Crear un array para almacenar los datos del jugador existente
            $existing_player_data = array(
                'id' => $existing_player->id,
            );
            // Enviar una respuesta JSON con el mensaje de error y los datos del jugador existente
            wp_send_json_error(array('message' => 'Ya existe un jugador con el mismo número de DNI.', 'existing_player' => $existing_player_data));        
        } else {
            try {
                // Insertar el nuevo jugador en la base de datos
                $result = $wpdb->insert(
                    $players_table_name,
                    array(
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'dni' => $dni,
                        'email' => $email,
                        'telefono' => $telefono,
                        'categoria_id' => $categoria_id,
                        'partidos_jugados' => $partidos_jugados,
                        'partidos_ganados' => $partidos_ganados,
                        'estado' => $estado,
                    ),
                    array(
                        '%s', // nombre
                        '%s', // apellido
                        '%s', // dni
                        '%s', // email
                        '%s', // telefono
                        '%d', // categoria_id
                        '%d', // partidos_jugados
                        '%d', // partidos_ganados
                        '%s', // estado
                    )
                );

                // Verificar si la inserción fue exitosa
                if ($result !== false) {
                    // Enviar una respuesta JSON con éxito
                    wp_send_json_success('El nuevo jugador se ha agregado correctamente.');
                } else {
                    // Agregar un mensaje de error en caso de falla en la inserción
                    $errors[] = 'Hubo un error al guardar los detalles del jugador.';
                }
            } catch (Exception $e) {
                // Agregar un mensaje de error en caso de excepción
                $errors[] = 'Hubo un error al guardar los detalles del jugador.';
            }
        }
    } else {
        // Agregar un mensaje de error si faltan datos del formulario
        $errors[] = '';
    }

    // Enviar una respuesta JSON con los mensajes de error
    wp_send_json_error($errors);
}




// Registrar la acción para procesar el formulario de añadir jugador
add_action('admin_post_add_new_player', 'add_new_player_callback');
add_action('admin_post_nopriv_add_new_player', 'add_new_player_callback');



add_action('admin_menu', 'generar_partidos');

function generar_partidos()
{
    add_submenu_page(
        'slug', // Slug del menú padre
        'Generar Partidos',      // Título de la página
        'Generar Partidos',      // Título en el menú
        'manage_options',      // Capacidad requerida para ver la página
        'generar_partidos',   // Slug de la página
        'mostrar_pagina_generar_partidos' // Función para mostrar el contenido de la página
    );
}

function mostrar_pagina_generar_partidos()
{
    include_once(plugin_dir_path(__FILE__) . 'includes/admin/generar_partidos.php');
}




// Función para procesar el formulario y generar los partidos
function generate_matches_callback()
{
    // Verificar si se recibió el ID del torneo
    if (isset($_POST['torneo_id'])) {
        // Obtener y sanitizar el ID del torneo
        $torneo_id = intval($_POST['torneo_id']);

        // Consultar la base de datos para obtener las parejas inscritas en el torneo
        global $wpdb;
        $table_inscritos = $wpdb->prefix . 'pa_inscritos';
        $table_parejas = $wpdb->prefix . 'pa_parejas';
        $table_jugadores = $wpdb->prefix . 'pa_jugadores'; // Definir la tabla de jugadores
        $table_partidos = $wpdb->prefix . 'pa_partidos'; 

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

        // Lógica para generar los partidos
        $ronda = 1; // Inicializamos la primera ronda

        // Crear los partidos para la primera ronda
        foreach ($parejas_inscritas as $index => $pareja) {
            // Solo generar partidos para la mitad de las parejas
            if ($index % 2 === 0) {
                // Obtener la pareja para la cual se generará el partido
                $pareja_local = $pareja;
                $pareja_visitante = $parejas_inscritas[$index + 1]; // La siguiente pareja en la lista

                // Guardar el partido en la base de datos
                $result = $wpdb->insert(
                    $table_partidos,
                    array(
                        'torneo_id' => $torneo_id,
                        'ronda' => $ronda,
                        'pareja_local_id' => $pareja_local['pareja_id'],
                        'pareja_visitante_id' => $pareja_visitante['pareja_id'],
                    ),
                    array('%d', '%d', '%d', '%d')
                );
            }
        }

        // Redirigir a la página de generar_partidos con un parámetro de éxito en la URL
        wp_redirect(admin_url('admin.php?page=generar_partidos&torneo_id=' . $torneo_id . '&success=true'));
        exit;
    } else {
        // Si falta el ID del torneo, redirigir con un parámetro de error en la URL
        wp_redirect(admin_url('admin.php?page=generar_partidos&error=missing_torneo_id'));
        exit;
    }
}



// Registrar la acción para procesar el formulario de generación de partidos
add_action('admin_post_generate_matches', 'generate_matches_callback');
add_action('admin_post_nopriv_generate_matches', 'generate_matches_callback');



// Función para obtener los partidos de un torneo mediante AJAX
function get_torneo_partidos_callback() {
    // Verificar si se recibió el ID del torneo
    if (isset($_GET['torneo_id'])) {
        // Obtener el ID del torneo
        $torneo_id = intval($_GET['torneo_id']);

        // Consultar la base de datos para obtener los partidos del torneo seleccionado
        global $wpdb;
        $table_partidos = $wpdb->prefix . 'pa_partidos';

        $partidos = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_partidos WHERE torneo_id = %d",
                $torneo_id
            ),
            ARRAY_A
        );

        // Generar el HTML para los partidos del torneo
        $html = '';
        if ($partidos) {
            $html .= '<h2>Partidos del Torneo</h2>';
            $html .= '<table class="wp-list-table widefat fixed striped">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th width="10%">Llave</th>';
            $html .= '<th>Pareja Local</th>';
            $html .= '<th>Pareja Visitante</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach ($partidos as $partido) {
                // Obtener los nombres y apellidos de los jugadores de la pareja local
                $pareja_local = obtener_pareja_jugadores($partido['pareja_local_id']);
                // Obtener los nombres y apellidos de los jugadores de la pareja visitante
                $pareja_visitante = obtener_pareja_jugadores($partido['pareja_visitante_id']);
                
                $html .= '<tr>';
                $html .= '<td>' . $partido['id'] . '</td>';
                // Mostrar nombre de jugador1 - nombre de jugador2 (ID de la pareja)
                $html .= '<td>' . $pareja_local['jugador1'] . ' - ' . $pareja_local['jugador2'] . ' 
                <button class="assign-winner button" data-pareja-id="' . $partido['pareja_local_id'] . '">Asignar Ganador</button>
                </td>';
                // Mostrar nombre de jugador1 - nombre de jugador2 (ID de la pareja)
                $html .= '<td>' . $pareja_visitante['jugador1'] . ' - ' . $pareja_visitante['jugador2'] . ' 
                <button class="assign-winner button" data-pareja-id="' . $partido['pareja_visitante_id'] . '">Asignar Ganador</button>
                </td>';
                
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html .= '<p>No se encontraron partidos para este torneo.</p>';
        }
        
        // Devolver el HTML de los partidos del torneo
        wp_send_json_success($html);
    } else {
        // Enviar una respuesta JSON con un mensaje de error si falta el ID del torneo
        wp_send_json_error('Falta el ID del torneo.');
    }
}


// Función para obtener los nombres y apellidos de los jugadores de una pareja
function obtener_pareja_jugadores($pareja_id) {
    global $wpdb;

    // Consultar la base de datos para obtener los IDs de los jugadores de la pareja
    $table_parejas = $wpdb->prefix . 'pa_parejas';

    $jugadores_ids = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT jugador1_id, jugador2_id FROM $table_parejas WHERE id = %d",
            $pareja_id
        ),
        ARRAY_A
    );

    // Consultar la base de datos para obtener los nombres y apellidos de los jugadores
    $table_jugadores = $wpdb->prefix . 'pa_jugadores';

    $jugador1 = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT CONCAT(nombre, ' ', apellido) FROM $table_jugadores WHERE id = %d",
            $jugadores_ids['jugador1_id']
        )
    );

    $jugador2 = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT CONCAT(nombre, ' ', apellido) FROM $table_jugadores WHERE id = %d",
            $jugadores_ids['jugador2_id']
        )
    );

    return array(
        'jugador1' => $jugador1,
        'jugador2' => $jugador2
    );
}



// Registrar la acción para la función de obtener los partidos de un torneo
add_action('wp_ajax_get_torneo_partidos', 'get_torneo_partidos_callback');
add_action('wp_ajax_nopriv_get_torneo_partidos', 'get_torneo_partidos_callback');



// Función para asignar el ganador del partido
function asignar_ganador_partido_callback() {
    // Verificar si se recibió el ID del torneo y el ID del ganador
    if (isset($_POST['torneo_id']) && isset($_POST['ganador_id'])) {
        // Obtener el ID del torneo y el ID del ganador
        $torneo_id = intval($_POST['torneo_id']);
        $ganador_id = intval($_POST['ganador_id']);

        // Verificar si los valores recibidos son válidos (mayores que cero)
        if ($torneo_id > 0 && $ganador_id > 0) {
            // Actualizar el campo de ganador_id en la tabla de partidos
            global $wpdb;
            $table_partidos = $wpdb->prefix . 'pa_partidos';

            $result = $wpdb->update(
                $table_partidos,
                array('ganador_id' => $ganador_id),
                array('torneo_id' => $torneo_id),
                array('%d'),
                array('%d')
            );

            // Verificar si la actualización fue exitosa
            if ($result !== false) {
                // Enviar una respuesta JSON de éxito
                wp_send_json_success('Ganador asignado correctamente.');
            } else {
                // Enviar una respuesta JSON de error si la actualización falló
                wp_send_json_error('Error al asignar el ganador del partido.');
            }
        } else {
            // Enviar una respuesta JSON de error si los datos recibidos son inválidos
            wp_send_json_error('Los datos recibidos son inválidos.');
        }
    } else {
        // Enviar una respuesta JSON de error si faltan datos requeridos
        wp_send_json_error('Faltan datos requeridos.');
    }
}



// Registrar la acción para la función de asignar ganador del partido
add_action('wp_ajax_asignar_ganador_partido', 'asignar_ganador_partido_callback');
add_action('wp_ajax_nopriv_asignar_ganador_partido', 'asignar_ganador_partido_callback');
