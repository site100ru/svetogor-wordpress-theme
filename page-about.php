<?php
/*
* Template Name: PAGE About Us
*/

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

<style>
    .hero-section h1 {
        font-size: clamp(1.25rem, 0.341rem + 4.55vw, 3.75rem);

    }

    .hero-section h2 {
        font-family: var(--second-family);
        font-size: clamp(1.125rem, 0.966rem + 0.8vw, 1.563rem);
        font-weight: 400;
    }
</style>

<!-- HERO СЕКЦИЯ -->
<section class="hero-section" <?php if ($hero_bg_url): ?>style="background-image: url('<?php echo esc_url($hero_bg_url); ?>'); height: 500px;" <?php endif; ?> >
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
    <div class="container">
        <?php render_page_breadcrumbs($page_title); ?>
    </div>
</section>

<section class="section">
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
</section>

<section class="section section-glide section-clients bg-grey">
    <div class="container">
        <div class="section-title text-center">
            <h2>Этапы развития компании</h2>
            <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="row">
            <div class="col">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/scheme-aboutUs.png" alt="Схема" class="img-fluid">
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
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid" />
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

        <div class="glide glide--ltr glide--carousel glide--swipeable" id="<?php echo esc_attr($slider_id); ?>">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php foreach ($portfolio_items as $item): ?>
                        <li class="glide__slide card">
                            <div class="w-100">
                                <div class="card-img-container">
                                    <img src="<?php echo get_template_directory_uri() . esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="card-img-top" />
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo esc_html($item['title']); ?></h5>
                                    <p><?php echo esc_html($item['subtitle']); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
                    <img src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>" alt="Назад" loading="lazy" />
                </button>
                <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
                    <img src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>" alt="Вперед" loading="lazy" />
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Glide !== 'undefined') {
            const glideSlider = new Glide('#<?php echo esc_js($slider_id); ?>', {
                type: 'carousel',
                startAt: 0,
                perView: 3,
                gap: 30,
                autoplay: 4000,
                hoverpause: true,
                breakpoints: {
                    1024: {
                        perView: 2,
                        gap: 20
                    },
                    768: {
                        perView: 1,
                        gap: 15
                    }
                }
            });

            glideSlider.mount();
        } else {
            console.error('Portfolio Slider Error: Glide is not available!');
        }
    });
</script>


<section class="section bg-grey">
    <div class="container">
        <div class="section-title text-center">
            <h3>Почему клиенты выбирают нас на протяжении многих лет?</h3>
            <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid">
        </div>

        <div class="row">
            <div class="col-12">
                <p>Качество для нас — это не просто термин, а основополагающая ценность. Мы тщательно отбираем комплектующие, основываясь на их надежности и долговечности, а также строго соблюдаем все оговоренные сроки выполнения заказов. Это позволяет нашим клиентам с уверенностью доверять нам свои проекты, зная, что мы приложим все усилия для достижения наилучшего результата. Мы с радостью принимаем сложные и нестандартные заказы, но также уделяем внимание и небольшим проектам, понимая, что каждая деталь имеет значение.</p>
                <p>Наши клиенты — это разнообразные организации, включая малый бизнес, бюджетные учреждения и крупные корпорации. Мы активно работаем в Москве и Московской области, но при необходимости готовы выезжать в другие регионы, чтобы обеспечить максимальное удобство и удовлетворение потребностей наших заказчиков. </p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-1.jpg" class="img-fluid w-100" style="border-radius: 5px;">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-2.jpg" class="img-fluid w-100" style="border-radius: 5px;">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-3.jpg" class="img-fluid w-100" style="border-radius: 5px;">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-4.jpg" class="img-fluid w-100" style="border-radius: 5px;">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-5.jpg" class="img-fluid w-100" style="border-radius: 5px;">
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <img decoding="async" src="<?php echo get_template_directory_uri(); ?>/assets/img/about/section-advantage-6.jpg" class="img-fluid w-100" style="border-radius: 5px;">
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row align-items-start justify-content-center background-color-field-text-only">

            <!-- Одна колонка -->
            <div class="col-12 text-start me-auto">
                <div class="text-content">
                    <p>
                        Компания постоянно обновляет ассортимент продукции, следуя современным трендам и технологиям. Наша команда профессионалов стремится к тому, чтобы каждый проект, независимо от его масштаба, был выполнен с высоким уровнем профессионализма и вниманием к деталям. Мы ценим доверие наших клиентов и всегда готовы к новым вызовам, стремясь к совершенству в каждом аспекте нашей работы.
                    <p></p>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

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
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/ico/points.svg" alt="Точки" class="img-fluid" />
        </div>

        <div class="glide glide--ltr glide--carousel glide--swipeable" id="<?php echo esc_attr($slider_id); ?>">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php foreach ($portfolio_items_people as $item): ?>
                        <li class="glide__slide card">
                            <div class="w-100">
                                <div class="card-img-container" style="max-height: 306px; max-width: 306px; align-self: center; margin: 0 auto 16px;">
                                    <img src="<?php echo get_template_directory_uri() . esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="card-img-top h-100 w-100 mx-auto" style="aspect-ratio: auto" />
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo esc_html($item['title']); ?></h5>
                                    <p><?php echo esc_html($item['subtitle']); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
                    <img src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>" alt="Назад" loading="lazy" style="top: 50%;" />
                </button>
                <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
                    <img src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>" alt="Вперед" loading="lazy" style="top: 50%;" />
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Glide !== 'undefined') {
            const glideSlider = new Glide('#<?php echo esc_js($slider_id); ?>', {
                type: 'carousel',
                startAt: 0,
                perView: 3,
                gap: 30,
                autoplay: 4000,
                hoverpause: true,
                breakpoints: {
                    1024: {
                        perView: 2,
                        gap: 20
                    },
                    768: {
                        perView: 1,
                        gap: 15
                    }
                }
            });

            glideSlider.mount();
        } else {
            console.error('Portfolio Slider Error: Glide is not available!');
        }
    });
</script>

<style>
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
/**
 * HTML код с карточками и модалкой с Glide
 */
$images = array(
    'portfolio-card-1.jpg',
    'portfolio-card-2.jpg',
    'portfolio-card-3.jpg',
    'portfolio-card-4.jpg',
    'portfolio-card-5.jpg',
    'portfolio-card-6.jpg',
    'portfolio-card-7.jpg',
    'portfolio-card-8.jpg',
);
?>

<section class="section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(0)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-1.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(1)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-2.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(2)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-3.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(3)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-4.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(4)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-5.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(5)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-6.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 mb-4">
                <div onclick="openGallery(6)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-7.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div onclick="openGallery(7)" class="cursor-pointer w-100 card-portfolio-aboutUs card">
                    <div class="card-img-container">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/portfolio-card-8.jpg" class="card-img-top">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Модалка с Glide -->
<div id="galleryModal" class="portfolio-gallery-modal" style="background: rgba(0, 0, 0, 0.85); display: none; position: fixed; top: 0; bottom: 0; left: 0; right: 0; z-index: 9999;">
    
    <div class="glide" id="galleryGlide" style="width: 100%; height: 100%;">
        <div class="glide__track" data-glide-el="track" style="height: 100%;">
            <ul class="glide__slides" style="height: 100%;">
                <?php foreach($images as $img): ?>
                <li class="glide__slide" style="display: flex; align-items: center; justify-content: center;">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about/<?php echo $img; ?>" style="max-width: 90%; max-height: 90vh; object-fit: contain;">
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="glide__arrows" data-glide-el="controls">
            <button class="glide__arrow glide__arrow--left btn-carousel-left" data-glide-dir="&lt;">
                    <img src="<?php echo esc_url(isset($prev_arrow['url']) ? $prev_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-left.svg'); ?>" alt="Назад" loading="lazy" style="top: 50%;" />
                </button>
                <button class="glide__arrow glide__arrow--right btn-carousel-right" data-glide-dir="&gt;">
                    <img src="<?php echo esc_url(isset($next_arrow['url']) ? $next_arrow['url'] : get_template_directory_uri() . '/assets/img/ico/arrow-right.svg'); ?>" alt="Вперед" loading="lazy" style="top: 50%;" />
                </button>
            </div>
    </div>

    <button onclick="closeGallery()" class="btn-close btn-close-white" style="position: fixed; top: 25px; right: 25px; z-index: 99999;" aria-label="Close"></button>
</div>

<script>
let galleryGlide;

function openGallery(index) {
    document.getElementById('galleryModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    if (!galleryGlide) {
        galleryGlide = new Glide('#galleryGlide', {
            type: 'carousel',
            startAt: 0,
            perView: 1,
            keyboard: true
        });
        galleryGlide.mount();
    }
    
    galleryGlide.go('=' + index);
}

function closeGallery() {
    document.getElementById('galleryModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('galleryModal').addEventListener('click', function(e) {
    if (e.target === this) closeGallery();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeGallery();
});
</script>

<?php get_template_part('template-parts/blocks/news-articles-tabs'); ?>

<?php get_template_part('template-parts/blocks/portfolio-slider/portfolio-gallery-modal'); ?>
<?php  
    set_query_var('portfolio_data', array( 'show_button' => true )); 
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
