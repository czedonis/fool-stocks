<?php get_header();
$args = array(
    'posts_per_page' => 10,
    'post_type'      => 'stock_recommendation',
    'order'         => 'DESC',
    'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1
);
$query = new WP_Query( $args );?>
    <main id="content" role="main">
        <header class="header">
            <h1 class="entry-title" itemprop="name"><?php the_archive_title(); ?></h1>
            <div class="archive-meta" itemprop="description"><?php if ( '' != the_archive_description() ) { echo esc_html( the_archive_description() ); } ?></div>
        </header>
        <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
            <?php include PLUGIN_DIR . 'src/partials/post-contents.php' ?>
        <?php endwhile; endif; ?>
        <?php  include PLUGIN_DIR . 'src/partials/pagination.php' ?>
    </main>
<?php get_footer();