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

/**
 * Mark the home menu item as current on the front page.
 */
add_filter('nav_menu_css_class', function ($classes, $menuItem, $args, $depth) {
  if (($args->theme_location ?? null) !== 'primary_navigation') {
    return $classes;
  }

  if (
    in_array('menu-item-home', $classes, true)
    && (is_home() || is_front_page())
    && ! in_array('current-menu-item', $classes, true)
  ) {
    $classes[] = 'current-menu-item';
  }

  return $classes;
}, 10, 4);

/**
 * Avoid duplicate menu item IDs in the mobile menu.
 */
add_filter('nav_menu_item_id', function ($menuId, $menuItem, $args, $depth) {
  return ($args->menu_id ?? null) === 'primary-menu-mobile' ? '' : $menuId;
}, 10, 4);
