<?php
// Verificar si el usuario tiene permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Consultar la base de datos para obtener la lista de categorías
global $wpdb;
$table_categorias = $wpdb->prefix . 'pa_categorias';
$categorias = $wpdb->get_results("SELECT * FROM $table_categorias", ARRAY_A);
?>

<div class="wrap">
    <h1>Categorías</h1>

    <?php if ($categorias) : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria) : ?>
                    <tr>
                        <td><?php echo $categoria['id']; ?></td>
                        <td><?php echo $categoria['nombre']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No hay categorías disponibles.</p>
    <?php endif; ?>
</div>
