<?php

namespace App;

function create_post_news() {
  $argsNews = array(
    'labels' => array(
				'name' => 'News',
				'singular_name' => 'Article',
				'add_new' => 'Add New',
				'add_new_item' => 'Add News Article',
				'edit' => 'Edit',
				'edit_item' => 'Edit News Article',
				'new_item' => 'New Article',
				'view_item' => 'View News Article',
				'search_items' => 'Search News',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
    ),
    'public' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_nav_menus' => false,
    'menu_position' => 20,
    'menu_icon' => 'dashicons-format-quote',
    'capability_type' => 'page',
    'hierarchical' => false,
    'supports' => array(
      'title',
      'editor',
      'revisions',
      'page-attributes',
      'thumbnail'
    ),
    'has_archive' => false,
    'rewrite' => array(
      'slug' => 'bio'
    )
  );
  register_post_type( 'simple-news', $argsNews );
}
add_action( 'init', __NAMESPACE__.'\\create_post_news' );

function create_taxonomies() {

	$argsNewsCategories = array(
		'labels' => array(
			'name' => __( 'Types' ),
			'singular_name' => __( 'Type' )
		),
		'publicly_queryable' => true,
		'show_ui' => true,
    'show_admin_column' => true,
		'show_in_nav_menus' => false,
		'hierarchical' => true,
		'rewrite' => false
	);
	register_taxonomy('simple-news-category', 'simple-news', $argsNewsCategories);

}
add_action( 'init', __NAMESPACE__.'\\create_taxonomies' );
