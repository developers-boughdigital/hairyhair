<?php 
 get_header();
?>
  <style>
     .treatment-details-wrap .hidden-part { display: none; }
    .read-more-link {
      color: blue;
      text-decoration: underline;
      cursor: pointer;
      background: none;
      border: none;
      padding: 0;
      font: inherit;
    }
  </style>
    <div class="main-page">
        <section class="main-banner-content comparison-banner mb-0">
               <div class="comparison-ban">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">

                        <?php if (function_exists('bcn_display')) {
                            bcn_display();
                            } ?>

                    </div>
                </nav>
                <h1 class="heading2"><?php echo the_title();?></h1>
                <div class="row position-relative">
                    <div class="row">
                       <?php
// Treatment 1
$treatment_1 = get_field('treatment_1');
if ($treatment_1):
    $post = $treatment_1;
    setup_postdata($post);
    $image_url_1 = get_the_post_thumbnail_url(get_the_ID(), 'full');
    $icon1 = get_field('comparison_icon', get_the_ID()); // Get ACF icon from treatment 1
?>
<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
    <div class="prp-left-compair">
        <img src="<?php echo $image_url_1 ? esc_url($image_url_1) : get_template_directory_uri() . '/assets/finasteride-img1.png'; ?>"
            alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" class="prp-img">
        <div class="compair-icon">
            <?php if ($icon1): ?>
                <img src="<?php echo esc_url($icon1['url']); ?>" alt="<?php echo esc_attr($icon1['alt'] ?? 'icon'); ?>" title="<?php echo esc_attr($icon1['alt'] ?? 'icon'); ?>">
            <?php else: ?>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/prp-injection.png" alt="fallback-icon" title="fallback-icon">
            <?php endif; ?>
            <h6><?php the_title(); ?></h6>
        </div>
    </div>
</div>
<?php wp_reset_postdata(); endif; ?>



<?php
// Treatment 2
$treatment_2 = get_field('treatment_2');
if ($treatment_2):
    $post = $treatment_2;
    setup_postdata($post);
    $image_url_2 = get_the_post_thumbnail_url(get_the_ID(), 'full');
    $icon2 = get_field('comparison_icon', get_the_ID()); // Get ACF icon from treatment 2
?>
<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
    <div class="prp-left-compair">
        <img src="<?php echo $image_url_2 ? esc_url($image_url_2) : get_template_directory_uri() . '/assets/finasteride-img2.png'; ?>"
            alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>" class="prp-img">
        <div class="compair-icon">
            <?php if ($icon2): ?>
                <img src="<?php echo esc_url($icon2['url']); ?>" alt="<?php echo esc_attr($icon2['alt'] ?? 'icon'); ?>" title="<?php echo esc_attr($icon2['alt'] ?? 'icon'); ?>">
            <?php else: ?>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/hair-transplant.png" alt="fallback-icon" title="fallback-icon">
            <?php endif; ?>
            <h6><?php the_title(); ?></h6>
        </div>
    </div>
</div>
<?php wp_reset_postdata(); endif; ?>

                    </div>

                    <div class="comparison-badge">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/comparison-vs.png"
                            alt="comparison vs">
                    </div>
                </div>
            </div>
        </div>
    </section>

            <section class="treatment-center">
                <div class="treatment-compare pb-54">
                    <div class="container">
                        <div class="row">
                            <div class="basic-desc">
                                <?php the_field( 'welcome_text' ); ?>
                            </div>
                            <div class="table-view">
                                <div class="comparison-container">





<?php
$treatment_1 = get_field('treatment_1');
$treatment_2 = get_field('treatment_2');
?>

<?php if ($treatment_1 && $treatment_2): ?>
    <div class="comparison-header">
        <div class="column-title blank"></div>

        <?php
        $post = $treatment_1;
        setup_postdata($post);
        ?>
        <div class="column-title fill"><?php the_title(); ?> Treatment</div>
        <?php wp_reset_postdata(); ?>

        <?php
        $post = $treatment_2;
        setup_postdata($post);
        ?>
        <div class="column-title fill"><?php the_title(); ?> Treatment</div>
        <?php wp_reset_postdata(); ?>
    </div>

    <div class="comparison-row">
        <?php
        $fields = array(
            'introduction' => 'Introduction',
            'effectiveness' => 'Effectiveness',
            'treatment_cost' => 'Treatment Cost',
            'treatment_duration' => 'Treatment Duration',
            'treatment_method' => 'Treatment Method',
            'safety' => 'Safety',
            'ease_of_use' => 'Ease of Use',
            'accessibility' => 'Accessibility',
            'sustainability_of_results' => 'Sustainability of Results',
            'compatibility_with_other_treatments' => 'Compatibility with Other Treatments',
            'emotional_appeal' => 'Emotional Appeal',
            'health_benefits_beyond_hair_loss' => 'Health Benefits Beyond Hair Loss',
            'environmental_impact' => 'Environmental Impact'
        );

        foreach ($fields as $field_key => $label): ?>
            <div class="comparison-row-wrap">
                <div class="row-title"><?php echo esc_html($label); ?></div>

                <?php
                // Treatment 1 field
                $post = $treatment_1;
                setup_postdata($post);
                $field_1 = get_field($field_key);
                ?>
                <div class="column-content"><?php echo $field_1 ? esc_html($field_1) : 'N/A'; ?></div>
                <?php wp_reset_postdata(); ?>

                <?php
                // Treatment 2 field
                $post = $treatment_2;
                setup_postdata($post);
                $field_2 = get_field($field_key);
                ?>
                <div class="column-content"><?php echo $field_2 ? esc_html($field_2) : 'N/A'; ?></div>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php endforeach; ?>


        <div class="comparison-row-wrap">
                                <div class="row-title"></div>
                                <div class="column-content">
                                    <div class="cta-box">
                                        <div class="cta-box-wrap">
                                            <?php the_field( 'box_treatment_1_content' ); ?>

                                            <?php $box_treatment_1_button = get_field( 'box_treatment_1_button' ); ?>
                                            <?php if ( $box_treatment_1_button ) : ?>
                                            <a class="btn secondary-btn"
                                                href="<?php echo esc_url( $box_treatment_1_button['url'] ); ?>"
                                                target="<?php echo esc_attr( $box_treatment_1_button['target'] ); ?>"><?php echo esc_html( $box_treatment_1_button['title'] ); ?></a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="treatment-product">
                                            <?php $box_treatment_1_image = get_field( 'box_treatment_1_image' ); ?>
                                            <?php if ( $box_treatment_1_image ) : ?>
                                            <img src="<?php echo esc_url( $box_treatment_1_image['url'] ); ?>"
                                                alt="<?php echo esc_attr( $box_treatment_1_image['alt'] ); ?>" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="column-content">
                                    <div class="cta-box">
                                        <div class="cta-box-wrap">
                                            <?php the_field( 'box_treatment_2_content' ); ?>

                                            <?php $box_treatment_2_button = get_field( 'box_treatment_2_button' ); ?>
                                            <?php if ( $box_treatment_2_button ) : ?>
                                            <a class="btn secondary-btn"
                                                href="<?php echo esc_url( $box_treatment_2_button['url'] ); ?>"
                                                target="<?php echo esc_attr( $box_treatment_2_button['target'] ); ?>"><?php echo esc_html( $box_treatment_2_button['title'] ); ?></a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="treatment-product">
                                            <?php $box_treatment_2_image = get_field( 'box_treatment_2_image' ); ?>
                                            <?php if ( $box_treatment_2_image ) : ?>
                                            <img src="<?php echo esc_url( $box_treatment_2_image['url'] ); ?>"
                                                alt="<?php echo esc_attr( $box_treatment_2_image['alt'] ); ?>" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
    </div>
<?php endif; ?>


                                        
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div class="treatment-details pb-54">
                    <div class="container">
                        <div class="d-flex tab-main">
                            <div class="nav flex-column nav-pills d-none d-md-flex" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <?php 
                                $count = 1;
                                if ( have_rows( 'treatment_details' ) ) : ?>
                                    <?php while ( have_rows( 'treatment_details' ) ) : the_row(); ?>
                                    
                                        <button class="nav-link <?php echo $count == 1? "active":""; ?>" id="v-pills-<?php echo $count;?>-tab" data-bs-toggle="pill" data-bs-target="#v-pills-<?php echo $count;?>" type="button" role="tab" aria-controls="v-pills-<?php echo $count;?>" aria-selected="true"> <?php the_sub_field( 'title' ); ?></button>
                                    <?php 
                                    $count++;
                                endwhile; ?>
                                <?php else : ?>
                                    <?php // No rows found ?>
                                <?php endif; ?>
                            </div>

                            <!-- Dropdown for Mobile View -->
                            <div class="d-md-none mb-3">
                                <select class="form-select" id="tab-dropdown">
                                <?php 
                                $count = 1;
                                if ( have_rows( 'treatment_details' ) ) : ?>
                                    <?php while ( have_rows( 'treatment_details' ) ) : the_row(); ?>
                                        <option value="v-pills-<?php echo $count;?>"><?php the_sub_field( 'title' ); ?></option>
                                    <?php 
                                    $count++;
                                endwhile; ?>
                                <?php else : ?>
                                    <?php // No rows found ?>
                                <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="tab-content" id="v-pills-tabContent">

                            <?php 
                                $count = 1;
                            if ( have_rows( 'treatment_details' ) ) : ?>
                                <?php while ( have_rows( 'treatment_details' ) ) : the_row(); ?>
                                    <div class="tab-pane fade  <?php echo $count == 1 ? "show active":"";?> " id="v-pills-<?php echo $count;?>" role="tabpanel" aria-labelledby="v-pills-<?php echo $count;?>-tab">
                                    <div class="treatment-details-inner">
                                        <div class="treatment-details-wrap" data-limit="300">
                                            <?php the_sub_field( 'treatment_1_content' ); ?>
                                        </div>                                
                                        <div class="treatment-details-wrap" data-limit="300">
                                            <?php the_sub_field( 'treatment_2_content' ); ?>
                                        </div>
                                    </div>                          
                                </div>
                                <?php 
                                 $count++;
                            endwhile; ?>
                            <?php else : ?>
                                <?php // No rows found ?>
                            <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
        
                
                <div class="marketing-sec-main hair-solution-main pb-54">
                    <div class="container">
                        <div class="marketing-wrap hair-solution-wrap">
                            <div class="marketing-left-content">
                            <?php the_field( 'hair_solutions_content' ); ?>
                       
                                <?php $hair_solutions_button = get_field( 'hair_solutions_button' ); ?>
<?php if ( $hair_solutions_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $hair_solutions_button['url'] ); ?>" target="<?php echo esc_attr( $hair_solutions_button['target'] ); ?>"><span><?php echo esc_html( $hair_solutions_button['title'] ); ?></span></a>
<?php endif; ?>
                            </div>
                            <div class="marketing-right-img">

                                <?php $hair_solutions_image = get_field( 'hair_solutions_image' ); ?>
<?php if ( $hair_solutions_image ) : ?>
	<img class="lg-show"  src="<?php echo esc_url( $hair_solutions_image['url'] ); ?>" alt="<?php echo esc_attr( $hair_solutions_image['alt'] ); ?>" />
<?php endif; ?>
           
                                <?php $hair_solutions_image_mobile = get_field( 'hair_solutions_image_mobile' ); ?>
<?php if ( $hair_solutions_image_mobile ) : ?>
	<img class="mb-show" src="<?php echo esc_url( $hair_solutions_image_mobile['url'] ); ?>" alt="<?php echo esc_attr( $hair_solutions_image_mobile['alt'] ); ?>" />
<?php endif; ?>                              
                            </div>
                        </div>
                    </div>
                </div>
        
                <div class="hair-summary-main pb-54">
                    <div class="container">
                        <div class="hair-summary-inner">
                        <?php the_field( 'comparison_summary' ); ?>
                        </div>
                        
                    </div>
                </div>

                <div class="marketing-sec-main hair-quiz-main pb-54">
                    <div class="container">
                        <div class="marketing-wrap">
                            <div class="marketing-left-content">
                                <h2><?php the_field( 'treatment_quiz_title' ); ?></h2>
                          
                                <?php $treatment_quiz_button = get_field( 'treatment_quiz_button' ); ?>
<?php if ( $treatment_quiz_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $treatment_quiz_button['url'] ); ?>" target="<?php echo esc_attr( $treatment_quiz_button['target'] ); ?>"><?php echo esc_html( $treatment_quiz_button['title'] ); ?></a>
<?php endif; ?>
                            </div>
                            <div class="marketing-right-img">
              
                                <?php $treatment_quiz_image = get_field( 'treatment_quiz_image' ); ?>
<?php if ( $treatment_quiz_image ) : ?>
	<img class="lg-show" src="<?php echo esc_url( $treatment_quiz_image['url'] ); ?>" alt="<?php echo esc_attr( $treatment_quiz_image['alt'] ); ?>" />
<?php endif; ?>

<?php $treatment_quiz_image_mobile = get_field( 'treatment_quiz_image_mobile' ); ?>
<?php if ( $treatment_quiz_image_mobile ) : ?>
	<img class="mb-show" src="<?php echo esc_url( $treatment_quiz_image_mobile['url'] ); ?>" alt="<?php echo esc_attr( $treatment_quiz_image_mobile['alt'] ); ?>" />
<?php endif; ?>
                               
                            </div>
                        </div>
                    </div>
                </div>


                <?php include 'template-part/faq-section.php'; ?>

                  
            </section>
            
            <section class="loved-by-people prp-loved-slider">
                <div class="container">
                    <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                        <h2><?php the_field( 'compare_treatments_title' ); ?></h2>
                  
                        <?php $compare_treatments_button = get_field( 'compare_treatments_button' ); ?>
<?php if ( $compare_treatments_button ) : ?>
	<a href="<?php echo esc_url( $compare_treatments_button['url'] ); ?>" target="<?php echo esc_attr( $compare_treatments_button['target'] ); ?>" class="btn secondary-btn"><?php echo esc_html( $compare_treatments_button['title'] ); ?></a>
<?php endif; ?>
                    </div>
                    <div class="loved-by-slider bottom-arrows">
                    <?php $compare_treatments = get_field('compare_treatments'); ?>
<?php if ($compare_treatments) : ?>
    <?php foreach ($compare_treatments as $post) : setup_postdata($post); ?>

        <div class="testi-slide">
            <div class="testi-img">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('full', array(
                            'alt' => get_the_title(),
                            'title' => get_the_title()
                        )); ?>
                    </a>
                <?php else : ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/default-image.png" alt="<?php the_title(); ?>" title="<?php the_title(); ?>">
                <?php endif; ?>

                <div class="testi-caption">
                    <h6><?php the_title(); ?></h6>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>
                    </div>
                </div>
            </section>
        
            <section class="newsletter mb-0 comparison-newsletter center-text">
                <div class="container">
                    <div class="bg-primary">
                        <div class="news-wrap">
                        <?php the_field( 'newsletter_title' ); ?>
                     
                            <?php $newsletter_button = get_field( 'newsletter_button' ); ?>
<?php if ( $newsletter_button ) : ?>
	<a class="btn secondary-btn d-flex m-auto" href="<?php echo esc_url( $newsletter_button['url'] ); ?>" target="<?php echo esc_attr( $newsletter_button['target'] ); ?>"><span><?php echo esc_html( $newsletter_button['title'] ); ?></span></a>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
    </div>
<?php 
get_footer();
?>
<script>
$(function(){
  // 1) your map
  var labels = {
    'High'         : '<span class="label green">High</span>',
    'high'         : '<span class="label green">High</span>',
    'Easy'         : '<span class="label green">Easy</span>',
    'Short-Term'   : '<span class="label red">Short-Term</span>',
    'Short Term'   : '<span class="label red">Short-Term</span>',
    'Medium-Term'  : '<span class="label yellow">Medium-Term</span>',
    'Medium Term'  : '<span class="label yellow">Medium-Term</span>',
    'Medium'       : '<span class="label yellow">Medium</span>',
    'Long-Term'    : '<span class="label yellow">Long Term</span>',
    'Long Term'    : '<span class="label yellow">Long Term</span>',
    'Low'          : '<span class="label red">Low</span>',
    'low'          : '<span class="label red">Low</span>'
  };

  // 2) escape helper
  function escapeForRegex(s) {
    return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
  }

  // 3) build a single regex: longest keys first!
  var keys    = Object.keys(labels),
      sorted  = keys.sort(function(a,b){ return b.length - a.length; }),
      pattern = '\\b(' + sorted.map(escapeForRegex).join('|') + ')\\b',
      regex   = new RegExp(pattern, 'g');

  // 4) do one pass per container
  $('#v-pills-tabContent, .comparison-row-wrap .column-content').each(function(){
    var html = $(this).html();
    html = html.replace(regex, function(match){
      return labels[match];
    });
    $(this).html(html);
  });
});
</script>

<script>
    // $(document).ready(function() {
    //   function splitHTML(html, limit) {
    //     var plainCount = 0;
    //     var visibleHTML = '';
    //     var tagStack = [];
    //     var i = 0;
    //     var len = html.length;
    //     while (i < len && plainCount < limit) {
    //       if (html[i] === '<') {
    //         var endTag = html.indexOf('>', i);
    //         if (endTag === -1) break;
    //         var tagText = html.slice(i, endTag + 1);
    //         visibleHTML += tagText;
    //         if (tagText[1] === '/') {
    //           tagStack.pop();
    //         } else if (tagText[tagText.length - 2] !== '/') {
    //           var tagName = tagText.slice(1).split(/[ \/>]/)[0];
    //           tagStack.push(tagName);
    //         }
    //         i = endTag + 1;
    //       } else {
    //         visibleHTML += html[i];
    //         plainCount++;
    //         i++;
    //       }
    //     }
    //     for (var j = tagStack.length - 1; j >= 0; j--) {
    //       visibleHTML += '</' + tagStack[j] + '>';
    //     }
    //     var hiddenHTML = html.slice(i);
    //     return { visibleHTML: visibleHTML, hiddenHTML: hiddenHTML };
    //   }

    //   $('.treatment-details-wrap').each(function() {
    //     var $el = $(this);
    //     var originalHTML = $el.html();
    //     var limit = parseInt($el.data('limit'), 10) || 300;
    //     var result = splitHTML(originalHTML, limit);
    //     if (!result.hiddenHTML) return;

    //     $el.html(result.visibleHTML);
    //     var $dots = $('<span class="dots">... </span>');
    //     $el.append($dots);

    //     var $hiddenSpan = $('<span class="hidden-part"></span>').html(result.hiddenHTML);
    //     $el.append($hiddenSpan);

    //     var $link = $('<button class="read-more-link">Read More</button>');
    //     $link.on('click', function() {
    //       $el.html(originalHTML);
    //     });
    //     $el.append($link);
    //   });
    // });
  </script>
