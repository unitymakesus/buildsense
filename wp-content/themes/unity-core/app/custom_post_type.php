<?php

namespace App;

// Team Post Type
function create_post_type() {
  $argsTeam = array(
    'labels' => array(
				'name' => 'People',
				'singular_name' => 'Person',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Person',
				'edit' => 'Edit',
				'edit_item' => 'Edit Person',
				's_item' => 'New Person',
				'view_item' => 'View Person',
				'search_items' => 'Search People',
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
    'menu_icon' => 'dashicons-groups',
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
  register_post_type( 'simple-team', $argsTeam );
}
add_action( 'init', __NAMESPACE__.'\\create_post_type' );

function create_taxonomies() {

	$argsTeamCategories = array(
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
	register_taxonomy('simple-team-category', 'simple-team', $argsTeamCategories);

}
add_action( 'init', __NAMESPACE__.'\\create_taxonomies' );



// News Post Type
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
      'slug' => 'news'
    )
  );
  register_post_type( 'simple-news', $argsNews );
}
add_action( 'init', __NAMESPACE__.'\\create_post_news' );

function create_newstax() {

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
add_action( 'init', __NAMESPACE__.'\\create_newstax' );



// Projects Post Type
function create_post_projects() {
  $argsNews = array(
    'labels' => array(
				'name' => 'Projects',
				'singular_name' => 'Project',
				'add_new' => 'Add New',
				'add_new_item' => 'Add Project',
				'edit' => 'Edit',
				'edit_item' => 'Edit Project',
				'new_item' => 'New Project',
				'view_item' => 'View Project',
				'search_items' => 'Search Projects',
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
    'menu_icon' => 'dashicons-admin-multisite',
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
      'slug' => 'projects'
    )
  );
  register_post_type( 'simple-projects', $argsNews );
}
add_action( 'init', __NAMESPACE__.'\\create_post_projects' );

function create_projectstax() {

	$argsProjectsCategories = array(
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
	register_taxonomy('simple-projects-category', 'simple-projects', $argsProjectsCategories);

}
add_action( 'init', __NAMESPACE__.'\\create_projectstax' );
