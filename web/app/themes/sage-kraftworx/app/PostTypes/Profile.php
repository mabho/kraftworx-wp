<?php

namespace App\PostTypes;

class Profile
{
  /**
   * Register the profile post type.
   *
   * Profiles are private content used by the Profiles block. They have no
   * front-end archive or single views.
   */
  public static function register(): void
  {
    register_post_type('profile', [
      'labels' => [
        'name' => __('Profiles', 'sage'),
        'singular_name' => __('Profile', 'sage'),
        'menu_name' => __('Profiles', 'sage'),
        'name_admin_bar' => __('Profile', 'sage'),
        'add_new' => __('Add New', 'sage'),
        'add_new_item' => __('Add New Profile', 'sage'),
        'new_item' => __('New Profile', 'sage'),
        'edit_item' => __('Edit Profile', 'sage'),
        'view_item' => __('View Profile', 'sage'),
        'all_items' => __('All Profiles', 'sage'),
        'search_items' => __('Search Profiles', 'sage'),
        'not_found' => __('No profiles found.', 'sage'),
        'not_found_in_trash' => __('No profiles found in Trash.', 'sage'),
      ],
      'public' => false,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_rest' => false,
      'publicly_queryable' => false,
      'exclude_from_search' => true,
      'show_in_nav_menus' => false,
      'show_in_admin_bar' => false,
      'query_var' => false,
      'rewrite' => false,
      'has_archive' => false,
      'hierarchical' => false,
      'menu_icon' => 'dashicons-groups',
      'menu_position' => 21,
      'supports' => ['title', 'thumbnail'],
    ]);
  }
}
