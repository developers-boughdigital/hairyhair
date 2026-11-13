<?php get_header(); ?>
<div class="main-page category-page">
    <section class="main-banner-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 align-self-center">
                    <div class="ban-left-content">
                    <?php
                        // somewhere at the top of this template file:
                        $current_cat = get_queried_object();
                        ?>
                        <div class="content-wrapper">
                            <nav aria-label="breadcrumb">
                                <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                                    <?php if ( function_exists('bcn_display') ) {
                                        bcn_display();
                                    } ?>
                                </div>
                            </nav>
                            <h1><?php echo esc_html( $current_cat->name ); ?></h1>
                            <p><?php echo wp_kses_post( wpautop( $current_cat->description ) ); ?></p>
                        </div>

                    </div>
                </div>
                <?php
                // just before your HTML
                $thumb_id  = get_term_meta( $current_cat->term_id, 'thumbnail_id', true );
                $thumb_url = $thumb_id
                    ? wp_get_attachment_url( $thumb_id )
                    : get_stylesheet_directory_uri() . '/assets/category-list-bnr.png';
                ?>
                <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
                    <div class="ban-right-img">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="banner-img" title="banner-img">
                    </div>  
                </div>

            </div>
        </div>
    </section>
<?php
// taxonomy-product_cat.php override for WooCommerce category with dynamic filters

// 1) Get the current product category term
$current_cat = get_queried_object();

// 2) Category banner (fallback to a static image)
$thumb_id  = get_term_meta( $current_cat->term_id, 'thumbnail_id', true );
$thumb_url = $thumb_id
    ? wp_get_attachment_url( $thumb_id )
    : get_stylesheet_directory_uri() . '/assets/category-list-bnr.png';

// 3) Base query args for fetching all products in this category
$args = [
  'post_type'      => 'product',
  'posts_per_page' => -1,
  'tax_query'      => [[
    'taxonomy' => 'product_cat',
    'field'    => 'term_id',
    'terms'    => $current_cat->term_id,
  ]],
];

// 4) Gather all prices & ratings for filter buckets
$all_products = new WP_Query( $args );
$all_prices   = [];
$all_ratings  = [];
if ( $all_products->have_posts() ) {
  while ( $all_products->have_posts() ) {
    $all_products->the_post();
    $p = wc_get_product( get_the_ID() );
    $all_prices[]  = (float) $p->get_price();
    $all_ratings[] = (int) round( $p->get_average_rating() );
  }
  wp_reset_postdata();
}

// Unique & sorted ratings
$all_ratings = array_unique( $all_ratings );
sort( $all_ratings );

// Price buckets every $100
$buckets = [];
if ( ! empty( $all_prices ) ) {
  $min = floor( min( $all_prices ) );
  $max = ceil( max( $all_prices ) );
  for ( $i = $min; $i < $max; $i += 100 ) {
    $buckets[] = sprintf( '$%d - $%d', $i, $i + 100 );
  }
}

// 5) Get “Hair Type” attribute terms
$hair_types = get_terms([
  'taxonomy'   => 'pa_hair_type',
  'hide_empty' => true,
]);
?>
<section class="white-radius-bg filter-product-grid">
  <div class="wrapper">
    <div class="container">

      <!-- Filters and Sort -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap">

          <!-- Rating Filter -->
          <div class="dropdown me-2" style="display:none;">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/star-icon.png">
              <span id="selected-rating">Any Rating</span>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" data-filter="rating" data-value="">Any Rating</a></li>
              <?php foreach ( $all_ratings as $r ) : ?>
                <li>
                  <a class="dropdown-item" href="#"
                     data-filter="rating"
                     data-value="<?php echo esc_attr( $r ); ?>">
                    <?php printf( '%d Rating', $r ); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Price Filter -->
          <div class="dropdown me-2" style="display:none;">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/money-icon.png">
              <span id="selected-price">Any Price</span>
            </button>
            <ul class="dropdown-menu product-filter-menu">
              <li><a class="dropdown-item" href="#" data-filter="price" data-value="">Any Price</a></li>
              <?php foreach ( $buckets as $range ) : ?>
                <li>
                  <a class="dropdown-item"
                     href="#"
                     data-filter="price"
                     data-value="<?php echo esc_attr( $range ); ?>">
                    <?php echo esc_html( $range ); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          
          <!-- Prescriptive & Non Prescriptive Filter -->
           <!-- Prescription Filter -->
          <div class="dropdown me-2" style="display:none;">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/prescription-icon.png">
              <span id="selected-prescription">Any Prescription</span>
            </button>
            <ul class="dropdown-menu product-filter-menu">
              <li>
                <a class="dropdown-item" href="#" data-filter="prescription" data-value="">
                  Any Prescription
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="#" data-filter="prescription" data-value="prescriptive">
                  Prescriptive
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="#" data-filter="prescription" data-value="non-prescriptive">
                  Non-prescriptive
                </a>
              </li>
            </ul>
          </div>

          
          <!-- Hair Type Filter -->
          <!-- <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/hair-type-icon.png">
              <span id="selected-hair">Hair Type</span>
            </button>
            <ul class="dropdown-menu product-filter-menu">
              <li><a class="dropdown-item" href="#" data-filter="hair" data-value="">All Types</a></li>
              <?php foreach ( $hair_types as $ht ) : ?>
                <li>
                  <a class="dropdown-item" href="#"
                     data-filter="hair"
                     data-value="<?php echo esc_attr( $ht->slug ); ?>">
                    <?php echo esc_html( $ht->name ); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div> -->
        
        
        </div>


        <!-- Sort -->
        <div class="dropdown">
          <span>Sort by: </span>
          <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <span id="selected-sort">Default</span>
          </button>
          <ul class="dropdown-menu product-filter-menu">
            <li><a class="dropdown-item" href="#" data-sort="default">Default</a></li>
            <li><a class="dropdown-item" href="#" data-sort="price-asc">Price: Low to High</a></li>
            <li><a class="dropdown-item" href="#" data-sort="price-desc">Price: High to Low</a></li>
            <li><a class="dropdown-item" href="#" data-sort="rating">Rating</a></li>
          </ul>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="row product pt-0" id="product-grid">
        <?php
        $products = new WP_Query( $args );
        if ( $products->have_posts() ) :
          while ( $products->have_posts() ) : $products->the_post();
            global $product; ?>
                                               <?php include 'template-part/product-block.php'; ?>
        <?php
          endwhile;
          wp_reset_postdata();
        else :
        ?>
          <p>No products found in this category.</p>
        <?php endif; ?>
      </div>

      <?php 
          $in_content_9 = do_shortcode( '[hh_campaign_widget slot="In Content 9"]' ); 
          if($in_content_9 != '') {
              echo "<div style='text-align: center; margin-bottom: 70px;'>".$in_content_9.'</div>';
          }
      ?>

      <div class="conditions-desc">


      <?php
      $taxonomy_prefix = 'product_cat';
      $term_id = $current_cat->term_id;
      $term_id_prefixed = $taxonomy_prefix .'_'. $term_id;
      ?>
      <?php the_field( 'addiction_description', $term_id_prefixed ); ?>
      </div>
    </div>
  </div>
</section>

<?php
// Define taxonomy prefix eg category
// Use term for all taxonomies
$taxonomy_prefix = 'product_cat';

// Define term ID
// Replace NULL with ID of term to be queried eg '123'
$term_id = $current_cat->term_id;

// Example: Get the term ID in a term archive template
// $term_id = get_queried_object_id();

// Define prefixed term ID
$term_id_prefixed = $taxonomy_prefix .'_'. $term_id;
?>
<section class="faq-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="global-title"> 
                        <?php the_field( 'faq_content', $term_id_prefixed ); ?>
                          
                            <?php $faq_button = get_field( 'faq_button', $term_id_prefixed ); ?>
<?php if ( $faq_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $faq_button['url'] ); ?>" target="<?php echo esc_attr( $faq_button['target'] ); ?>"><span><?php echo esc_html( $faq_button['title'] ); ?></span></a>
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-12">
                        <div class="all-faq">
                            <div class="container">
                                <div class="accordion" id="accordionExample">
                                <?php $count = 1; ?>
                                <?php if ( have_rows( 'faq', $term_id_prefixed ) ) : ?>
                                  <?php while ( have_rows( 'faq', $term_id_prefixed ) ) : the_row(); ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?php if($count != '1'){ echo 'collapsed'; } ?>" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne<?php echo $count; ?>" aria-expanded="true"
                                                aria-controls="collapseOne<?php echo $count; ?>">
                                                <?php the_sub_field( 'title' ); ?>
                                            </button>
                                        </h2>
                                        <div id="collapseOne<?php echo $count; ?>" class="accordion-collapse collapse <?php if($count == '1'){ echo 'show'; } ?>"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                            <?php the_sub_field( 'content' ); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $count = $count + 1; ?>
                                    <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<script>
(function(){
  const grid  = document.getElementById('product-grid');
  const items = Array.from(grid.children);
  const state = { rating:'', price:'', hair:'', prescription:'',sort:'default' };

  function refresh(){
    let filtered = items.filter(item=>{
      if ( state.rating && item.dataset.rating !== state.rating ) return false;
      if ( state.hair   && !item.dataset.hair.split(' ').includes(state.hair) ) return false;
      if ( state.price ) {
        const [min,max] = state.price.replace(/\$/g,'').split(' - ').map(parseFloat);
        const p          = parseFloat(item.dataset.price);
        if ( p < min || p > max ) return false;
      }
      if ( state.prescription && item.dataset.prescription !== state.prescription ) return false;
      return true;
    });

    if ( state.sort === 'price-asc' ) {
      filtered.sort((a,b)=> parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
    } else if ( state.sort === 'price-desc' ) {
      filtered.sort((a,b)=> parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
    } else if ( state.sort === 'rating' ) {
      filtered.sort((a,b)=> parseInt(b.dataset.rating) - parseInt(a.dataset.rating));
    }

    grid.innerHTML = '';
    filtered.forEach(i=> grid.appendChild(i));
  }

  document.querySelectorAll('.product-filter-menu a').forEach(a=>{
    a.addEventListener('click', e=>{
      e.preventDefault();
      const f = a.dataset.filter, s = a.dataset.sort, v = a.dataset.value||'';
      if ( f ) {
        state[f] = v;
        document.getElementById('selected-'+f).textContent = a.textContent.trim();
      }
      if ( s ) {
        state.sort = s;
        document.getElementById('selected-sort').textContent = a.textContent.trim();
      }
      refresh();
    });
  });
})();
</script>

<?php get_footer(); ?>