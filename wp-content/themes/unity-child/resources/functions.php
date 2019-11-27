<?php

namespace App;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
  // Enqueue files for child theme (which include the core assets as imports)
  wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
  wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);

  // Set array of theme customizations for JS
  wp_localize_script( 'sage/main.js', 'simple_options', array('fonts' => get_theme_mod('theme_fonts'), 'colors' => get_theme_mod('theme_color')) );
}, 100);

/**
 * REMOVE WP EMOJI
 */
 remove_action('wp_head', 'print_emoji_detection_script', 7);
 remove_action('wp_print_styles', 'print_emoji_styles');
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Enable plugins to manage the document title
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
 */
add_theme_support('title-tag');

/**
 * Register navigation menus
 * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
 */
register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'sage'),
    'social_links' => __('Social Links', 'sage')
]);

/**
 * Enable post thumbnails
 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
 */
add_theme_support('post-thumbnails');

/**
 * Enable HTML5 markup support
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
 */
add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

/**
 * Enable selective refresh for widgets in customizer
 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
 */
add_theme_support('customize-selective-refresh-widgets');

/**
* Add support for Gutenberg.
*
* @link https://wordpress.org/gutenberg/handbook/reference/theme-support/
*/
add_theme_support( 'align-wide' );
add_theme_support( 'disable-custom-colors' );
add_theme_support( 'wp-block-styles' );

/**
 * Enqueue editor styles for Gutenberg
 */
// function simple_editor_styles() {
//   wp_enqueue_style( 'simple-gutenberg-style', asset_path('styles/main.css') );
// }
// add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\simple_editor_styles' );

/**
 * Add image quality
 */
add_filter('jpeg_quality', function($arg){return 100;});

/**
 * Enable logo uploader in customizer
 */
add_image_size('simple-logo', 200, 200, false);
add_image_size('simple-logo-2x', 400, 400, false);
add_theme_support('custom-logo', array(
  'size' => 'simple-logo-2x'
));

/**
 * Set image sizes
 */
update_option( 'thumbnail_size_w', 300 );
update_option( 'thumbnail_size_h', 300 );
update_option( 'thumbnail_crop', 1 );
update_option( 'medium_size_w', 600 );
update_option( 'medium_size_h', 600 );
add_image_size('tiny-thumbnail', 80, 80, true);
add_image_size('small-thumbnail', 150, 150, true);
add_image_size('medium-square-thumbnail', 400, 400, true);


add_filter( 'image_size_names_choose', function( $sizes ) {
  return array_merge( $sizes, array(
    'tiny-thumbnail' => __( 'Tiny Thumbnail' ),
    'small-thumbnail' => __( 'Small Thumbnail' ),
    'medium-square-thumbnail' => __( 'Medium Square Thumbnail' ),
  ) );
} );


/**
 * Remove prefixes from archive titles
 */
add_filter( 'get_the_archive_title', function ($title) {
  if (is_post_type_archive('project')) {
    $title = 'Gallery';
  } if ( is_category() ) {
    $title = single_cat_title( '', false );
  } elseif ( is_tag() ) {
    $title = single_tag_title( '', false );
  }
  return $title;
});

/**
 * Update SEO title element for archive pages.
 *
 * @link https://developer.wordpress.org/reference/functions/is_post_type_archive/
 */
add_filter( 'the_seo_framework_title_from_generation', function( $title, $args ) {
  if ( is_post_type_archive( 'project' ) ) {
    $title = 'Gallery';
  }

  return $title;
}, 10, 2 );

// Custom Post Types
add_action( 'init', function () {
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
    'public' => false,
    'show_ui' => true,
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
    'rewrite' => false
  );
  register_post_type( 'simple-news', $argsNews );

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

  $argsProjects = array(
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
    'has_archive' => true,
    'rewrite' => array(
      'slug' => 'gallery'
    )
  );
  register_post_type( 'project', $argsProjects );

	$argsProjectsCategories = array(
		'labels' => array(
			'name' => __( 'Project Types' ),
			'singular_name' => __( 'Project Type' )
		),
		'publicly_queryable' => true,
		'show_ui' => true,
    'show_admin_column' => true,
		'show_in_nav_menus' => false,
		'hierarchical' => true,
		'rewrite' => false
	);
	register_taxonomy('project-category', 'project', $argsProjectsCategories);
});


// Show all projects on archive
add_action( 'pre_get_posts', function( $query ) {
    if ( $query->is_post_type_archive('project')) {
      $query->set( 'order', 'ASC' );
      $query->set( 'orderby', 'menu_order' );
      $query->set( 'posts_per_page', '-1' );
    }
} );

/**
 * Remove single page from people post type
 */
function change_public_people( $args, $post_type ){

  if ( 'simple-team' == $post_type ) {
    $args['publicly_queryable'] = false;
  }

  return $args;
}
add_filter( 'register_post_type_args', __NAMESPACE__ . '\\change_public_people' , 10, 2 );


/**
 * Staff list shortcode
 */
add_shortcode('filterable-team', function($atts) {
	$people = new \WP_Query([
		'post_type' => 'simple-team',
		'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
	]);

	ob_start(); ?>

  <div class="row team-filters">
    <label for="filter">Filter</label>
    <select id="filter" name="filter">
      <option data-filter="*" selected>View All</option>
      <?php
        $terms = get_terms( 'simple-team-category', array(
          'orderby'    => 'menu_order',
          'hide_empty' => 1
        ) );

        foreach ($terms as $term) {
          echo '<option data-filter=".' . $term->slug . '">' . $term->name . '</option>';
        }
      ?>
    </select>
  </div>

	<div class="row team-container flex-grid l3x m2x s1x">

	<?php if ($people->have_posts()) :
		while ($people->have_posts()) : $people->the_post();
    global $post;
    $terms = wp_get_post_terms( get_the_id(), 'simple-team-category');
		?>

		<div class="flex-item <?php echo join(' ', wp_list_pluck($terms, 'slug')); ?>">
      <div class="person">
	      <div class="person-img">
          <?php
            if (!empty($longer_bio = get_field('longer_bio'))) {
              echo '<a href="#' . $post->post_name . '">';
            }
          ?>
					<?php if (!empty($image = get_field('primary_image'))) { ?>
            <noscript class="lazy" data-class="biopic" data-src="<?php echo $image['url']; ?>" data-alt="<?php echo $image['alt']; ?>" aria-hidden="true">
              <img class="biopic" src="<?php echo $image['url']; ?>" data-src="" alt="<?php echo $image['alt']; ?>">
            </noscript>
					<?php } ?>

					<?php if (!empty($imagehov = get_field('hover_image'))) { ?>
            <noscript class="lazy" data-class="biopic-hover" data-src="<?php echo $imagehov['url']; ?>" data-alt="<?php echo $imagehov['alt']; ?>" aria-hidden="true">
              <img class="biopic-hover" src="<?php echo $imagehov['url']; ?>" data-src="" alt="<?php echo $imagehov['alt']; ?>">
            </noscript>
					<?php } ?>
          <?php
            if (!empty($longer_bio = get_field('longer_bio'))) {
              echo '</a>';
            }
          ?>
	      </div>
	      <div class="person-info">
					<div class="h4 roles">
						<?php
							echo join(' <span class="interpunct">&#9642;</span> ', wp_list_pluck($terms, 'name'));
						?>
					</div>

	        <h2 class="h3" itemprop="name"><?php the_title(); ?></h2>

	        <?php if (!empty($title = get_field('title'))) { ?>
	          <h3 class="title" itemprop="jobTitle"><?php echo $title; ?></h3>
	        <?php } ?>

	        <?php
	          if (!empty($short_bio = get_field('short_bio'))) {
	            echo $short_bio;
						}
            if (!empty($longer_bio = get_field('longer_bio'))) {
              echo '<p><a href="#' . $post->post_name . '">Read more &raquo;</a></p>';
            }
	        ?>
	      </div>
      </div>

      <div id="<?php echo $post->post_name; ?>" class="modaal-hidden">
        <div class="row">
          <div class="col m6">
            <div class="h4 roles">
  						<?php
  							echo join(' <span class="interpunct">&#9642;</span> ', wp_list_pluck($terms, 'name'));
  						?>
  					</div>

  	        <h2 class="h3" itemprop="name"><?php the_title(); ?></h2>

  	        <?php if (!empty($title = get_field('title'))) { ?>
  	          <h3 class="title" itemprop="jobTitle"><?php echo $title; ?></h3>
  	        <?php } ?>

  	        <?php
  	          if (!empty($short_bio = get_field('short_bio'))) {
  	            echo $short_bio;
  						}
  						if (!empty($longer_bio = get_field('longer_bio'))) {
  	            echo $longer_bio;
  	          }
  	        ?>
          </div>
          <div class="col m6">
            <div class="person-img">
              <?php if (!empty($image = get_field('primary_image'))) { ?>
                <noscript class="lazy" data-class="biopic" data-src="<?php echo $image['url']; ?>" data-alt="<?php echo $image['alt']; ?>" aria-hidden="true">
                  <img class="biopic" src="<?php echo $image['url']; ?>" data-src="" alt="<?php echo $image['alt']; ?>">
                </noscript>
              <?php } ?>

              <?php if (!empty($imagehov = get_field('hover_image'))) { ?>
                <noscript class="lazy" data-class="biopic-hover" data-src="<?php echo $imagehov['url']; ?>" data-alt="<?php echo $imagehov['alt']; ?>" aria-hidden="true">
                  <img class="biopic-hover" src="<?php echo $imagehov['url']; ?>" data-src="" alt="<?php echo $imagehov['alt']; ?>">
                </noscript>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
		</div>

		<?php
		endwhile; endif; wp_reset_postdata(); ?>

	</div>

	<?php return ob_get_clean();
});
