<?php

/**
 * Шаблон одиночной статьи
 */
get_header();

while (have_posts()): the_post();

    // HERO секция с универсальной функцией
    render_hero_section(get_the_title(), get_hero_bg());

    // Хлебные крошки
    render_breadcrumbs([
        [
            'title' => 'Статьи',
            'url' => get_permalink(get_option('page_for_posts'))
        ],
        [
            'title' => wp_trim_words(get_the_title(), 6)
        ]
    ]);

    // Основной контент
    $content = get_the_content();

    if (!empty($content)) {
        render_universal_content($content, 'post');
    } else {
?>
        <section class="section single-article-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="article-content">
                            <p class="text-muted">Содержимое статьи не добавлено.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php
    }

    // Другие статьи
    render_related_posts(
        get_the_ID(),
        'post',
        'Другие статьи',
        get_permalink(get_option('page_for_posts')),
        'Все статьи'
    );

endwhile;

get_footer();
