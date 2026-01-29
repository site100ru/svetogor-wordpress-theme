<?php
/*
* Template Name: PAGE About Us
*/

add_action('wp_head', function () {
?>
    <style>
        .hero-section h1 {
            font-size: clamp(1.25rem, 0.341rem + 4.55vw, 3.75rem);
        }

        .hero-section h2 {
            font-family: var(--second-family);
            font-size: clamp(1.125rem, 0.966rem + 0.8vw, 1.563rem);
            font-weight: 400;
        }

        .card.card-portfolio-aboutUs {
            border-radius: 0;
            height: 100%;
            max-height: 416px;
        }

        .card.card-portfolio-aboutUs .card-img-container,
        .card.card-portfolio-aboutUs .card-img-container img {
            height: 100%;
            max-height: 416px;
            border-radius: 0;
        }
    </style>
<?php
});

get_header();
// Получаем данные текущей страницы
$page_id = get_the_ID();
$page_title = get_the_title();

$hero_bg_id = get_post_meta($page_id, 'page_hero_bg', true);
$hero_bg_url = '';

// Получаем URL фонового изображения
if ($hero_bg_id) {
    $hero_bg_data = wp_get_attachment_image_src($hero_bg_id, 'full');
    if ($hero_bg_data) {
        $hero_bg_url = $hero_bg_data[0];
    }
}

?>

<!-- HERO СЕКЦИЯ -->
<section class="hero-section" <?php if ($hero_bg_url): ?>style="background-image: url('<?php echo esc_url($hero_bg_url); ?>'); height: 500px;" <?php endif; ?>>
    <div class="container position-relative">
        <div class="row">
            <div class="col hero-content">
                <h1><?php echo $page_title; ?></h1>
                <h2>Начали с мечты, достигли успеха - <br>мы на вашей стороне!</h2>
            </div>
        </div>
    </div>
</section>

<!-- ХЛЕБНЫЕ КРОШКИ -->
<section class="section-mini">
    <h2 class=d-none>Секция навигации по сайту</h2>
    <div class="container">
        <?php render_page_breadcrumbs($page_title); ?>
    </div>
</section>

<div class="section">
    <div class="container">
        <div class="row align-items-start justify-content-center background-color-field-text-only">

            <!-- Одна колонка -->
            <div class="col-12 text-start me-auto">
                <div class="text-content">
                    <p>Компания «Светогор-СВ» — это динамично развивающаяся рекламно-производственная компания, основанная в 2008 году. Наш путь начинался скромно: в небольшой офисной комнате с единственным компьютером. Однако благодаря упорству, стремлению к качеству и постоянному развитию, мы смогли вырасти в современное предприятие, обладающее высокотехнологичным оборудованием и производственными площадями более 400 кв. м. Сегодня мы способны самостоятельно производить весь ассортимент нашей продукции, что позволяет нам предлагать клиентам решения, соответствующие самым высоким стандартам.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section section-glide section-clients bg-grey">
    <div class="container">
        <div class="section-title text-center">
            <h2>Этапы развития компании</h2>
            <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="row">
            <div class="col">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/scheme-aboutUs.png" alt="Схема" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<?php
// Массив с картинками и подписями
$portfolio_items = array(
    array(
        'image' => '/assets/img/about/card-1.jpg',
        'title' => 'Изготовление букв',
        'subtitle' => 'Бортогиб нового поколения.',
    ),
    array(
        'image' => '/assets/img/about/card-2.jpg',
        'title' => 'Раскрой материала',
        'subtitle' => 'Фрезерно-гравировальный станок 2х4 м.',
    ),
    array(
        'image' => '/assets/img/about/card-3.jpg',
        'title' => 'Обработка материала',
        'subtitle' => 'Лазерная резка оргстекла, пластика.',
    ),
);
// ========================================

// Если нет элементов, не показываем блок
if (empty($portfolio_items)) {
    return;
}

// Генерируем уникальный ID для слайдера
$slider_id = 'portfolio-slider-' . uniqid();


// Получаем стрелки из настроек
$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');
?>

<section class="section section-glide">
    <div class="container">
        <div class="section-title text-center">
            <h3>Благодаря этому достигается высокое качество изделий</h3>
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="row">
            <div class="col">
                <p>
                    Производственные мощности компании включают в себя широкий спектр современного оборудования, включая фрезерно-гравировальные станки, лазерные станки и лазерную точечную сварку для нержавеющей стали. Кроме того, у нас есть множество специализированных устройств, таких
                    как автоматические кромкооблицовочные станки и печатные машины. Это разнообразие технологий позволяет нам контролировать каждый этап
                    производственного процесса, начиная от выбора материалов и заканчивая финальной сборкой, что, в свою очередь, гарантирует высокое
                    качество нашей продукции и короткие сроки изготовления изделий. Парк оборудования компании:
                </p>
            </div>
        </div>

        <div class="glide glide-auto glide--ltr glide--carousel glide--swipeable" data-glide-perview="3"
            data-glide-gap="30"
            data-glide-autoplay="4000"
            data-glide-perview-md="2"
            data-glide-perview-sm="1"
            id="<?php echo esc_attr($slider_id); ?>">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php foreach ($portfolio_items as $item): ?>
                        <li class="glide__slide card">
                            <div class="w-100">
                                <div class="card-img-container">
                                    <img loading="lazy" src="<?php echo get_template_directory_uri() . esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="card-img-top">
                                </div>
                                <div class="card-body">
                                    <h4 class="h5 card-title"><?php echo esc_html($item['title']); ?></h4>
                                    <p><?php echo esc_html($item['subtitle']); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
                    <img loading="lazy" src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>" alt="Назад">
                </button>
                <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
                    <img loading="lazy" src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>" alt="Вперед">
                </button>
            </div>
        </div>
    </div>
</section>



<section class="section bg-grey">
    <div class="container">
        <div class="section-title text-center">
            <h3>Почему клиенты выбирают нас на протяжении многих лет?</h3>
            <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="row">
            <div class="col-12">
                <p>Качество для нас — это не просто термин, а основополагающая ценность. Мы тщательно отбираем комплектующие, основываясь на их надежности и долговечности, а также строго соблюдаем все оговоренные сроки выполнения заказов. Это позволяет нашим клиентам с уверенностью доверять нам свои проекты, зная, что мы приложим все усилия для достижения наилучшего результата. Мы с радостью принимаем сложные и нестандартные заказы, но также уделяем внимание и небольшим проектам, понимая, что каждая деталь имеет значение.</p>
                <p>Наши клиенты — это разнообразные организации, включая малый бизнес, бюджетные учреждения и крупные корпорации. Мы активно работаем в Москве и Московской области, но при необходимости готовы выезжать в другие регионы, чтобы обеспечить максимальное удобство и удовлетворение потребностей наших заказчиков. </p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-1.jpg" class="img-fluid w-100" style="border-radius: 5px;" alt="Выгодная, гибкая ценовая политика">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-2.jpg" class="img-fluid w-100" style="border-radius: 5px;" alt="Надежно! Гарантированно получите свой заказ в срок">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-3.jpg" class="img-fluid w-100" style="border-radius: 5px;" alt="Мы используем только лучшие комплектующие">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-4.jpg" class="img-fluid w-100" style="border-radius: 5px;" alt="Мы следим за качеством наших изделий">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-5.jpg" class="img-fluid w-100" style="border-radius: 5px;" alt="Один час на рассчет вашего заказа">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img loading="lazy" decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-6.jpg" class="img-fluid w-100" style="border-radius: 5px;" alt="Пять дней срок реализации заказа">
            </div>
        </div>
    </div>
</section>

<div class="section">
    <div class="container">
        <div class="row align-items-start justify-content-center background-color-field-text-only">

            <!-- Одна колонка -->
            <div class="col-12 text-start me-auto">
                <div class="text-content">
                    <p>
                        Компания постоянно обновляет ассортимент продукции, следуя современным трендам и технологиям. Наша команда профессионалов стремится к тому, чтобы каждый проект, независимо от его масштаба, был выполнен с высоким уровнем профессионализма и вниманием к деталям. Мы ценим доверие наших клиентов и всегда готовы к новым вызовам, стремясь к совершенству в каждом аспекте нашей работы.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('template-parts/blocks/clients-slider/clients-slider'); ?>


<?php
// Массив с картинками и подписями
$portfolio_items_people = array(
    array(
        'image' => '/assets/img/about/card-people.png',
        'title' => 'Авинникова Ольга',
        'subtitle' => 'Генеральный директор',
    ),
    array(
        'image' => '/assets/img/about/card-people.png',
        'title' => 'Авинникова Ольга',
        'subtitle' => 'Генеральный директор',
    ),
    array(
        'image' => '/assets/img/about/card-people.png',
        'title' => 'Авинникова Ольга',
        'subtitle' => 'Генеральный директор',
    ),
);
// ========================================

// Если нет элементов, не показываем блок
if (empty($portfolio_items_people)) {
    return;
}

// Генерируем уникальный ID для слайдера
$slider_id = 'portfolio-slider-' . uniqid();


// Получаем стрелки из настроек
$prev_arrow = get_field('carousel_prev_arrow', 'option');
$next_arrow = get_field('carousel_next_arrow', 'option');
?>

<section class="section section-glide">
    <div class="container">
        <div class="section-title text-center">
            <h3>Лица компании</h3>
            <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="glide glide-auto glide--ltr glide--carousel glide--swipeable" data-glide-perview="3"
            data-glide-gap="30"
            data-glide-autoplay="4000"
            data-glide-perview-md="2"
            data-glide-perview-sm="1"
            id="<?php echo esc_attr($slider_id); ?>">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php foreach ($portfolio_items_people as $item): ?>
                        <li class="glide__slide card">
                            <div class="w-100">
                                <div class="card-img-container" style="max-height: 306px; max-width: 306px; align-self: center; margin: 0 auto 16px;">
                                    <img loading="lazy" src="<?php echo get_template_directory_uri() . esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="card-img-top h-100 w-100 mx-auto" style="aspect-ratio: auto">
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="h5 card-title"><?php echo esc_html($item['title']); ?></h4>
                                    <p><?php echo esc_html($item['subtitle']); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
                    <img loading="lazy" src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>" alt="Назад" style="top: 50%;">
                </button>
                <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
                    <img loading="lazy" src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>" alt="Вперед" style="top: 50%;">
                </button>
            </div>
        </div>
    </div>
</section>




<div class="section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(0)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-1.jpg" class="card-img-top" alt="Объемная световая буква А">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(1)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-2.jpg" class="card-img-top" alt="На изображении представлена производственная площадка с несколькими современными широкоформатными принтерами, которые используются для печати рекламной продукции.">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(2)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-3.jpg" class="card-img-top" alt="На изображении видно, как мастер собирает объемные световые буквы">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(3)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-4.jpg" class="card-img-top" alt="Печать любой сложности и на любом оборудовании">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(4)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-5.jpg" class="card-img-top" alt="Печать любой сложности и на любом оборудовании">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(5)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-6.jpg" class="card-img-top" alt="Объемная световая буква А">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(6)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-7.jpg" class="card-img-top" alt="На изображении видно, как мастер собирает объемные световые буквы">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(7)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-8.jpg" class="card-img-top" alt="Печать любой сложности и на любом оборудовании">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="galleryWrapper" style="background: rgba(0,0,0,0.85); display: block; position: fixed; inset: 0; z-index: 9999;">

    <!-- Крестик закрытия -->
    <button type="button" class="btn-close position-fixed top-0 end-0 m-3" onclick="closeGallery()" aria-label="Close" style="z-index: 10000;"></button>

    <!-- Пример одной карусели -->
    <div id="gallery-123" class="carousel slide" data-bs-ride="carousel" style="display: block; position: fixed; top: 0; height: 100%; width: 100%;">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#gallery-123" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#gallery-123" data-bs-slide-to="1" aria-label="Slide 2"></button>
        </div>
        <div class="carousel-inner h-100">
            <div class="carousel-item h-100 active" data-bs-interval="999999999">
                <div class="row align-items-center h-100">
                    <div class="col text-center">
                        <img src="https://site100.ru/furniture/wp-content/uploads/sites/3/2025/09/g5-3.webp" class="img-fluid" style="max-width: 90vw; max-height: 90vh;" alt="Шкаф #001">
                    </div>
                </div>
            </div>
            <div class="carousel-item h-100" data-bs-interval="999999999">
                <div class="row align-items-center h-100">
                    <div class="col text-center">
                        <img src="https://site100.ru/furniture/wp-content/uploads/sites/3/2025/09/g5-2.webp" class="img-fluid" style="max-width: 90vw; max-height: 90vh;" alt="Шкаф #001">
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#gallery-123" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Предыдущий</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#gallery-123" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Следующий</span>
        </button>
    </div>

</div>

<script>
function closeGallery() {
    document.getElementById('galleryWrapper').style.display = 'none';
    // Останавливаем все карусели при закрытии
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach(c => bootstrap.Carousel.getInstance(c)?.pause());
}
</script>





<?php get_template_part('template-parts/blocks/news-articles-tabs'); ?>

<?php get_template_part('template-parts/blocks/portfolio-slider/portfolio-gallery-modal'); ?>
<?php
set_query_var('portfolio_data', array('show_button' => true));
get_template_part('template-parts/blocks/portfolio-slider/portfolio-slider');
?>

<?php
set_query_var('faq_bg', 'bg-white');
set_query_var('faq_questions', array(
    array(
        'question_answer' => array(
            'question' => 'Есть ли гарантия на ваши изделия?',
            'answer' => 'Да, гарантия на световые изделия – 1 год в течении которых мы сделаем бесплатный выезд и произведем бесплатную замену комплектующих. Но мы используем качественные светодиодные модули и блоки питания, на которые производитель дает гарантию от 3х до 5 лет. Поэтому в течении как минимум 3х лет стоимость комплектующих для замены бесплатна, по истечении 1 года нашей гарантии оплатить нужно будет только работу мастера.'
        ),
        'expanded' => false
    ),
    array(
        'question_answer' => array(
            'question' => 'Какой срок изготовления вывески?',
            'answer' => 'Изготовление вывески: 5 — 10 рабочих дней. На другие изделия распространяется этот же срок. Но если возникает необходимость в более коротком сроке, то можем сделать вывеску и в укороченный период.'
        ),
        'expanded' => false
    ),
    array(
        'question_answer' => array(
            'question' => 'Выезжаете ли вы на замер?',
            'answer' => 'Да, при необходимости мы осуществляем выезд на замер. При оформлении заказа стоимость выезда вычитается из стоимости договора.'
        ),
        'expanded' => true // Этот вопрос будет открыт по умолчанию
    ),
    array(
        'question_answer' => array(
            'question' => 'Работаете ли вы с бюджетными организациями?',
            'answer' => 'Да, работаем. Возможно сотрудничество и через Портал Поставщиков.'
        ),
        'expanded' => false
    ),
    array(
        'question_answer' => array(
            'question' => 'Возможна ли оплата с НДС?',
            'answer' => 'Да, мы работаем с НДС'
        ),
        'expanded' => false
    ),
    array(
        'question_answer' => array(
            'question' => 'Можете ли вы сделать оформление торговой точки?',
            'answer' => 'Да, мы делаем оформление торговой точки целиком – Прилавки, тумбы из ЛДСП, обшивка прилавков — холодильников, вывески, информ. стенды.'
        ),
        'expanded' => false
    ),
    array(
        'question_answer' => array(
            'question' => 'Можно ли осуществить доставку изделия?',
            'answer' => 'Доставку изделий без монтажных работ мы осуществляем с привлечением сторонних организаций, типа Яндекс Доставки и пр.'
        ),
        'expanded' => false
    ),
));
get_template_part('template-parts/blocks/faq/faq');
?>

<?php get_template_part('template-parts/blocks/not-found-product/not-found-product'); ?>



<?php
get_footer();
