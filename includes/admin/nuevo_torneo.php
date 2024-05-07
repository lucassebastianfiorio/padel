<?php
global $wpdb;
$table_categorias = $wpdb->prefix . 'pa_categorias';
$categorias = $wpdb->get_results("SELECT * FROM $table_categorias", ARRAY_A);

// Separar categorías de damas y caballeros
$categorias_damas = [];
$categorias_caballeros = [];
foreach ($categorias as $categoria) {
    if (strpos($categoria['nombre'], 'Damas') !== false) {
        $categorias_damas[] = $categoria;
    } elseif (strpos($categoria['nombre'], 'Caballeros') !== false) {
        $categorias_caballeros[] = $categoria;
    }
}
?>

<div class="wrap">
    <h1>Nuevo Torneo</h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="crear_torneo">

        <!-- Campos para el formulario -->
        <label for="nombre">Nombre del Torneo:</label>
        <input type="text" name="nombre" id="nombre" required><br>

        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" required><br>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" name="fecha_fin" id="fecha_fin" required><br>

        <label for="mixto">Mixto:</label>
        <select name="mixto" id="mixto" required onchange="toggleCategorias(this.value)">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select><br>

        <!-- Selección de categorías oculta -->
        <select name="categoria" id="categoria" style="display: none;">
            <optgroup label="Damas">
                <?php foreach ($categorias_damas as $categoria) : ?>
                    <option value="<?php echo esc_attr($categoria['id']); ?>"><?php echo esc_html($categoria['nombre']); ?></option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="Caballeros">
                <?php foreach ($categorias_caballeros as $categoria) : ?>
                    <option value="<?php echo esc_attr($categoria['id']); ?>"><?php echo esc_html($categoria['nombre']); ?></option>
                <?php endforeach; ?>
            </optgroup>
        </select><br>

        <!-- Selección de categorías femeninas -->
        <select name="categorias_damas[]" id="categorias_damas" style="display: none;" multiple>
            <?php foreach ($categorias_damas as $categoria) : ?>
                <option value="<?php echo esc_attr($categoria['id']); ?>"><?php echo esc_html($categoria['nombre']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <!-- Selección de categorías masculinas -->
        <select name="categorias_caballeros[]" id="categorias_caballeros" style="display: none;" multiple>
            <?php foreach ($categorias_caballeros as $categoria) : ?>
                <option value="<?php echo esc_attr($categoria['id']); ?>"><?php echo esc_html($categoria['nombre']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <!-- Selección de género -->
        <label for="genero" id="genero_label" style="display: none;">Género:</label>
        <select name="genero" id="genero" style="display: none;">
            <option value="Femenino">Femenino</option>
            <option value="Masculino">Masculino</option>
        </select><br>

        <label for="cupo">Cupo:</label>
        <input type="number" name="cupo" id="cupo" required><br>

        <!-- Botón para enviar el formulario -->
        <input type="submit" value="Crear Torneo">
    </form>
</div>

<script>
    function toggleCategorias(mixto) {
        var categorias = document.getElementById("categoria");
        var categoriasDamas = document.getElementById("categorias_damas");
        var categoriasCaballeros = document.getElementById("categorias_caballeros");
        var generoLabel = document.getElementById("genero_label");
        var generoSelect = document.getElementById("genero");

        if (mixto === "1") {
            categorias.style.display = "block";
            categoriasDamas.style.display = "none";
            categoriasCaballeros.style.display = "none";
            generoLabel.style.display = "none";
            generoSelect.style.display = "none";
        } else {
            categorias.style.display = "none";
            generoLabel.style.display = "block";
            generoSelect.style.display = "block";
            toggleGenero(generoSelect.value);
        }
    }

    function toggleGenero(genero) {
        var categoriasDamas = document.getElementById("categorias_damas");
        var categoriasCaballeros = document.getElementById("categorias_caballeros");

        if (genero === "Femenino") {
            categoriasDamas.style.display = "block";
            categoriasCaballeros.style.display = "none";
        } else {
            categoriasDamas.style.display = "none";
            categoriasCaballeros.style.display = "block";
        }
    }
</script>
