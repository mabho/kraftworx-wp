<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Register the Kraftworx Components block category.
 *
 * @param  array  $categories
 * @return array
 */
add_filter('block_categories_all', function ($categories) {
    array_unshift($categories, [
        'slug' => 'kraftworx',
        'title' => __('Kraftworx Components', 'sage'),
    ]);

    return $categories;
});
