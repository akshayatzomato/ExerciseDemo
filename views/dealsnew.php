<?php global $hdRequestParams; ?>
<section>
    <div class="wrapper">
        <div class="top-section">
            <div class="filter-tab">
                <div class="label inline">Rating:</div>
                <div data-filter="1" class="filter-rating filter-rating-one">Good</div>
                <div data-filter="2" class="filter-rating filter-rating-two">Average</div>
                <div data-filter="3" class="filter-rating filter-rating-three">Excellent</div>
                <div data-filter="4" class="filter-rating filter-rating-four">Legendary</div>
                <div class="label inline ml30">Sort by</div>
                <div class="preselect-option inline">Rating</div>
                <div class="inline sort-options border hidden">
                    <div data-sort="rating" data-name="Rating" class="sort-option">Rating</div>
                    <div data-sort="cost" data-name="Cost" class="sort-option">Cost</div>
                </div>
            </div>
        </div>    
        <div id="mainframe" class="inner-container">
            <?php if ( !$data ) { ?>
                <div class="no-results-container ta-center">
                    No results
                </div> 
            <?php } else { ?>    
            <div class="results-container">
                <?php $i = 0; $j = 0; $pages = array(); ?>
                <?php foreach ( $data as $item ) { ?>
                <?php if ( $i % 6 == 0 ) {
                        $j++;
                        $pages[] = $j;
                        }

                    $item_class = $j > 1 ? 'hidden' : '';
                ?>        
                <div class="grid-item page<?php echo $j; ?><?php echo $item_class ? ( ' ' . $item_class ) : ''; ?>">
                    <?php if ( $item['percentSavings'] > 0 ) { ?>
                    <div class="deal-savings">Save <?php echo floor( $item['percentSavings'] ) . '%'; ?></div>
                    <?php } else { ?>
                    <div class="deal-no-savings"></div>
                    <?php } ?>
                    <div class="deal-hotel ta-center"><?php echo $item['name']; ?></div>
                    <div class="deal-destination ta-center"><?php echo $item['longDestinationName']; ?></div>
                    <div class="deal-stars">
                    <?php $stars = hdGetStars( $item['starRating'] );
                        for ( $k = 0; $k < $stars; $k++ ) { ?>
                        <img src="assets/star.png" alt="r" />
                        <?php  } ?>
                    </div>    
                    <div class="deal-description ta-center"><?php echo $item['description']; ?></div>
                    <div class="deal-cost"><?php echo '$' . $item['totalRate']; ?></div>
                </div>
                <?php $i++; ?>
                <?php } ?>    
            </div>    
            <div class="clear"></div>
            <div class="pagination-bottom">
                <div class="inner-page-left">
                    <div>Page <span class="start-page"><?php echo $pages ? $pages[0] : 0; ?></span> of <span class="last-page"><?php echo count( $pages ); ?></span></div>
                </div>
                <div class="inner-page-right">
                    <ul>
                       <li class="disabled prev"><</li> 
                        <?php for ( $i = 0; $i < count( $pages ); $i++ ) {
                            if ( $i == 0 ) { ?>
                                <li data-page-no="<?php echo $pages[$i]; ?>" class="current">
                                    <a class="link" href="#"><?php echo $pages[$i]; ?></a>
                                </li>
                            <?php } else { ?> 
                                <li data-page-no="<?php echo $pages[$i]; ?>" class="active">
                                    <a class="link" href="#"><?php echo $pages[$i]; ?></a>
                                </li>
                            <?php } ?> 
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="loading-img hidden"><img src="assets/floading.gif" alt="loading icon" /></div>            
    </div>
</section>
