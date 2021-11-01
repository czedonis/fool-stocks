<?php get_header(); ?>
    <main id="content" class="fmb company-page" role="main">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="header">
                    <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); } ?>
                    <h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1> <?php edit_post_link(); ?>
                </header>
                <div class="entry-content" itemprop="mainContentOfPage">
                    <?php the_content(); ?>
                    <div class="entry-links"><?php wp_link_pages(); ?></div>
                </div>
            </article>
        <?php endwhile; endif; ?>
        <?php  include PLUGIN_DIR . 'src/partials/sidebar-company.php' ?>
    </main>
    <footer>
        <?php
        $term = get_the_terms($post->ID,'stocks');
        $stocks = [];
        foreach($term as $t)
        {
            $stocks[] = $t->slug;
        }
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'stock_recommendation',
            'order'         => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'stocks',
                    'field'    => 'slug',
                    'terms'    => $stocks
                ))

        );
        $query = new WP_Query( $args );?>
        <div id="stock_rec_links" class="" role="main">
            <header class="header">
                <h3 class="entry-title" itemprop="name">Recommendations:</h3>
            </header>
            <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php include PLUGIN_DIR . 'src/partials/post-contents.php' ?>
            <?php endwhile; endif; ?>
            <?php  include PLUGIN_DIR . 'src/partials/pagination.php' ?>
        </div>
        <?php $args = array(
            'posts_per_page' => 3,
            'post_type'      => 'stock_article',
            'order'         => 'DESC',
            'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'stocks',
                    'field'    => 'slug',
                    'terms'    => $stocks
                ))

        );
        $query = new WP_Query( $args );?>
        <div id="stock_rec_links" class="" role="main">
            <header class="header">
                <h3 class="entry-title" itemprop="name">Other Coverage:</h3>
            </header>
            <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php include PLUGIN_DIR . 'src/partials/post-contents.php' ?>
            <?php endwhile; endif; ?>
            <?php  include PLUGIN_DIR . 'src/partials/pagination.php' ?>
        </div>
    </footer>
<?php get_footer();