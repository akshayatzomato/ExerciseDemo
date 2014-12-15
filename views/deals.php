<?php global $hdRequestParams; ?>
<section>
    <div class="wrapper">
        <div class="top-section">
            <h1>Top Deals</h1>
        </div>    
        <div class="left-container">
            <div class="filters-container">
                <div class="filter-container">
                    <div class="label">Sort by</div>
                    <div class="sub-filter">
                        <a href="">Cost - low to high</a>
                    </div>
                    <div class="sub-filter">
                        <a href="">Cost - high to low</a>
                    </div>
                    <div class="sub-filter">
                        <a href="">Rating - high to low</a>
                    </div>
                </div>
                <div class="filter-container">
                    <div class="label">City</div>
                    <input class="input city-input-js" type="text" placeholder="Anywhere" value="<?php echo isset( $hdRequestParams['city'] ) ? $hdRequestParams['city'] : ''; ?>"/>
                </div>
                <div class="filter-container">
                    <div class="label">Travelling in</div>
                    <select class="input">
                        <option>0 - 7 days</option>
                        <option>2 weeks</option>
                        <option>3 weeks</option>
                        <option>4 weeks</option>
                        <option>5 weeks</option>
                    </select>
                </div>
                <div class="filter-container">
                    <div class="label">Staying</div>
                    <select class="input">
                        <option>1 night</option>
                        <option>2 nights</option>
                        <option>3 nights</option>
                        <option>4 nights</option>
                        <option>5 nights</option>
                        <option>6 nights</option>
                        <option>7 nights</option>
                    </select>
                </div>
                <div class="filter-container">
                    <div class="label">Check-in on</div>
                    <select class="input">
                        <option>Anytime</option>
                        <?php
                            $days = hdGetUpcomingDays();
                            foreach ( $days as $day ) { ?>
                            <option><?php echo $day; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div id="mainframe" class="inner-container divider--left">
            <?php if ( !$data ) { ?>
                <div class="no-results-container ta-center">
                    No results
                </div> 
            <?php } else { ?>    
            <div class="results-container">
                <ol>
                    <?php $i = 0; $j = 0; $pages = array(); ?>
                    <?php foreach ( $data as $item ) { ?>
                       <?php if ( $i % 6 == 0 ) {
                                $j++;
                                $pages[] = $j;
                                }

                            $item_class = $j > 1 ? 'hidden' : '';
                        ?>        
                        <li class="deal-item bb pb5 mb5 page<?php echo $j . ' ' . $item_class; ?>" style="display: list-item;">
                            <article class="item <?php $class = $i == 0 ? "first" : ""; echo $class; ?>">
                                <div class="left">
                                <img class="item-img" src="<?php echo $item['imageUrl']; ?>" alt="Restaurant Image" />
                                </div>
                                <div class="item-left pos-relative">
                                    <div class="hotel-name">
                                        <h3 class="hotel-name-head ln24 left"><?php echo $item['name']; ?></h3>
                                    </div>
                                    <div class="ln24 mt2 truncate">
                                        <span class="item-address" title=""><?php echo $item['streetAddress'] . (  $item['province'] ? ( ', ' . $item['province'] ) : '' ) . ( $item['city'] ?  ( ', ' . $item['city'] ) : '' ); ?></span>
                                    </div>
                                    <div class="item-cost bold" title="<?php echo $item['originalBaseRate'] . ' + ' . $item['taxesAndFees'] . ' (as taxes and fees)'; ?>">
                                        <?php echo '$' . $item['totalRate']; ?></div>
                                </div>
                                <div class="item-right">
                                    <div class="item-stars ln24">
                                        <div class="right">
                                            <?php if ( $item['starRating'] < 5 && $item['starRating'] >= 4 ) {
                                                $class = 'level1';
                                                $title = 'Legendary';
                                            } elseif ( $item['starRating'] < 4 && $item['starRating'] >=3 ) {
                                                $class = 'level2';
                                                $title = 'Excellent';
                                            } elseif ( $item['starRating'] < 3 && $item['starRating'] <=2 ) {
                                                $class = 'level3';
                                                $title = 'Good';
                                            } elseif ( $item['starRating'] < 2 && $item['starRating'] >= 1 ) {
                                                $class = 'level4';
                                                $title = 'Poor';
                                            }
                                            ?>
                                            <div class="rating-div<?php echo ' ' . $class; ?>" title="<?php echo $title; ?>">
                                                <?php echo number_format( $item['starRating'], 1 ); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </li>
                        <?php $i++; ?>
                    <? } ?>
                </ol>
            </div>    
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
    </div>
</section>
