<?php
/**
 * Single Product tabs
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter tabs and allow third parties to add their own.
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) : ?>

    <!-- Табуляция -->
    <ul class="nav nav-tabs justify-content-start align-items-center mb-4" id="productTabs" role="tablist">
        <?php $tab_count = 0; ?>
        <?php foreach ( $product_tabs as $key => $product_tab ) : ?>
            <?php if ($tab_count > 0): ?>
                <li class="nav-item">
                    <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/menu-decoration-point.svg" 
                         alt="Декоративная точка - разделитель"
                         class="img-fluid py-3">
                </li>
            <?php endif; ?>
            
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $tab_count === 0 ? 'active' : ''; ?>" 
                        id="<?php echo esc_attr( $key ); ?>-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#<?php echo esc_attr( $key ); ?>" 
                        type="button" 
                        role="tab" 
                        aria-controls="<?php echo esc_attr( $key ); ?>" 
                        aria-selected="<?php echo $tab_count === 0 ? 'true' : 'false'; ?>">
                    <?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
                </button>
            </li>
            <?php $tab_count++; ?>
        <?php endforeach; ?>
    </ul>

    <!-- Содержимое табов -->
    <div class="tab-content tab-content-product" id="productTabsContent">
        <?php $tab_count = 0; ?>
        <?php foreach ( $product_tabs as $key => $product_tab ) : ?>
            <div class="tab-pane fade <?php echo $tab_count === 0 ? 'show active' : ''; ?>" 
                 id="<?php echo esc_attr( $key ); ?>" 
                 role="tabpanel" 
                 aria-labelledby="<?php echo esc_attr( $key ); ?>-tab">
                <div class="row">
                    <div class="col-12">
                        <?php
                        if ( isset( $product_tab['callback'] ) ) {
                            call_user_func( $product_tab['callback'], $key, $product_tab );
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php $tab_count++; ?>
        <?php endforeach; ?>
    </div>

<?php endif; ?>