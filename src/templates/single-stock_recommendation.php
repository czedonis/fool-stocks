<?php get_header(); ?>
    <div id="fmb-wrapper">
        <main id="content" class="fmp-article stock-recommendation" role="main">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php include PLUGIN_DIR . 'src/partials/post-contents.php' ?>
        </main>
        <?php include PLUGIN_DIR . 'src/partials/sidebar-company.php' ?>

        <?php endwhile; endif; ?>
    </div>
<?php get_footer();