jQuery(function($) {
    var labels = {
        'High': '<span class="label green">High</span>',
        'high': '<span class="label green">High</span>',
        'Easy': '<span class="label green">Easy</span>',
        'Medium': '<span class="label yellow">Medium</span>',
        'Short Term': '<span class="label red">Short-Term</span>',
        'Short-Term': '<span class="label red">Short-Term</span>',
        'Long-Term': '<span class="label yellow">Long Term</span>',
        'Long Term': '<span class="label yellow">Long Term</span>',
        'Low': '<span class="label red">Low</span>',
        'low': '<span class="label red">Low</span>'
    };

    function replaceLabels(container) {
        $(container).find('.comparison-row-wrap .column-content').each(function() {
            var html = $(this).html();
            $.each(labels, function(word, spanHtml) {
                var regex = new RegExp('\\b' + word + '\\b', 'g');
                html = html.replace(regex, spanHtml);
            });
            $(this).html(html);
        });
    }

    function updateOptions() {
        var sel = [];
        $('.cat-dropdown').each(function() {
            var v = $(this).val();
            if (v) sel.push(v);
        });
        $('.cat-dropdown').each(function() {
            var $t = $(this);
            $t.find('option').each(function() {
                var $o = $(this), v = $o.val();
                $o.prop('disabled', v && sel.indexOf(v) !== -1 && $t.val() != v);
            });
        });
        $('#compare-btn').prop('disabled', sel.length < 2);
    }

    $('.cat-dropdown').on('change', updateOptions);

    $('#compare-btn').on('click', function() {
        var selected = [];
        $('.cat-dropdown').each(function() {
            var v = $(this).val();
            if (v) selected.push(v);
        });
        $.ajax({
            url: CompareAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'compare_treatments',
                nonce: CompareAjax.nonce,
                ids: selected
            },
            success: function(res) {
                if (res.success) {
                    $('#compare-results').html(res.data);
                    replaceLabels('#compare-results');
                } else {
                    alert(res.data);
                }
            }
        });
    });

    updateOptions();
});

jQuery(document).ready(function($){
  $('.filter-btn').on('click', function(){
    var filter = $(this).data('filter');

    // 1. update active button
    $('.filter-btn').removeClass('is-active');
    $(this).addClass('is-active');

    // 2. show/hide items
    if ( filter === 'all' ) {
      // show all
      $('.all-tabs-content .row > [class*="col-"]').fadeIn(200);
    } else {
      // hide non-matches, show matches
      $('.all-tabs-content .row > [class*="col-"]').hide();
      $('.all-tabs-content .row > .' + filter).fadeIn(200);
    }
  });

  // optional: trigger click on page load to set initial state
  $('.filter-btn.is-active').first().trigger('click');
});