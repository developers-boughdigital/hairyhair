
    <footer class="bg-dark">
        <div class="container">
            <div class="top-footer">
                <div class="left-footer">
                    <a href="<?php echo home_url();?>" class="brand-logo">
                    <?php $footer_logo = get_field( 'footer_logo', 'option' ); ?>
<?php if ( $footer_logo ) : ?>
	<img src="<?php echo esc_url( $footer_logo['url'] ); ?>" alt="<?php echo esc_attr( $footer_logo['alt'] ); ?>" />
<?php endif; ?>
                    </a>
                    <div class="subscribe-box">
                        <input type="email" id="footer-subscribe-email" placeholder="Enter your email">
                        <button class="btn secondary-btn" id="footer-subscribe-btn" data-nonce="<?php echo esc_attr( wp_create_nonce( 'footer_subscribe_nonce' ) ); ?>">Subscribe</button>
                        <p class="subscribe-msg" style="display:none;margin-top:8px;font-size:13px;"></p>
                    </div>
                    <div class="social-icons">
                        <a href="<?php the_field( 'facebook', 'option' ); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/facebook-icon.png" alt="facebook">
                        </a>
                        <a href="<?php the_field( 'linkedin', 'option' ); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/linkedin.png" alt="linkedin">
                        </a>
                    </div>

                    <div class="contact">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/call.png" alt="call">
                        <span>Call Us : <a href="tel:<?php the_field( 'phone', 'option' ); ?>"><?php the_field( 'phone', 'option' ); ?></a></span>
                    </div>
                </div>
                <div class="right-footer">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-4">
                            <h5>Links</h5>
                            <?php
            wp_nav_menu(array(
                'theme_location' => 'links-menu',
                'container' => false,
                'menu_class' => 'list-cms-inner',
                'fallback_cb' => '__return_false',
                'walker' => new bootstrap_5_wp_nav_menu_walker()
            ));
            ?>  
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-4">
                            <h5>learn</h5>
                            <?php
            wp_nav_menu(array(
                'theme_location' => 'learn-menu',
                'container' => false,
                'menu_class' => 'list-cms-inner',
                'fallback_cb' => '__return_false',
                'walker' => new bootstrap_5_wp_nav_menu_walker()
            ));
            ?>  
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-4">
                            <h5>support</h5>
                            <?php
            wp_nav_menu(array(
                'theme_location' => 'support-menu',
                'container' => false,
                'menu_class' => 'list-cms-inner',
                'fallback_cb' => '__return_false',
                'walker' => new bootstrap_5_wp_nav_menu_walker()
            ));
            ?>  
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-12">
                            <h5>Products</h5>
                            <?php
            wp_nav_menu(array(
                'theme_location' => 'products-menu',
                'container' => false,
                'menu_class' => 'list-cms-inner',
                'fallback_cb' => '__return_false',
                'walker' => new bootstrap_5_wp_nav_menu_walker()
            ));
            ?>  
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-footer">
                <p><?php the_field( 'copyright_text', 'option' ); ?></p>
            </div>
        </div>
    </footer>
            
<?php include('quiz-popup.php'); ?>
<?php wp_footer(); ?>
<script>

jQuery(function($){
  var $content = $('.blog-artical-content');
  var $tocList = $('#toc-list');

  // Check if any h2/h3 already have a "toc-heading-" ID
  var useH3 = $content.find('h2[id^="toc-heading-"], h3[id^="toc-heading-"]').length > 0;

  // If none have toc-heading- IDs, do only h2; else use both
  var selector = 'h2, h3'; //useH3 ? 'h2, h3' : 'h2';

  // Add toc-heading-* IDs to selected headings if missing
  var headingCount = 0;
  $content.find(selector).each(function(){
    var $heading = $(this);
    if (!$heading.attr('id') || !$heading.attr('id').startsWith('toc-heading-')) {
      $heading.attr('id', 'toc-heading-' + headingCount++);
    }
  });

  // Generate TOC
  $tocList.empty();
  $content.find(selector).each(function(){
    var id = this.id;
    var text = $(this).text();
    var $li = $('<li>');
    $('<a>')
      .attr('href', '#' + id)
      .text(text)
      .appendTo($li);
    $tocList.append($li);
  });

  // Smooth scroll
  $tocList.on('click', 'a', function(e){
    e.preventDefault();
    var target = this.hash;
    var $target = $(target);
    if($target.length){
      $('html, body').animate({
        scrollTop: $target.offset().top - 200
      }, 400);
    }
  });
});



jQuery(document).ready(function($){
  // 1) Prevent typing digits / symbols
  $('input[name="fullname"]').on('keypress', function(e){
    var char = String.fromCharCode(e.which);
    if ( !/^[A-Za-z ]$/.test(char) ) {
      e.preventDefault();
    }
  });
  // 2) (Extra safety) Strip out any bad chars on paste or autocomplete
  $('input[name="fullname"]').on('input', function(){
    var clean = this.value.replace(/[^A-Za-z ]+/g, '');
    if ( this.value !== clean ) {
      this.value = clean;
    }
  });
 
});
</script>



<script>
// $('.read-btn').on('click', function () {
//     var $container = $(this).closest('.testi-review');
//     var $reviewText = $container.find('p');

//     $container.toggleClass('expanded');
//     $(this).text($container.hasClass('expanded') ? 'Read Less' : 'Read More');

//     // Optional: Animate parent height change
//     $container.closest('.testi-slide').css('height', 'auto');
// });



jQuery(document).ready(function ($) {
    $('.loved-by-slider .testi-review').each(function () {
        var $review = $(this);

        // Append Read More button if not already present
        if ($review.find('.read-btn').length === 0) {
            $review.append('<button class="read-btn btn dark-btn">Read More</button>');
        }
    });

    // Toggle class and button text
    $(document).on('click', '.read-btn', function () {
        var $container = $(this).closest('.loved-by-slider .testi-review');

        $container.toggleClass('expanded');
        $(this).text($container.hasClass('expanded') ? 'Read Less' : 'Read More');

        // Force parent to recalculate height if inside a slider
        $container.closest('.testi-slide').css('height', 'auto');
    });
});


</script>


<script>
  let count1 = document.querySelectorAll("#counter h3");
let arr = Array.from(count1);

const startCounter = (item) => {
    let start = 0;
    let target = parseInt(item.dataset.number);
    let duration = 3000;
    let symbol = item.querySelector('span').innerHTML;

    let frameRate = 30;
    let totalSteps = Math.floor(duration / frameRate);
    let currentStep = 0;

    function updateCounter() {
        currentStep++;

        let progress = currentStep / totalSteps;
        let currentValue = Math.floor(progress * target);

        if (currentStep >= totalSteps) {
            currentValue = target;
            clearInterval(timer);
        }

        item.innerHTML = `${currentValue}<span>${symbol}</span>`;
    }

    let timer;
    setTimeout(() => {
        timer = setInterval(updateCounter, frameRate);
    }, 500);
};

// Create an IntersectionObserver to start the counter when it's visible
const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            // Start the counter animation when the element is visible
            startCounter(entry.target);
            // Stop observing this element once the counter starts
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 }); // Trigger when 50% of the element is visible

arr.forEach(item => {
    observer.observe(item);
});


$(function () {
  $('.subscription-details').each(function () {
    var $el = $(this);

    // Normalize whitespace for reliable matching
    var norm = $el.text().replace(/\s+/g, ' ').trim();

    // If it's exactly "every X month(s)" (typo-friendly: "mointh(s)"), DO NOTHING
    var keepAsIs = /^every\s+\d+\s+(?:month|months|mointh|moinths)$/i.test(norm);
    if (keepAsIs) return;

    // Otherwise, rebuild to: "$X per month, with an initial N-month upfront cost of $Y"
    var $amount = $el.find('.woocommerce-Price-amount');
    var upfrontHtml = $amount.length ? $amount.prop('outerHTML') : '';

    // Per-month price (first $ amount in the text)
    var perMonthMatch = norm.match(/\$\s*\d+(?:\.\d{2})?/);
    var perMonth = perMonthMatch ? perMonthMatch[0].replace(/\s+/g, '') : '';

    // Extract N for "N-month": from "N-month" or "every N months"
    var monthsMatch =
      norm.match(/(\d+)\s*-\s*month/i) ||
      norm.match(/every\s+(\d+)\s+mo(?:n|in)?th?s?/i);
    var months = monthsMatch ? monthsMatch[1] : '3';
    var xMonth = months + '-month';

    // Only rewrite if we actually have something meaningful to insert
    if (perMonth || upfrontHtml) {
      var newHtml ='/ per month, with an initial ' + xMonth + ' upfront cost of ' +
        (upfrontHtml || '');
      $el.html(newHtml);
    }
  });
});




jQuery(function($){
  // Remove max=-1 on DOM ready
  $('.qty[max="-1"]').removeAttr('max');
  // Also do it whenever cart updates (AJAX carts)
  $(document).on('updated_cart_totals wc_fragments_loaded', function() {
    $('.qty[max="-1"]').removeAttr('max');
  });
});

jQuery(document).ready(function($) {
  $('.wc-block-components-checkout-place-order-button__text').text('Place Order');
});

</script>


<script>
jQuery(document).ready(function($) {
    $('#footer-subscribe-btn').on('click', function() {
        var $btn   = $(this);
        var email  = $('#footer-subscribe-email').val().trim();
        var nonce  = $btn.data('nonce');
        var $msg   = $('.subscribe-msg');

        if (!email) {
            $msg.text('Please enter your email address.').css('color', '#ff4444').show();
            return;
        }

        $btn.prop('disabled', true).text('Subscribing…');

        $.ajax({
            url: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'POST',
            data: { action: 'footer_subscribe', email: email, nonce: nonce },
            success: function(res) {
                if (res.success) {
                    $msg.text(res.data.message).css('color', '#00cc66').show();
                    $('#footer-subscribe-email').val('');
                } else {
                    $msg.text(res.data.message).css('color', '#ff4444').show();
                }
            },
            error: function() {
                $msg.text('Something went wrong. Please try again.').css('color', '#ff4444').show();
            },
            complete: function() {
                $btn.prop('disabled', false).text('Subscribe');
            }
        });
    });
});
</script>
</body>
</html>