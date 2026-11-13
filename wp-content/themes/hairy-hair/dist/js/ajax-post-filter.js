jQuery(function($) {
    // On page load, add active class to the current category
    $(document).ready(function() {
        var currentCategory = $('.category-filter').filter(function() {
            return $(this).text().trim() === '<?php single_cat_title(); ?>'; // Match the current category name
        });

        if (currentCategory.length) {
            currentCategory.addClass('active');
        }
    });

    // Handle category filter click
    $('.category-filter').on('click', function(e) {
        e.preventDefault();

        // Remove the active class from all categories
        $('.category-filter').removeClass('active');

        // Add the active class to the clicked category
        $(this).addClass('active');

        var categoryId = $(this).data('category-id');
        var sortOption = $('#sort-filter').val();

        loadPosts(categoryId, sortOption);
    });

    // Handle sorting option change
    $('#sort-filter').on('change', function() {
        var sortOption = $(this).val();
        var categoryId = $('.category-filter.active').data('category-id') || 0; // Default to 0 if no category is selected

        loadPosts(categoryId, sortOption);
    });

    // Function to load posts via AJAX
    function loadPosts(categoryId, sortOption) {
        $.ajax({
            url: ajax_filter.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_posts',
                nonce: ajax_filter.nonce,
                category_id: categoryId,
                sort_option: sortOption
            },
            success: function(response) {
                $('#latest-posts').html(response); // Update posts section
            }
        });
    }
});
