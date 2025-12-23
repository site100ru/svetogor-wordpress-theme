<?php
/**
 * SEO Meta Fields System (Complete)
 * Добавляет Title, Meta Description и Open Graph для всех типов контента
 */

// ============================================================================
// РЕГИСТРАЦИЯ МЕТА-БОКСОВ ДЛЯ ПОСТОВ
// ============================================================================

function add_seo_meta_boxes() {
    $post_types = array('post', 'page', 'product', 'portfolio', 'news', 'services');
    
    foreach ($post_types as $post_type) {
        add_meta_box('seo_meta_box', 'SEO настройки', 'render_seo_meta_box', $post_type, 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'add_seo_meta_boxes');
add_action('add_meta_boxes_product', 'add_seo_meta_boxes');

function render_seo_meta_box($post) {
    wp_nonce_field('seo_meta_box_nonce', 'seo_meta_box_nonce_field');
    
    $seo_title = get_post_meta($post->ID, '_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_seo_description', true);
    $seo_image = get_post_meta($post->ID, '_seo_image', true);
    $default_title = get_the_title($post->ID);
    $default_image_url = get_default_seo_image($post->ID);
    ?>
    
    <div class="seo-meta-fields" style="padding: 10px 0;">
        
        <!-- SEO Title -->
        <div class="seo-field-group" style="margin-bottom: 20px;">
            <label for="seo_title" style="display: block; font-weight: 600; margin-bottom: 8px;">
                SEO Title
                <span style="font-weight: normal; color: #666; font-size: 12px;">(рекомендуется 50-60 символов)</span>
            </label>
            
            <input type="text" id="seo_title" name="seo_title" value="<?php echo esc_attr($seo_title); ?>" 
                placeholder="<?php echo esc_attr($default_title); ?>" style="width: 100%; padding: 8px; font-size: 14px;" maxlength="70" />
            
            <div style="margin-top: 5px; font-size: 12px; color: #666;">
                <span id="seo_title_counter">0</span> символов
                <span id="seo_title_status" style="margin-left: 10px;"></span>
            </div>
            
            <p class="description" style="margin-top: 8px;">
                Если оставить пустым, будет использован заголовок страницы. Оптимальная длина: 50-60 символов.
            </p>
        </div>
        
        <!-- SEO Description -->
        <div class="seo-field-group" style="margin-bottom: 20px;">
            <label for="seo_description" style="display: block; font-weight: 600; margin-bottom: 8px;">
                Meta Description
                <span style="font-weight: normal; color: #666; font-size: 12px;">(рекомендуется 150-160 символов)</span>
            </label>
            
            <textarea id="seo_description" name="seo_description" rows="3" placeholder="Краткое описание страницы для поисковых систем..." 
                style="width: 100%; padding: 8px; font-size: 14px;" maxlength="320"><?php echo esc_textarea($seo_description); ?></textarea>
            
            <div style="margin-top: 5px; font-size: 12px; color: #666;">
                <span id="seo_description_counter">0</span> символов
                <span id="seo_description_status" style="margin-left: 10px;"></span>
            </div>
            
            <p class="description" style="margin-top: 8px;">
                Краткое описание содержимого страницы. Оптимальная длина: 150-160 символов.
            </p>
        </div>
        
        <!-- SEO Image (Open Graph) -->
        <div class="seo-field-group" style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                Open Graph изображение
                <span style="font-weight: normal; color: #666; font-size: 12px;">(для соцсетей и мессенджеров)</span>
            </label>
            
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <div id="seo_image_preview" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; position: relative;">
                    <?php if ($seo_image): 
                        $image_url = wp_get_attachment_url($seo_image);
                    ?>
                        <img src="<?php echo esc_url($image_url); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                    <?php elseif ($default_image_url): ?>
                        <img src="<?php echo esc_url($default_image_url); ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;" />
                        <div style="position: absolute; bottom: 5px; left: 5px; font-size: 11px; color: #666; background: rgba(255,255,255,0.9); padding: 2px 5px; border-radius: 3px;">по умолчанию</div>
                    <?php else: ?>
                        <span style="color: #999; font-size: 12px;">Нет изображения</span>
                    <?php endif; ?>
                </div>
                
                <div style="flex: 1;">
                    <input type="hidden" id="seo_image" name="seo_image" value="<?php echo esc_attr($seo_image); ?>" />
                    
                    <button type="button" class="button" id="upload_seo_image_button" style="margin-bottom: 8px;">
                        Выбрать изображение
                    </button>
                    
                    <button type="button" class="button" id="remove_seo_image_button" style="margin-bottom: 8px; <?php echo $seo_image ? '' : 'display:none;'; ?>">
                        Удалить изображение
                    </button>
                    
                    <p class="description" style="margin: 0;">
                        <?php if ($default_image_url): ?>
                            По умолчанию используется: <br/>
                            <strong>
                            <?php 
                            if (get_post_type($post->ID) === 'portfolio') {
                                echo 'Главное изображение портфолио';
                            } else {
                                echo 'Миниатюра записи';
                            }
                            ?>
                            </strong>
                        <?php else: ?>
                            Если не выбрать, будет использован логотип сайта
                        <?php endif; ?>
                        <br/>Рекомендуемый размер: 1200×630 px
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Предпросмотр в поисковой выдаче -->
        <div class="seo-preview" style="background: #f9f9f9; padding: 15px; border-radius: 4px; border-left: 4px solid #0073aa;">
            <h4 style="margin: 0 0 10px 0; font-size: 13px; color: #0073aa;">
                Предпросмотр в поисковой выдаче
            </h4>
            <div style="font-family: Arial, sans-serif;">
                <div id="preview_title" style="color: #1a0dab; font-size: 18px; margin-bottom: 5px; line-height: 1.2;">
                    <?php echo esc_html($seo_title ?: $default_title); ?>
                </div>
                <div id="preview_url" style="color: #006621; font-size: 14px; margin-bottom: 5px;">
                    <?php echo esc_url(get_permalink($post->ID) ?: home_url('/')); ?>
                </div>
                <div id="preview_description" style="color: #545454; font-size: 13px; line-height: 1.4;">
                    <?php 
                    if ($seo_description) {
                        echo esc_html($seo_description);
                    } else {
                        echo '<em style="color: #999;">Описание не задано</em>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#upload_seo_image_button').click(function(e) {
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            
            mediaUploader = wp.media({
                title: 'Выберите изображение для Open Graph',
                button: { text: 'Использовать это изображение' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#seo_image').val(attachment.id);
                $('#seo_image_preview').html('<img src="' + attachment.url + '" style="width: 100%; height: 100%; object-fit: cover;" />');
                $('#remove_seo_image_button').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove_seo_image_button').click(function(e) {
            e.preventDefault();
            $('#seo_image').val('');
            
            <?php if ($default_image_url): ?>
                $('#seo_image_preview').html('<img src="<?php echo esc_url($default_image_url); ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;" /><div style="position: absolute; bottom: 5px; left: 5px; font-size: 11px; color: #666; background: rgba(255,255,255,0.9); padding: 2px 5px; border-radius: 3px;">по умолчанию</div>');
            <?php else: ?>
                $('#seo_image_preview').html('<span style="color: #999; font-size: 12px;">Нет изображения</span>');
            <?php endif; ?>
            
            $(this).hide();
        });
        
        function updateCounter(input, counter, status) {
            const length = input.val().length;
            counter.text(length);
            
            let statusText = '', statusColor = '#666';
            
            if (input.attr('id') === 'seo_title') {
                if (length === 0) { statusText = 'Будет использован заголовок страницы'; statusColor = '#999'; }
                else if (length < 40) { statusText = '⚠️ Слишком короткий'; statusColor = '#d63638'; }
                else if (length <= 60) { statusText = '✓ Оптимально'; statusColor = '#00a32a'; }
                else if (length <= 70) { statusText = '⚠️ Немного длинный'; statusColor = '#dba617'; }
                else { statusText = '❌ Слишком длинный'; statusColor = '#d63638'; }
            } else {
                if (length === 0) { statusText = 'Описание не задано'; statusColor = '#999'; }
                else if (length < 100) { statusText = '⚠️ Слишком короткое'; statusColor = '#d63638'; }
                else if (length <= 160) { statusText = '✓ Оптимально'; statusColor = '#00a32a'; }
                else if (length <= 200) { statusText = '⚠️ Немного длинное'; statusColor = '#dba617'; }
                else { statusText = '❌ Слишком длинное'; statusColor = '#d63638'; }
            }
            
            status.text(statusText).css('color', statusColor);
        }
        
        function updatePreview() {
            const title = $('#seo_title').val() || '<?php echo esc_js($default_title); ?>';
            const description = $('#seo_description').val() || 'Описание не задано';
            
            $('#preview_title').text(title);
            
            if ($('#seo_description').val()) {
                $('#preview_description').html(description);
            } else {
                $('#preview_description').html('<em style="color: #999;">Описание не задано</em>');
            }
        }
        
        const titleInput = $('#seo_title'), titleCounter = $('#seo_title_counter'), titleStatus = $('#seo_title_status');
        const descInput = $('#seo_description'), descCounter = $('#seo_description_counter'), descStatus = $('#seo_description_status');
        
        updateCounter(titleInput, titleCounter, titleStatus);
        updateCounter(descInput, descCounter, descStatus);
        
        titleInput.on('input', function() { updateCounter(titleInput, titleCounter, titleStatus); updatePreview(); });
        descInput.on('input', function() { updateCounter(descInput, descCounter, descStatus); updatePreview(); });
    });
    </script>
    
    <?php
}

function get_default_seo_image($post_id) {
    $post_type = get_post_type($post_id);
    
    if ($post_type === 'portfolio') {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) return wp_get_attachment_url($thumbnail_id);
    }
    
    if (in_array($post_type, array('post', 'page', 'news', 'services', 'product'))) {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) return wp_get_attachment_url($thumbnail_id);
    }
    
    return null;
}

function save_seo_meta_fields($post_id) {
    if (!isset($_POST['seo_meta_box_nonce_field'])) return;
    if (!wp_verify_nonce($_POST['seo_meta_box_nonce_field'], 'seo_meta_box_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['seo_title'])) {
        update_post_meta($post_id, '_seo_title', sanitize_text_field($_POST['seo_title']));
    }
    
    if (isset($_POST['seo_description'])) {
        update_post_meta($post_id, '_seo_description', sanitize_textarea_field($_POST['seo_description']));
    }
    
    if (isset($_POST['seo_image'])) {
        update_post_meta($post_id, '_seo_image', absint($_POST['seo_image']));
    }
}
add_action('save_post', 'save_seo_meta_fields');

// ============================================================================
// РЕГИСТРАЦИЯ ХУКОВ ДЛЯ ТАКСОНОМИЙ
// ============================================================================

function add_taxonomy_seo_fields() {
    $taxonomies = array('category', 'post_tag', 'product_cat', 'product_tag', 'product_brand', 'complex_design', 'portfolio_category');
    
    foreach ($taxonomies as $taxonomy) {
        $priority = ($taxonomy === 'complex_design') ? 999 : 10;
        
        add_action("{$taxonomy}_add_form_fields", 'render_taxonomy_seo_fields_add', $priority);
        add_action("{$taxonomy}_edit_form_fields", 'render_taxonomy_seo_fields_edit', $priority);
        add_action("created_{$taxonomy}", 'save_taxonomy_seo_fields', 10);
        add_action("edited_{$taxonomy}", 'save_taxonomy_seo_fields', 10);
    }
}
add_action('init', 'add_taxonomy_seo_fields', 999);

// ============================================================================
// РЕНДЕР ПОЛЕЙ - ФОРМА СОЗДАНИЯ
// ============================================================================

function render_taxonomy_seo_fields_add($taxonomy) {
    ?>
    <div class="form-field">
        <h3 style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #0073aa;">🔍 SEO Настройки</h3>
    </div>
    
    <div class="form-field">
        <label for="term_seo_title">SEO Title</label>
        <input type="text" id="term_seo_title" name="term_seo_title" value="" maxlength="70" style="width: 95%;" />
        <p>Оптимальная длина: 50-60 символов. Если пусто, будет использовано название термина.</p>
    </div>
    
    <div class="form-field">
        <label for="term_seo_description">Meta Description</label>
        <textarea id="term_seo_description" name="term_seo_description" rows="3" maxlength="320" style="width: 95%;"></textarea>
        <p>Оптимальная длина: 150-160 символов.</p>
    </div>
    
    <div class="form-field">
        <label>Open Graph изображение</label>
        <div style="margin-top: 10px;">
            <input type="hidden" id="term_seo_image" name="term_seo_image" value="" />
            <div id="term_seo_image_preview" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                <span style="color: #999; font-size: 12px;">Нет изображения</span>
            </div>
            <button type="button" class="button" id="term_upload_seo_image_button">Выбрать изображение</button>
            <button type="button" class="button" id="term_remove_seo_image_button" style="display:none;">Удалить</button>
        </div>
        <p>Рекомендуемый размер: 1200×630 px. Используется в соцсетях и мессенджерах.</p>
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#term_upload_seo_image_button').click(function(e) {
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            
            mediaUploader = wp.media({
                title: 'Выберите изображение для Open Graph',
                button: { text: 'Использовать это изображение' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#term_seo_image').val(attachment.id);
                $('#term_seo_image_preview').html('<img src="' + attachment.url + '" style="width: 100%; height: 100%; object-fit: cover;" />');
                $('#term_remove_seo_image_button').show();
            });
            
            mediaUploader.open();
        });
        
        $('#term_remove_seo_image_button').click(function(e) {
            e.preventDefault();
            $('#term_seo_image').val('');
            $('#term_seo_image_preview').html('<span style="color: #999; font-size: 12px;">Нет изображения</span>');
            $(this).hide();
        });
    });
    </script>
    <?php
}

// ============================================================================
// РЕНДЕР ПОЛЕЙ - ФОРМА РЕДАКТИРОВАНИЯ
// ============================================================================

function render_taxonomy_seo_fields_edit($term) {
    $seo_title = get_term_meta($term->term_id, 'seo_title', true);
    $seo_description = get_term_meta($term->term_id, 'seo_description', true);
    $seo_image = get_term_meta($term->term_id, 'seo_image', true);
    
    $term_link = get_term_link($term);
    if (is_wp_error($term_link)) $term_link = home_url('/');
    ?>
    
    <tr class="form-field">
        <th colspan="2" style="padding-left: 0;">
            <h3 style="margin: 30px 0 15px 0; padding-top: 20px; border-top: 3px solid #0073aa; font-size: 18px;">🔍 SEO Настройки</h3>
        </th>
    </tr>
    
    <tr class="form-field">
        <th scope="row" style="padding: 15px 10px;">
            <label for="term_seo_title">SEO Title</label>
        </th>
        <td style="padding: 15px 10px;">
            <input type="text" id="term_seo_title" name="term_seo_title" value="<?php echo esc_attr($seo_title); ?>" 
                maxlength="70" style="width: 100%; padding: 8px; font-size: 14px;" placeholder="<?php echo esc_attr($term->name); ?>" />
            <p class="description">
                Оптимальная длина: 50-60 символов. Если пусто, будет использовано название термина.
                <br><strong>Текущая длина: <span id="term_seo_title_length"><?php echo strlen($seo_title); ?></span> символов</strong>
            </p>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row" style="padding: 15px 10px;">
            <label for="term_seo_description">Meta Description</label>
        </th>
        <td style="padding: 15px 10px;">
            <textarea id="term_seo_description" name="term_seo_description" rows="3" maxlength="320" 
                style="width: 100%; padding: 8px; font-size: 14px;"><?php echo esc_textarea($seo_description); ?></textarea>
            <p class="description">
                Оптимальная длина: 150-160 символов.
                <br><strong>Текущая длина: <span id="term_seo_desc_length"><?php echo strlen($seo_description); ?></span> символов</strong>
            </p>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row" style="padding: 15px 10px; vertical-align: top;">
            <label>Open Graph изображение</label>
        </th>
        <td style="padding: 15px 10px;">
            <input type="hidden" id="term_seo_image" name="term_seo_image" value="<?php echo esc_attr($seo_image); ?>" />
            
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <div id="term_seo_image_preview" style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden; width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                    <?php if ($seo_image): 
                        $image_url = wp_get_attachment_url($seo_image);
                    ?>
                        <img src="<?php echo esc_url($image_url); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                    <?php else: ?>
                        <span style="color: #999; font-size: 12px;">Нет изображения</span>
                    <?php endif; ?>
                </div>
                
                <div style="flex: 1;">
                    <button type="button" class="button" id="term_upload_seo_image_button" style="margin-bottom: 8px;">
                        Выбрать изображение
                    </button>
                    
                    <button type="button" class="button" id="term_remove_seo_image_button" style="margin-bottom: 8px; <?php echo $seo_image ? '' : 'display:none;'; ?>">
                        Удалить изображение
                    </button>
                    
                    <p class="description" style="margin: 0;">
                        Рекомендуемый размер: 1200×630 px<br>Используется в соцсетях и мессенджерах.
                    </p>
                </div>
            </div>
        </td>
    </tr>
    
    <tr class="form-field">
        <th colspan="2" style="padding: 15px 10px;">
            <div class="seo-preview" style="background: #f9f9f9; padding: 15px; border-radius: 4px; border-left: 4px solid #0073aa;">
                <h4 style="margin: 0 0 10px 0; font-size: 13px; color: #0073aa;">Предпросмотр в поисковой выдаче</h4>
                <div style="font-family: Arial, sans-serif;">
                    <div id="term_preview_title" style="color: #1a0dab; font-size: 18px; margin-bottom: 5px; line-height: 1.2;">
                        <?php echo esc_html($seo_title ?: $term->name); ?>
                    </div>
                    <div id="term_preview_url" style="color: #006621; font-size: 14px; margin-bottom: 5px;">
                        <?php echo esc_url($term_link); ?>
                    </div>
                    <div id="term_preview_description" style="color: #545454; font-size: 13px; line-height: 1.4;">
                        <?php 
                        if ($seo_description) {
                            echo esc_html($seo_description);
                        } else {
                            echo '<em style="color: #999;">Описание не задано</em>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </th>
    </tr>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#term_upload_seo_image_button').click(function(e) {
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            
            mediaUploader = wp.media({
                title: 'Выберите изображение для Open Graph',
                button: { text: 'Использовать это изображение' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#term_seo_image').val(attachment.id);
                $('#term_seo_image_preview').html('<img src="' + attachment.url + '" style="width: 100%; height: 100%; object-fit: cover;" />');
                $('#term_remove_seo_image_button').show();
            });
            
            mediaUploader.open();
        });
        
        $('#term_remove_seo_image_button').click(function(e) {
            e.preventDefault();
            $('#term_seo_image').val('');
            $('#term_seo_image_preview').html('<span style="color: #999; font-size: 12px;">Нет изображения</span>');
            $(this).hide();
        });
        
        function updatePreview() {
            const title = $('#term_seo_title').val() || '<?php echo esc_js($term->name); ?>';
            const description = $('#term_seo_description').val();
            
            $('#term_preview_title').text(title);
            
            if (description) {
                $('#term_preview_description').html(description);
            } else {
                $('#term_preview_description').html('<em style="color: #999;">Описание не задано</em>');
            }
        }
        
        $('#term_seo_title').on('input', function() {
            $('#term_seo_title_length').text($(this).val().length);
            updatePreview();
        });
        
        $('#term_seo_description').on('input', function() {
            $('#term_seo_desc_length').text($(this).val().length);
            updatePreview();
        });
    });
    </script>
    <?php
}

// ============================================================================
// СОХРАНЕНИЕ ПОЛЕЙ ТАКСОНОМИЙ
// ============================================================================

function save_taxonomy_seo_fields($term_id) {
    if (isset($_POST['term_seo_title'])) {
        update_term_meta($term_id, 'seo_title', sanitize_text_field($_POST['term_seo_title']));
    }
    
    if (isset($_POST['term_seo_description'])) {
        update_term_meta($term_id, 'seo_description', sanitize_textarea_field($_POST['term_seo_description']));
    }
    
    if (isset($_POST['term_seo_image'])) {
        update_term_meta($term_id, 'seo_image', absint($_POST['term_seo_image']));
    }
}

function output_seo_meta_tags() {
    $seo_title = '';
    $seo_description = '';
    $seo_image = '';
    $site_name = get_bloginfo('name');
    
    // Для страниц таксономий (категории, теги и т.д.)
    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        $seo_title = get_term_meta($term->term_id, 'seo_title', true);
        $seo_description = get_term_meta($term->term_id, 'seo_description', true);
        $seo_image_id = get_term_meta($term->term_id, 'seo_image', true);
        
        if (!$seo_title) {
            $seo_title = $term->name . ' - ' . $site_name;
        }
        
        if ($seo_image_id) {
            $seo_image = wp_get_attachment_url($seo_image_id);
        }
    }
    // Для архивных страниц типов записей
    elseif (is_post_type_archive()) {
        $post_type = get_post_type();
        $archive_key = '';
        
        // Определяем ключ архива
        if ($post_type === 'post') $archive_key = 'blog';
        elseif ($post_type === 'portfolio') $archive_key = 'portfolio';
        elseif ($post_type === 'news') $archive_key = 'news';
        elseif ($post_type === 'services') $archive_key = 'services';
        elseif ($post_type === 'product') $archive_key = 'products';
        
        if ($archive_key) {
            $seo_title = get_option("archive_seo_title_{$archive_key}", '');
            $seo_description = get_option("archive_seo_description_{$archive_key}", '');
            $seo_image_id = get_option("archive_seo_image_{$archive_key}", '');
            
            if (!$seo_title) {
                $post_type_obj = get_post_type_object($post_type);
                $seo_title = $post_type_obj->labels->name . ' - ' . $site_name;
            }
            
            if ($seo_image_id) {
                $seo_image = wp_get_attachment_url($seo_image_id);
            }
        }
    }
    // Для страницы магазина WooCommerce
    elseif (function_exists('is_shop') && is_shop()) {
        $seo_title = get_option('archive_seo_title_shop', '');
        $seo_description = get_option('archive_seo_description_shop', '');
        $seo_image_id = get_option('archive_seo_image_shop', '');
        
        if (!$seo_title) {
            $seo_title = 'Магазин - ' . $site_name;
        }
        
        if ($seo_image_id) {
            $seo_image = wp_get_attachment_url($seo_image_id);
        }
    }
    // Для отдельных постов, страниц, товаров и т.д.
    elseif (is_singular()) {
        global $post;
        $seo_title = get_post_meta($post->ID, '_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_seo_description', true);
        $seo_image_id = get_post_meta($post->ID, '_seo_image', true);
        
        if (!$seo_title) {
            $seo_title = get_the_title() . ' - ' . $site_name;
        }
        
        if ($seo_image_id) {
            $seo_image = wp_get_attachment_url($seo_image_id);
        } else {
            $seo_image = get_default_seo_image($post->ID);
        }
    }
    // Для главной страницы
    elseif (is_front_page() || is_home()) {
        $seo_title = get_bloginfo('name') . ' - ' . get_bloginfo('description');
        $seo_description = get_bloginfo('description');
    }
    
    // Если нет изображения, используем логотип сайта
    if (!$seo_image) {
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $seo_image = wp_get_attachment_url($custom_logo_id);
        }
    }
    
    // Выводим мета-теги
    if ($seo_title) {
        echo '<title>' . esc_html($seo_title) . '</title>' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($seo_title) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($seo_title) . '">' . "\n";
    }
    
    if ($seo_description) {
        echo '<meta name="description" content="' . esc_attr($seo_description) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($seo_description) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($seo_description) . '">' . "\n";
    }
    
    if ($seo_image) {
        echo '<meta property="og:image" content="' . esc_url($seo_image) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($seo_image) . '">' . "\n";
    }
    
    // Дополнительные Open Graph теги
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}