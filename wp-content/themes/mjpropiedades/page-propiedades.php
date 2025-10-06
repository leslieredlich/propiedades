<?php
/**
 * Template Name: Página de Propiedades
 * 
 * Plantilla para mostrar todas las propiedades con filtros de búsqueda
 */

get_header(); ?>

<section class="properties-page">
    <div class="container">
        <div class="properties-header">
            <h1 class="page-title">Propiedades Disponibles</h1>
            <p class="page-subtitle">Encuentra la propiedad perfecta para ti</p>
        </div>
        
        <!-- Filtros de búsqueda -->
        <div class="properties-filters">
            <form class="filters-form" method="get" action="">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="filter-tipo-propiedad">Tipo de Propiedad</label>
                        <select id="filter-tipo-propiedad" name="tipo_propiedad" class="filter-select">
                            <option value="">Todos los tipos</option>
            <?php 
                            $tipos_propiedad = get_option('mjpropiedades_tipos_propiedad', array());
                            foreach ($tipos_propiedad as $value => $label) {
                                $selected = (isset($_GET['tipo_propiedad']) && $_GET['tipo_propiedad'] === $value) ? 'selected' : '';
                                echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                
                <div class="filter-group">
                        <label for="filter-ubicacion">Ubicación</label>
                        <select id="filter-ubicacion" name="ubicacion" class="filter-select">
                            <option value="">Todas las comunas</option>
                            <?php
                            $comunas = get_option('mjpropiedades_comunas', array());
                            foreach ($comunas as $value => $label) {
                                $selected = (isset($_GET['ubicacion']) && $_GET['ubicacion'] === $value) ? 'selected' : '';
                                echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                    </select>
                </div>
                
                <div class="filter-group">
                        <label for="filter-dormitorios">Dormitorios</label>
                        <select id="filter-dormitorios" name="dormitorios" class="filter-select">
                            <option value="">Cualquier cantidad</option>
                            <?php
                            $dormitorios_options = get_option('mjpropiedades_dormitorios', array());
                            foreach ($dormitorios_options as $value => $label) {
                                $selected = (isset($_GET['dormitorios']) && $_GET['dormitorios'] === $value) ? 'selected' : '';
                                echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                    </select>
                </div>
                
                <div class="filter-group">
                        <label for="filter-banos">Baños</label>
                        <select id="filter-banos" name="banos" class="filter-select">
                            <option value="">Cualquier cantidad</option>
                            <?php
                            $banos_options = get_option('mjpropiedades_banos', array());
                            foreach ($banos_options as $value => $label) {
                                $selected = (isset($_GET['banos']) && $_GET['banos'] === $value) ? 'selected' : '';
                                echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                        </select>
                </div>
                
                <div class="filter-group">
                        <label for="filter-precio-min">Precio Mínimo</label>
                        <input type="number" id="filter-precio-min" name="precio_min" class="filter-input" 
                               value="<?php echo isset($_GET['precio_min']) ? esc_attr($_GET['precio_min']) : ''; ?>" 
                               placeholder="0" min="0">
                </div>
                
                <div class="filter-group">
                        <label for="filter-precio-max">Precio Máximo</label>
                        <input type="number" id="filter-precio-max" name="precio_max" class="filter-input" 
                               value="<?php echo isset($_GET['precio_max']) ? esc_attr($_GET['precio_max']) : ''; ?>" 
                               placeholder="1000000000" min="0">
                </div>
                
                    <div class="filter-group">
                        <button type="submit" class="filter-btn">Filtrar</button>
                        <a href="<?php echo get_permalink(); ?>" class="filter-reset">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Resultados de búsqueda -->
        <div class="properties-results">
            <?php
            // Realizar búsqueda de propiedades usando la función centralizada
            $properties_query = mjpropiedades_search_properties();
            
            // Debug: mostrar los parámetros recibidos (solo para admin)
            if (current_user_can('manage_options')) {
                echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px;">';
                echo '<strong>Debug - Parámetros recibidos:</strong><br>';
                echo '<pre>' . print_r($_GET, true) . '</pre>';
                echo '</div>';
            }
            
            // Mostrar información de resultados
            echo '<div class="search-results-info">';
            echo '<p>Mostrando ' . $properties_query->found_posts . ' propiedades encontradas';
            if ($properties_query->found_posts > 0) {
                echo ' (página ' . max(1, get_query_var('paged')) . ' de ' . $properties_query->max_num_pages . ')';
            }
            echo '</p>';
            echo '</div>';
            
            if ($properties_query->have_posts()) :
                echo '<div class="properties-grid">';
                while ($properties_query->have_posts()) : $properties_query->the_post();
                    $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                    $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                    $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                    $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                    $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                    $operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                    $tipo = get_post_meta(get_the_ID(), '_propiedad_tipo', true);
                    
                    $tag_class = ($operacion === 'arriendo') ? 'style="background: #ff6b35;"' : '';
                    $tag_text = ucfirst($operacion);
                    $precio_text = $precio ? '$' . number_format($precio, 0, ',', '.') : 'Consultar';
                    if ($operacion === 'arriendo') {
                        $precio_text .= '/mes';
                    }
                    ?>
                    <div class="property-card">
                        <div class="property-image">
                            <div class="property-tag" <?php echo $tag_class; ?>><?php echo $tag_text; ?></div>
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('alt' => get_the_title() . ' - Propiedad en ' . ($comuna ? $comuna : 'Región de Coquimbo'))); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/propiedades/placeholder.jpg" alt="<?php the_title(); ?> - Propiedad en <?php echo $comuna ? $comuna : 'Región de Coquimbo'; ?>" onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 250\'><rect fill=\'%23f0f0f0\' width=\'400\' height=\'250\'/><text x=\'50%\' y=\'50%\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'><?php the_title(); ?></text></svg>'">
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="property-content">
                            <h3 class="property-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php if ($comuna) : ?>
                                <p class="property-location">📍 <?php echo $comuna; ?></p>
                            <?php endif; ?>
                            
                            <div class="property-details">
                                <?php if ($dormitorios) : ?>
                                    <span><?php echo $dormitorios; ?> dormitorios</span>
                                <?php endif; ?>
                                
                                <?php if ($banos) : ?>
                                    <span><?php echo $banos; ?> baños</span>
                                <?php endif; ?>
                                
                                <?php if ($metros) : ?>
                                    <span><?php echo $metros; ?> m²</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($precio) : ?>
                                <div class="property-price">
                                    <?php echo $precio_text; ?>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="property-btn">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
            <?php 
                endwhile;
                echo '</div>';
                
                // Paginación
                if ($properties_query->max_num_pages > 1) :
            ?>
                    <div class="pagination">
                        <?php 
                        echo paginate_links(array(
                            'total' => $properties_query->max_num_pages,
                            'current' => max(1, get_query_var('paged')),
                            'format' => '?paged=%#%',
                            'show_all' => false,
                            'type' => 'plain',
                            'end_size' => 2,
                            'mid_size' => 1,
                            'prev_text' => '‹ Anterior',
                            'next_text' => 'Siguiente ›',
                            'add_args' => false,
                            'add_fragment' => '',
                        ));
                        ?>
                    </div>
            <?php
                endif;
                
                wp_reset_postdata();
            else :
            ?>
                <div class="no-properties">
                    <h3>No se encontraron propiedades</h3>
                    <p>Intenta ajustar los filtros de búsqueda o <a href="<?php echo get_permalink(); ?>">ver todas las propiedades</a>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>