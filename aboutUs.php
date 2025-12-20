<?php
/*
Template Name: About Us
*/
// Получаем бренды и фильтруем по наличию постов с категорией vt-racing
?>

<?php include 'header.php'; ?>
<?php
// 1️⃣ Берём контент текущей страницы
$content = get_post_field('post_content', get_the_ID());

// 2️⃣ Разбираем контент Gutenberg в массив блоков
$blocks = parse_blocks($content);

// 3️⃣ Функция для вывода конкретного блока по его имени
function render_acf_block($blocks, $block_name)
{
    foreach ($blocks as $block) {
        if ($block['blockName'] === $block_name) {
            echo render_block($block);
        }

        // Рекурсия: ищем вложенные блоки, если есть
        if (! empty($block['innerBlocks'])) {
            render_acf_block($block['innerBlocks'], $block_name);
        }
    }
}
?>

<!-- Full Width Image Block -->
<section id="fullwidth-image">
    <img src="<?php the_field('hero_banner') ?>" alt="Изображение" class="fullwidth-img">
    <div class="fullwidth-content">
        <h2><?php the_field('hero_title') ?></h2>
        <div class="fullwidth-text">
            <p class="fullwidth-phrase"><?php the_field('hero_subtitle1') ?></p>
            <p class="fullwidth-phrase fullwidth-phrase-indent"><?php the_field('hero_subtitle2') ?></p>
            <p class="fullwidth-phrase fullwidth-phrase-last"><?php the_field('hero_subtitle3') ?></p>
        </div>
    </div>
</section>

<?php render_acf_block($blocks, 'acf/breadcrumbs-header'); ?>


<!-- Text Block -->
<section id="text-block">
    <div class="container">
        <div class="text-content">
            <p><?php the_field('AboutUs_info') ?></p>
        </div>
    </div>
</section>

<!-- Company Development Stages Block -->
<section id="development-stages">
    <div class="container">
        <h2><?php the_field('cds_title') ?></h2>
        <div class="red-squares">
            <div class="square"></div>
            <div class="square"></div>
            <div class="square"></div>
        </div>
        <img src="<?php the_field('cds_img') ?>" alt="Этапы развития компании" class="stages-image">
    </div>
</section>

<!-- Equipment Block -->
<section id="equipment">
    <div class="container">
        <h2><?php the_field('equipment_title') ?></h2>
        <div class="red-squares">
            <div class="square"></div>
            <div class="square"></div>
            <div class="square"></div>
        </div>
        <div class="equipment-text">
            <p><?php the_field('equipment_text') ?></p>
        </div>
        <div class="equipment-grid">
            <div class="equipment-item">
                <div class="equipment-image-wrapper">
                    <img src="<?php the_field('equipment_img1') ?>" alt="Бортогиб нового поколения" class="equipment-image">
                </div>
                <div class="equipment-caption">
                    <h3><?php the_field('equipment_title1') ?></h3>
                    <p><?php the_field('equipment_subtitle1') ?></p>
                </div>
            </div>
            <div class="equipment-item">
                <div class="equipment-image-wrapper">
                    <img src="<?php the_field('equipment_img2') ?>" alt="Фрезерно-гравировальный станок" class="equipment-image">
                </div>
                <div class="equipment-caption">
                    <h3><span><?php the_field('equipment_title2') ?></span><?php the_field('equipment_title2_2') ?></h3>
                    <p><?php the_field('equipment_subtitle2') ?></p>
                </div>
            </div>
            <div class="equipment-item">
                <div class="equipment-image-wrapper">
                    <img src="<?php the_field('equipment_img3') ?>" alt="Лазерный станок" class="equipment-image">
                </div>
                <div class="equipment-caption">
                    <h3><span><?php the_field('equipment_title3') ?></span><?php the_field('equipment_title3_3') ?></h3>
                    <p><?php the_field('equipment_subtitle3') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Block -->
<section id="why-choose-us">
    <div class="container">
        <h2><?php the_field('wcu_title') ?></h2>
        <div class="red-squares">
            <div class="square"></div>
            <div class="square"></div>
            <div class="square"></div>
        </div>
        <div class="why-choose-text">
            <p><?php the_field('wcu_text1') ?></p>
            <p><?php the_field('wcu_text2') ?></p>
        </div>
        <div class="benefits-grid">
            <?php if (have_rows('wcu_repeater')): ?>
                <?php while (have_rows('wcu_repeater')): the_row(); ?>
                    <div class="benefit-item">
                        <img src="<?php the_sub_field('wcu_image'); ?>" alt="" class="benefit-image">
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Final Text Block -->
<section id="final-text">
    <div class="container">
        <div class="final-text-content">
            <p><?php the_field('text_block') ?></p>
        </div>
    </div>
</section>

<?php render_acf_block($blocks, 'acf/clients-slider'); ?>

<!-- Company Faces Block -->
<section id="company-faces">
    <div class="container">
        <h2><?php the_field('cf_title') ?></h2>
        <div class="red-squares">
            <div class="square"></div>
            <div class="square"></div>
            <div class="square"></div>
        </div>
        <div class="slider-container">
            <button class="slider-arrow slider-arrow-left" id="prevBtn">‹</button>
            <div class="slider-wrapper">
                <div class="slider" id="slider">
                    <?php if (have_rows('cf_repeater')): ?>
                        <?php while (have_rows('cf_repeater')): the_row(); ?>
                            <div class="slide">
                                <div class="person-image-container">
                                    <img src="<?php the_sub_field('cf_image'); ?>" alt="" class="person-image">
                                </div>
                                <div class="person-info">
                                    <h3 class="person-name"><?php the_sub_field('cf_name'); ?></h3>
                                    <p class="person-title"><?php the_sub_field('cf_title'); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="slider-arrow slider-arrow-right" id="nextBtn">›</button>
        </div>
    </div>
</section>

<!-- Our Production Block -->
<section id="our-production">
    <div class="container">
        <h2><?php the_field('production_title') ?></h2>
        <div class="red-squares">
            <div class="square"></div>
            <div class="square"></div>
            <div class="square"></div>
        </div>
        <div class="production-flex">
            <div class="production-block production-block-large production-block-purple">
                <img src="<?php the_field('production_img1') ?>" alt="Наше производство" class="production-block-image">
            </div>
            <div class="production-right">
                <div class="production-top">
                    <div class="production-block production-block-yellow">
                        <img src="<?php the_field('production_img2') ?>" alt="Наше производство" class="production-block-image">
                    </div>
                    <div class="production-block production-block-pink">
                        <img src="<?php the_field('production_img3') ?>" alt="Наше производство" class="production-block-image">
                    </div>
                </div>
                <div class="production-bottom">
                    <div class="production-block production-block-teal">
                        <img src="<?php the_field('production_img4') ?>" alt="Наше производство" class="production-block-image">
                    </div>
                    <div class="production-block production-block-blue">
                        <img src="<?php the_field('production_img5') ?>" alt="Наше производство" class="production-block-image">
                    </div>
                    <div class="production-block production-block-dark">
                        <img src="<?php the_field('production_img6') ?>" alt="Наше производство" class="production-block-image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest News/Articles Block -->
<section id="latest-news">
    <div class="container">
        <h2><?php the_field('na_title') ?></h2>
        <div class="red-squares">
            <div class="square"></div>
            <div class="square"></div>
            <div class="square"></div>
        </div>
        <div class="news-navigation">
            <a href="#" class="nav-item active"><?php the_field('na_subtitle1') ?></a>
            <div class="nav-separator"></div>
            <a href="#" class="nav-item"><?php the_field('na_subtitle2') ?></a>
        </div>
        <div class="news-grid">
            <?php if (have_rows('news_repeater')): ?>
                <?php while (have_rows('news_repeater')): the_row(); ?>
                    <a href="<?php the_sub_field('news_link'); ?>" class="news-card">
                        <div class="news-image-container">
                            <img src="<?php the_sub_field('news_image'); ?>" alt="" class="news-image">
                        </div>
                        <div class="news-content">
                            <h3 class="news-title"><?php the_sub_field('news_title'); ?></h3>
                            <p class="news-description"><?php the_sub_field('news_description'); ?></p>
                            <span class="news-date"><?php the_sub_field('news_date'); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="news-button-container">
            <?php
            $link = get_field('news_btn_link');
            if ($link):
                $link_url = $link['url'];
                $link_title = $link['title'];
            ?>
                <a href="<?php echo esc_url($link_url); ?>" class="news-button"><?php echo esc_html($link_title); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php render_acf_block($blocks, 'acf/portfolio-slider'); ?>

<?php render_acf_block($blocks, 'acf/faq'); ?>

<?php render_acf_block($blocks, 'acf/not-found-product'); ?>

<?php include 'footer.php'; ?>
<style>
    /* Full Width Image Block */
    #fullwidth-image {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    #fullwidth-image .fullwidth-img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    #fullwidth-image .fullwidth-content {
        position: absolute;
        top: 12vw;
        left: 25.8vw;
        transform: translateY(-50%);
        z-index: 2;
        max-width: 50%;
    }

    #fullwidth-image h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: 2.6vw;
        color: #000;
        margin-bottom: 3.7vw;
        margin-left: 0.8vw;
        margin-top: -1.1vw; 
    }

    #fullwidth-image .fullwidth-text {
        display: flex;
        flex-direction: column;
        gap: 0.6vw;
    }

    #fullwidth-image .fullwidth-text p {
        font-size: 1.7vw !important;
    }

    #fullwidth-image .fullwidth-phrase {
        font-family: 'Gilroy', sans-serif;
        color: #EB3549;
        margin: 0;
        font-weight: 300;
        line-height: 1.2;
    }

    #fullwidth-image .fullwidth-phrase-indent {
        margin-left: 7.4vw;
    }

    #fullwidth-image .fullwidth-phrase-last {
        margin-left: 1.3vw;
    }

    /* Text Block */
    #text-block {
        padding: 3.75rem 0;
        background-color: #ffffff;
    }

    #text-block .container {
        max-width: 84.4rem;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    #text-block .text-content {
        width: 100%;
    }

    #text-block p {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1rem, 0.909rem + 0.45vw, 1.25rem);
        line-height: 1.35;
        color: #333333;
        text-align: justify;
        font-weight: 300;
    }


    /* Responsive Design */


    @media (max-width: 30rem) {

        #text-block {
            padding: 1.875rem 0;
        }

        #text-block .container {
            padding: 0 0.9375rem;
        }

        #text-block p {
            font-size: 0.875rem;
            line-height: 1.6;
        }
    }

    /* Company Development Stages Block */
    #development-stages {
        padding: 2rem 0;
        background-color: #F5F5F5;
    }

    #development-stages .container {
        max-width: 84.4rem;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    #development-stages h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.6rem);
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 1.25rem;
    }

    #development-stages .red-squares {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 3.125rem;
    }

    #development-stages .square {
        width: 0.7rem;
        height: 0.7rem;
        background-color: #DA2B1A;
        border-radius: 3px;
    }

    #development-stages .stages-image {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Responsive Design for Development Stages */
    @media (max-width: 48rem) {
        #development-stages {
            padding: 2.5rem 0;
        }

        #development-stages h2 {
            margin-bottom: 2rem;
        }
    }

    @media (max-width: 30rem) {
        #development-stages {
            padding: 1.875rem 0;
        }

        #development-stages .container {
            padding: 0 0.9375rem;
        }

        #development-stages h2 {
            margin-bottom: 1.5rem;
        }
    }

    /* Equipment Block */
    #equipment {
        padding: 2rem 0;
        background-color: #ffffff;
        margin-top: 5rem;
    }

    #equipment .container {
        max-width: 85.8rem;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    #equipment h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.6rem);
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 1.25rem;
    }

    #equipment .red-squares {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 5.125rem;
    }

    #equipment .square {
        width: 0.7rem;
        height: 0.7rem;
        background-color: #DA2B1A;
        border-radius: 3px;
    }

    #equipment .equipment-text {
        margin-bottom: 7.125rem;
    }

    #equipment .equipment-text p {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1rem, 0.909rem + 0.45vw, 1.25rem);
        line-height: 1.35;
        color: #333333;
        text-align: justify;
        font-weight: 300;
    }

    #equipment .equipment-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    #equipment .equipment-item {
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: #ffffff;
    }

    #equipment .equipment-image-wrapper {
        width: 99%;
        overflow: hidden;
        border: 1px solid #757272;
        border-radius: 10px;
    }

    #equipment .equipment-image {
        width: 100%;
        height: auto;
        display: block;
    }

    #equipment .equipment-caption {
        padding: 1.25rem 0;
        text-align: center;
    }

    #equipment .equipment-caption h3 {
        font-family: 'Gilroy', sans-serif;
        font-size: 1rem;
        font-weight: 300;
        color: #000;
        margin-bottom: 0.5rem;
    }

    #equipment .equipment-caption span {
        color: #DA2B1A;
    }

    #equipment .equipment-caption p {
        font-family: 'Gilroy', sans-serif;
        font-size: 0.875rem;
        color: #666666;
        font-weight: 300;
    }

    /* Responsive Design for Equipment */
    @media (max-width: 48rem) {
        #equipment {
            padding: 2.5rem 0;
        }

        #equipment .equipment-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }


        #equipment .equipment-text p {
            font-size: 0.9375rem;
            line-height: 1.7;
        }
    }

    @media (max-width: 30rem) {
        #equipment {
            padding: 1.875rem 0;
        }

        #equipment .container {
            padding: 0 0.9375rem;
        }

        #equipment .equipment-text p {
            font-size: 0.875rem;
            line-height: 1.6;
        }
    }

    /* Why Choose Us Block */
    #why-choose-us {
        padding: 2rem 0;
        background-color: #F5F5F5;
    }

    #why-choose-us .container {
        max-width: 85.8rem;
        margin: 0 auto;
        padding: 0 1.25rem;
        margin-top: 3rem;
    }

    #why-choose-us h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.6rem);
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 1.25rem;
    }

    #why-choose-us .red-squares {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 5.125rem;
    }

    #why-choose-us .square {
        width: 0.7rem;
        height: 0.7rem;
        background-color: #DA2B1A;
        border-radius: 3px;
    }

    #why-choose-us .why-choose-text {
        margin-bottom: 5.125rem;
        max-width: 79rem;
    }

    #why-choose-us .why-choose-text p {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1rem, 0.909rem + 0.45vw, 1.25rem);
        line-height: 1.35;
        color: #333333;
        text-align: justify;
        font-weight: 300;
        margin-bottom: 1.5rem;
    }

    #why-choose-us .why-choose-text p:last-child {
        margin-bottom: 0;
    }

    #why-choose-us .benefits-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        margin-bottom: 4.325rem;
    }

    #why-choose-us .benefit-item {
        overflow: hidden;
        flex: 0 0 auto;
    }

    #why-choose-us .benefit-image {
        width: 12rem;
        height: auto;
        display: block;
    }

    /* Responsive Design for Why Choose Us */
    @media (max-width: 48rem) {
        #why-choose-us {
            padding: 2.5rem 0;
        }

        #why-choose-us .benefit-image {
            width: 10rem;
        }

        #why-choose-us .why-choose-text p {
            font-size: 0.9375rem;
            line-height: 1.7;
        }
    }

    @media (max-width: 30rem) {
        #why-choose-us {
            padding: 1.875rem 0;
        }

        #why-choose-us .container {
            padding: 0 0.9375rem;
        }

        #why-choose-us .benefit-image {
            width: 8rem;
        }

        #why-choose-us .why-choose-text p {
            font-size: 0.875rem;
            line-height: 1.6;
        }
    }

    /* Company Faces Block */
    #company-faces {
        padding: 2rem 0;
        background-color: #ffffff;
    }

    #company-faces .container {
        max-width: 100rem;
        margin: 0 auto;
        padding: 0 1.25rem;
        margin-bottom: 4rem;
    }

    #company-faces h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.6rem);
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 1.25rem;
    }

    #company-faces .red-squares {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 3.125rem;
    }

    #company-faces .square {
        width: 0.7rem;
        height: 0.7rem;
        background-color: #DA2B1A;
        border-radius: 3px;
    }

    #company-faces .slider-container {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #company-faces .slider-wrapper {
        overflow: hidden;
        width: 100%;
        max-width: 77rem;
    }

    #company-faces .slider {
        display: flex;
        transition: transform 0.3s ease;
        width: 100%;
    }

    #company-faces .slide {
        flex: 0 0 25%;
        padding: 0 1.3rem;
        display: flex;
        flex-direction: column;
    }

    #company-faces .person-image-container {
        border: 1px solid #757272;
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: #ffffff;
    }

    #company-faces .person-image {
        width: 100%;
        height: 17rem;
        object-fit: cover;
        display: block;
    }

    #company-faces .person-info {
        background-color: #ffffff;
        margin-top: 0.5rem;
        padding: 0 22.4px;
    }

    #company-faces .person-name {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.5rem);
        font-weight: 600;
        color: #000;
        margin-bottom: 0.5rem;
    }

    #company-faces .person-title {
        font-family: 'Gilroy', sans-serif;
        font-size: 1.075rem;
        color: #666666;
        font-weight: 300;
        margin: 0;
    }

    #company-faces .slider-arrow {
        background: none;
        border: none;
        font-size: 4rem;
        color: #DA2B1A;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 6rem;
        height: 6rem;
        border-radius: 50%;
        transition: background-color 0.3s ease;
        font-weight: 100;
    }

    /* Responsive Design for Company Faces */
    @media (max-width: 48rem) {
        #company-faces {
            padding: 2.5rem 0;
        }

        #company-faces .slide {
            flex: 0 0 50%;
        }

        #company-faces .slider-container {
            gap: 1rem;
        }

        #company-faces .person-image {
            height: 10rem;
        }
    }

    @media (max-width: 30rem) {
        #company-faces {
            padding: 1.875rem 0;
        }

        #company-faces .container {
            padding: 0 0.9375rem;
        }

        #company-faces .slide {
            flex: 0 0 100%;
        }

        #company-faces .slider-container {
            gap: 0.5rem;
        }

        #company-faces .person-image {
            height: 8rem;
        }

        #company-faces .slider-arrow {
            font-size: 1.5rem;
            width: 2.5rem;
            height: 2.5rem;
        }
    }


    /* Final Text Block */
    #final-text {
        padding: 2rem 0;
        background-color: #ffffff;
    }

    #final-text .container {
        max-width: 80.8rem;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    #final-text .final-text-content {
        width: 100%;
    }

    #final-text p {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1rem, 0.909rem + 0.45vw, 1.25rem);
        line-height: 1.35;
        color: #333333;
        text-align: justify;
        font-weight: 300;
    }

    /* Responsive Design for Final Text */
    @media (max-width: 48rem) {
        #final-text {
            padding: 2.5rem 0;
        }

        #final-text p {
            font-size: 0.9375rem;
            line-height: 1.7;
        }
    }

    @media (max-width: 30rem) {
        #final-text {
            padding: 1.875rem 0;
        }

        #final-text .container {
            padding: 0 0.9375rem;
        }

        #final-text p {
            font-size: 0.875rem;
            line-height: 1.6;
        }
    }

    /* Our Production Block */
    #our-production {
        padding-top: 2rem;
        background-color: #F5F5F5;
    }

    #our-production .container {
        max-width: 100%;
        margin: 0 auto;
        margin-top: 4.5rem;
        box-sizing: border-box;
    }

    #our-production h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.6rem);
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 1.25rem;
    }

    #our-production .red-squares {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 3.125rem;
    }

    #our-production .square {
        width: 0.7rem;
        height: 0.7rem;
        background-color: #DA2B1A;
        border-radius: 3px;
    }

    #our-production .production-flex {
        display: flex;
        gap: 0.5rem;
        height: 60rem;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }

    #our-production .production-right {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }

    #our-production .production-top {
        display: flex;
        gap: 0.5rem;
        flex: 1;
    }

    #our-production .production-bottom {
        display: flex;
        gap: 0.5rem;
        flex: 1;
    }

    #our-production .production-block {
        border-radius: 0.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        min-height: 8rem;
    }

    #our-production .production-block-large {
        flex: 0 0 45%;
    }

    #our-production .production-block-yellow {
        flex: 0 0 50%;
    }

    #our-production .production-block-pink {
        flex: 0 0 50%;
    }

    #our-production .production-block-teal {
        flex: 0 0 33.33%;
    }

    #our-production .production-block-blue {
        flex: 0 0 33.33%;
    }

    #our-production .production-block-dark {
        flex: 0 0 33.33%;
    }

    #our-production .production-block-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Responsive Design for Our Production */

    @media (max-width: 1600px) {
        #our-production .production-flex {
            height: 33rem;
        }
    }

    @media (max-width: 1600px) {
        #our-production .production-flex {
            height: 26rem;
        }
    }


    @media (max-width: 1000px) {
        #our-production .production-flex {
            height: 20rem;
        }

        #our-production .production-block {
            min-height: 6rem;
        }

        #our-production .production-block-large {
            flex: 0 0 50%;
        }

        #our-production .production-block-yellow,
        #our-production .production-block-pink {
            flex: 0 0 50%;
        }

        #our-production .production-block-teal,
        #our-production .production-block-blue,
        #our-production .production-block-dark {
            flex: 0 0 33.33%;
        }
    }

    @media (max-width: 48rem) {
        #our-production {
            padding: 2.5rem 0;
        }

        #our-production .production-flex {
            flex-direction: column;
            height: auto;
            gap: 1rem;
        }

        #our-production .production-block {
            min-height: 12rem;
        }

        #our-production .production-block-large {
            flex: 1 1 100%;
        }

        #our-production .production-right {
            flex-direction: column;
        }

        #our-production .production-top,
        #our-production .production-bottom {
            flex-direction: column;
        }

        #our-production .production-block-yellow,
        #our-production .production-block-pink,
        #our-production .production-block-teal,
        #our-production .production-block-blue,
        #our-production .production-block-dark {
            flex: 1 1 100%;
        }
    }

    @media (max-width: 30rem) {
        #our-production {
            padding: 1.875rem 0;
        }

        #our-production .container {
            padding: 0 0.9375rem;
        }

        #our-production .production-flex {
            flex-direction: column;
            height: auto;
        }

        #our-production .production-block {
            min-height: 6rem;
        }

        #our-production .production-block-large {
            flex: 1;
        }
    }

    /* Latest News/Articles Block */
    #latest-news {
        padding: 2rem 0;
        background-color: #ffffff;
    }

    #latest-news .container {
        max-width: 85.8rem;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    #latest-news h2 {
        font-family: 'Gilroy', sans-serif;
        font-size: clamp(1.5rem, 1.25rem + 1.25vw, 1.6rem);
        font-weight: 600;
        color: #000;
        text-align: center;
        margin-bottom: 1.25rem;
    }

    #latest-news .red-squares {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 3.125rem;
    }

    #latest-news .square {
        width: 0.7rem;
        height: 0.7rem;
        background-color: #DA2B1A;
        border-radius: 3px;
    }

    #latest-news .news-navigation {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-bottom: 3.125rem;
    }

    #latest-news .nav-item {
        font-family: 'Gilroy', sans-serif;
        font-size: 1rem;
        font-weight: 500;
        color: #000;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    #latest-news .nav-item.active {
        color: #DA2B1A;
    }

    #latest-news .nav-item:hover {
        color: #DA2B1A;
    }

    #latest-news .nav-separator {
        width: 0.5rem;
        height: 0.5rem;
        background-color: #DA2B1A;
        border-radius: 50%;
    }

    #latest-news .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 3rem;
        margin-bottom: 3.125rem;
    }

    #latest-news .news-card {
        background-color: #F5F5F5;
        border-radius: 0.5rem;
        overflow: hidden;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: block;
    }

    #latest-news .news-image-container {
        width: 100%;
        height: 21rem;
        overflow: hidden;
    }

    #latest-news .news-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    #latest-news .news-content {
        padding: 1.25rem;
    }

    #latest-news .news-title {
        font-family: 'Gilroy', sans-serif;
        font-size: 1.125rem;
        font-weight: 600;
        color: #000;
        margin-bottom: -0.65rem;
        line-height: 1.3;
    }

    #latest-news .news-description {
        font-family: 'Gilroy', sans-serif;
        font-size: 1.175rem;
        color: #666666;
        line-height: 1.5;
        margin-bottom: 1rem;
        font-weight: 300;
    }

    #latest-news .news-date {
        font-family: 'Gilroy', sans-serif;
        font-size: 1.2rem;
        color: #999999;
        font-weight: 300;
    }

    #latest-news .news-button-container {
        display: flex;
        justify-content: center;
    }

    #latest-news .news-button {
        background: linear-gradient(90deg, #DA2B1A 0%, #FF6B35 100%);
        color: #ffffff;
        font-family: 'Gilroy', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 0.4rem;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: inline-block;
        border: 1px solid transparent;
    }

    #latest-news .news-button:hover {
        background: transparent;
        color: #e85122;
        border-color: #e85122
    }


    /* Responsive Design for Latest News */
    @media (max-width: 48rem) {
        #latest-news {
            padding: 2.5rem 0;
        }

        #latest-news .news-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        #latest-news .news-image-container {
            height: 25rem;
        }

        #latest-news .news-content {
            padding: 1rem;
        }
    }

    @media (max-width: 30rem) {
        #latest-news {
            padding: 1.875rem 0;
        }

        #latest-news .container {
            padding: 0 0.9375rem;
        }

        #latest-news .news-navigation {
            gap: 0.75rem;
        }

        #latest-news .nav-item {
            font-size: 0.875rem;
        }

        #latest-news .news-image-container {
            height: 8rem;
        }

        #latest-news .news-content {
            padding: 0.875rem;
        }

        #latest-news .news-title {
            font-size: 1rem;
        }

        #latest-news .news-description {
            font-size: 0.8125rem;
        }

        #latest-news .news-button {
            font-size: 0.875rem;
            padding: 0.875rem 1.5rem;
        }
    }
</style>
<script>
    // Company Faces Slider
    let currentSlide = 0;
    const slides = document.querySelectorAll('#company-faces .slide');
    const totalSlides = slides.length;
    const slidesToShow = 4;
    const slider = document.getElementById('slider');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    function updateSlider() {
        const slideWidth = 100 / slidesToShow;
        const translateX = -currentSlide * slideWidth;
        slider.style.transform = `translateX(${translateX}%)`;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % (totalSlides - slidesToShow + 1);
        updateSlider();
    }

    function prevSlide() {
        currentSlide = currentSlide === 0 ? (totalSlides - slidesToShow) : currentSlide - 1;
        updateSlider();
    }

    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);

    // Initialize slider
    updateSlider();
</script>