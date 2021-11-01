<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="fmp-post-contents">
    <header>
        <?php if ( is_singular() && !is_page() ) { echo '<h1 class="entry-title" itemprop="headline">'; } else { echo '<h4 class="entry-title">'; } ?>
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?>  <?php echo strip_tags (get_the_term_list($post->ID,'stocks','(',',',')')); ?></a>
        <?php if ( is_singular() && !is_page() ) { echo '</h1>'; } else { echo '</h4>'; } ?>
        <?php if ( is_singular()  && !is_page()) { edit_post_link(); } ?>
    </header>
    <?php if ( is_singular() && !is_page() ) {  ?>
        <div class="fmp entry-content" itemprop="mainEntityOfPage">
            <?php if ( has_post_thumbnail() ) { ?>
                <a href="<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full', false ); echo esc_url( $src[0] ); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); ?></a>
            <?php } ?>
            <meta itemprop="description" content="<?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?>" />
            <?php the_content(); ?>
            <div class="entry-links"><?php wp_link_pages(); ?></div>
        </div>
    <?php  } ?>
</article>