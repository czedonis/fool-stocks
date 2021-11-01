 <?php
    global $wpdb;
    $term = get_the_terms($post->ID,'stocks');
    if(!empty($term))
    {
        ?> <section id="fmp-sidebar">
     <?php
    $stocks = [];
    foreach($term as $t)
        $stocks[] = $t->slug;

    $stk = implode(',',$stocks);

    $table = $wpdb->prefix.'stock_info';

    $company_info = $wpdb->get_results($wpdb->prepare( "SELECT * 
FROM $table WHERE `symbol` = %s",$stk ));

    foreach ($company_info as $company)
    {
        if(!is_page())
        {   ?>
            <section class="fmp-company-info">
                <?php if(!empty($company->company_logo)) ?>
                <p><img class="has-text-align-center" aria-label="Logo of <?php echo $company->company_name; ?>" src="<?php echo $company->company_logo; ?>" /></p>
                <?php if(!empty($company->company_name)) ?>
                <h3 class="has-text-align-center"><?php echo $company->company_name; ?></h3>
                <ul class="details">
                    <?php if(!empty($company->symbol)) ?>
                    <li><strong>Symbol:</strong> <?php echo $company->symbol; ?></li>
                    <?php if(!empty($company->exchange)) ?>
                    <li><strong>Exchange:</strong> <?php echo $company->exchange; ?></li>
                    <?php if(!empty($company->industry)) ?>
                    <li><strong>Industry:</strong> <?php echo $company->industry; ?></li>
                    <?php if(!empty($company->sector)) ?>
                    <li><strong>Sector:</strong> <?php echo $company->sector; ?></li>
                    <?php if(!empty($company->ceo_name)) ?>
                    <li><strong>CEO:</strong> <?php echo $company->ceo_name; ?></li>
                    <?php if(!empty($company->website_url)) ?>
                    <li><a href="<?php echo $company->website_url; ?>" aria-label="Learn more at <?php echo $company->website_url; ?>"><?php echo $company->website_url; ?></a></li>
                    <?php if(!empty($company->description)) ?>
                    <li><?php echo $company->description; ?></li>
                </ul>
            </section>
        <?php } else { ?>
            <section class="fmp-company-info page-info">
                <ul class="details">
                    <?php if(!empty($company->price)) ?>
                    <li><strong>Price:</strong> $<?php echo number_format($company->price); ?></li>
                    <?php if(!empty($company->price_change_percent)) ?>
                    <li><strong>Price Change:</strong> <?php echo $company->price_change_percent; ?>%</li>
                    <?php if(!empty($company->yearly_range)) ?>
                    <li><strong>52 week range:</strong> <?php echo $company->yearly_range; ?></li>
                    <?php if(!empty($company->beta)) ?>
                    <li><strong>Beta:</strong> <?php echo number_format($company->beta); ?></li>
                    <?php if(!empty($company->volume_avg)) ?>
                    <li><strong>Volume Average:</strong> <?php echo number_format($company->volume_avg); ?></li>
                    <?php if(!empty($company->market_capitalization)) ?>
                    <li><strong>Market Cap:</strong> <?php echo number_format($company->market_capitalization); ?></li>
                    <?php if(!empty($company->last_dividend)) ?>
                    <li><strong>Last Dividend:</strong> <?php echo ($company->last_dividend > 0) ? '$' . $company->last_dividend : 'N/A'; ?></li>
                </ul>
            </section>
            <?php
        }
    }
    ?>
</section>
 <?php } ?>