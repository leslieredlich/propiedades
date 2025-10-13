<?php
/**
 * Template para mostrar el archivo de propiedades
 * Plantilla unificada para mostrar todas las propiedades con filtros de búsqueda
 */

get_header(); ?>

<style type="text/css" id="mjpropiedades-property-cards-colors-direct">
<?php echo mjpropiedades_property_cards_dynamic_css(); ?>
</style>

<section class="properties-page">
    <div class="container">
        <!-- Header con breadcrumbs -->
        <div class="properties-header">
            <nav class="breadcrumbs">
                <a href="<?php echo home_url(); ?>">Inicio</a>
                <span class="separator">›</span>
                <span class="current">Propiedades</span>
            </nav>
            <br><br>
            <h1 class="page-title">Propiedades Disponibles</h1>
            <p class="page-subtitle">Encuentra la propiedad perfecta para ti</p>
                        </div>
        
        <!-- Controles de vista y ordenamiento -->
        <div class="properties-controls">
            <div class="results-summary">
                <?php
                $properties_query = mjpropiedades_search_properties();
                $total_results = $properties_query->found_posts;
                ?>
                <span class="results-count"><?php echo $total_results; ?> propiedades encontradas</span>
                </div>
            
            <div class="view-controls">
                <div class="sort-controls">
                    <label for="sort-by">Ordenar por:</label>
                    <select id="sort-by" name="sort" class="sort-select">
                        <option value="date-desc" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'date-desc'); ?>>Más recientes</option>
                        <option value="price-asc" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'price-asc'); ?>>Precio: menor a mayor</option>
                        <option value="price-desc" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'price-desc'); ?>>Precio: mayor a menor</option>
                        <option value="title-asc" <?php selected(isset($_GET['sort']) ? $_GET['sort'] : '', 'title-asc'); ?>>Nombre: A-Z</option>
                    </select>
                </div>
                
                <div class="view-toggle">
                    <button type="button" class="view-btn active" data-view="grid" title="Vista de cuadrícula">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="2" width="7" height="7"/>
                            <rect x="11" y="2" width="7" height="7"/>
                            <rect x="2" y="11" width="7" height="7"/>
                            <rect x="11" y="11" width="7" height="7"/>
                        </svg>
                    </button>
                    <button type="button" class="view-btn" data-view="list" title="Vista de lista">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="3" width="16" height="2"/>
                            <rect x="2" y="7" width="16" height="2"/>
                            <rect x="2" y="11" width="16" height="2"/>
                            <rect x="2" y="15" width="16" height="2"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Formulario de búsqueda principal -->
        <div class="search-section section">
            <?php 
            echo mjpropiedades_get_search_form(array(
                'show_title' => true,
                'title' => 'Encuentra tu Propiedad Ideal',
                'button_text' => 'Buscar Propiedades',
                'action' => get_post_type_archive_link('propiedad'),
                'preserve_values' => true,
                'form_id' => 'properties-search-form'
            ));
            ?>
        </div>
        
        <div class="properties-layout">
            
            <!-- Contenido principal -->
            <main class="properties-main">
                <!-- Resultados de búsqueda -->
                <div class="properties-results">
                    <?php
                    // Realizar búsqueda de propiedades usando la función centralizada
                    $properties_query = mjpropiedades_search_properties();
                    
            
            if ($properties_query->have_posts()) :
                        echo '<div class="properties-grid" id="properties-grid">';
                while ($properties_query->have_posts()) : $properties_query->the_post();
                    $precio = get_post_meta(get_the_ID(), '_propiedad_precio', true);
                    $dormitorios = get_post_meta(get_the_ID(), '_propiedad_dormitorios', true);
                    $banos = get_post_meta(get_the_ID(), '_propiedad_banos', true);
                    $metros = get_post_meta(get_the_ID(), '_propiedad_metros', true);
                    $comuna = get_post_meta(get_the_ID(), '_propiedad_comuna', true);
                            $operacion = get_post_meta(get_the_ID(), '_propiedad_operacion', true);
                    $tipo = get_post_meta(get_the_ID(), '_propiedad_tipo', true);
                    
                            // Usar colores configurados desde el admin
                            $venta_tag_color = get_theme_mod('mjpropiedades_card_tag_bg', '#00d4aa');
                            $arriendo_tag_color = get_theme_mod('mjpropiedades_card_arriendo_tag_bg', '#4285F4');
                            $tag_text_color = get_theme_mod('mjpropiedades_card_tag_text', '#ffffff');
                            
                            // Determinar clase y color según la operación
                            $tag_class = ($operacion === 'arriendo') ? 'arriendo' : 'venta';
                            $tag_bg = ($operacion === 'arriendo') ? $arriendo_tag_color : $venta_tag_color;
                            $tag_style = 'style="background: ' . $tag_bg . '; color: ' . $tag_text_color . ';"';
                            $tag_text = ucfirst($operacion);
                    $precio_text = $precio ? '$' . number_format(floatval($precio), 0, ',', '.') : 'Consultar';
                    if ($operacion === 'arriendo') {
                        $precio_text .= '/mes';
                    }
                    ?>
                            <article class="property-card" data-price="<?php echo $precio; ?>" data-date="<?php echo get_the_date('Y-m-d'); ?>">
                                <div class="property-image">
                                    <div class="property-tag <?php echo $tag_class; ?>" <?php echo $tag_style; ?>><?php echo $tag_text; ?></div>
                                    <div class="property-actions">
                                        <button type="button" class="property-favorite" title="Agregar a favoritos">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 18.35l-1.45-1.32C5.4 14.25 2 11.28 2 7.5 2 4.42 4.42 2 7.5 2c1.74 0 3.41.81 4.5 2.09C13.09 2.81 14.76 2 16.5 2 19.58 2 22 4.42 22 7.5c0 3.78-3.4 6.75-6.55 9.53L10 18.35z"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="property-share" title="Compartir">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M15 8a3 3 0 1 0-2.977-2.63l-4.94 2.47a3 3 0 1 0 0 4.319l4.94 2.47a3 3 0 1 0 .895-1.789l-4.94-2.47a3.027 3.027 0 0 0 0-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                                            </svg>
                                        </button>
                            </div>
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
                                    <!-- Precio primero, como en la imagen -->
                                    <?php if ($precio) : ?>
                                        <div class="property-price">
                                            <?php echo $precio_text; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Título de la propiedad -->
                            <h3 class="property-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                                    <!-- Ubicación -->
                            <?php if ($comuna) : ?>
                                        <p class="property-location">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                            <?php echo $comuna; ?>
                                        </p>
                            <?php endif; ?>
                            
                                    <!-- Detalles de la propiedad -->
                            <div class="property-details">
                                <?php if ($dormitorios) : ?>
                                            <div class="detail-item">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                    <path d="M8.5 5.5a.5.5 0 0 0-1 0V7H6a.5.5 0 0 0 0 1h1.5v1.5a.5.5 0 0 0 1 0V8H10a.5.5 0 0 0 0-1H8.5V5.5z"/>
                                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4H14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3.5V3.5A2.5 2.5 0 0 1 8 1zm0 1a1.5 1.5 0 0 0-1.5 1.5V4h3V3.5A1.5 1.5 0 0 0 8 2z"/>
                                                </svg>
                                                <span><?php echo $dormitorios; ?> dorm</span>
                                            </div>
                                <?php endif; ?>
                                
                                <?php if ($banos) : ?>
                                            <div class="detail-item">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4H14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3.5V3.5A2.5 2.5 0 0 1 8 1zm0 1a1.5 1.5 0 0 0-1.5 1.5V4h3V3.5A1.5 1.5 0 0 0 8 2z"/>
                                                </svg>
                                    <span><?php echo $banos; ?> baños</span>
                                            </div>
                                <?php endif; ?>
                                
                                <?php if ($metros) : ?>
                                            <div class="detail-item">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4H14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3.5V3.5A2.5 2.5 0 0 1 8 1zm0 1a1.5 1.5 0 0 0-1.5 1.5V4h3V3.5A1.5 1.5 0 0 0 8 2z"/>
                                                </svg>
                                    <span><?php echo $metros; ?> m²</span>
                                            </div>
                                <?php endif; ?>
                            </div>
                            
                                    <!-- Botón VER DETALLES -->
                                    <a href="<?php the_permalink(); ?>" class="property-btn">
                                        VER DETALLES
                                    </a>
                                </div>
                            </article>
                    <?php 
                        endwhile;
                        echo '</div>';
                        
                        // Paginación mejorada
                        if ($properties_query->max_num_pages > 1) :
                    ?>
                        <nav class="pagination-wrapper">
                    <div class="pagination-info">
                                Página <?php echo max(1, isset($_GET['page']) ? intval($_GET['page']) : 1); ?> de <?php echo $properties_query->max_num_pages; ?>
                    </div>
                            <div class="pagination">
                        <?php 
                                // Preservar todos los parámetros GET excepto page/paged
                                $current_args = $_GET;
                                unset($current_args['page'], $current_args['paged']);
                                
                                echo paginate_links(array(
                                    'total' => $properties_query->max_num_pages,
                                    'current' => max(1, isset($_GET['page']) ? intval($_GET['page']) : 1),
                            'format' => '?page=%#%',
                            'show_all' => false,
                                    'type' => 'plain',
                            'end_size' => 2,
                                    'mid_size' => 1,
                                    'prev_text' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg> Anterior',
                                    'next_text' => 'Siguiente <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>',
                                    'add_args' => $current_args,
                                    'add_fragment' => '',
                                ));
                                ?>
                            </div>
                            </nav>
                    <?php
                        endif;
                        
                        wp_reset_postdata();
                    else :
                    ?>
                        <div class="no-properties">
                            <div class="no-properties-icon">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                    <h3>No se encontraron propiedades</h3>
                    <p>Intenta ajustar los filtros de búsqueda o <a href="<?php echo get_post_type_archive_link('propiedad'); ?>">ver todas las propiedades</a>.</p>
                            <div class="no-properties-actions">
                                <a href="<?php echo get_post_type_archive_link('propiedad'); ?>" class="btn primary">Ver todas las propiedades</a>
                                <a href="<?php echo home_url(); ?>" class="btn secondary">Volver al inicio</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</section>

<?php get_footer(); ?>
