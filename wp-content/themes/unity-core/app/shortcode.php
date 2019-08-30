<?php

namespace App;

/**
 * Staff list shortcode
 */
add_shortcode('team', function($atts) {
	$people = new \WP_Query([
		'post_type' => 'simple-team',
		'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
	]);

	ob_start(); ?>

	<div class='team-container flex-grid l3x m2x s1x'>

	<?php if ($people->have_posts()) :
		while ($people->have_posts()) : $people->the_post();
		?>

		<div class="flex-item">
	    <div class="person">
	      <div class="person-img">
					<?php if (!empty($image = get_field('primary_image'))) { ?>
						<img class="biopic" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
					<?php } ?>

					<?php if (!empty($imagehov = get_field('hover_image'))) { ?>
						<img class="biopic-hover" src="<?php echo $imagehov['url']; ?>" alt="<?php echo $image['alt']; ?>" />
					<?php } ?>
	      </div>
	      <div class="person-info">
					<span class="roles">
						<?php
							$terms = wp_get_post_terms( get_the_id(), 'simple-team-category');
							echo join(' <span class="interpunct">&#183;</span> ', wp_list_pluck($terms, 'name'));
						?>
					</span>

	        <h2 itemprop="name"><?php the_title(); ?></h2>

	        <?php if (!empty($title = get_field('title'))) { ?>
	          <h3 class="title" itemprop="jobTitle"><?php echo $title; ?></h3>
	        <?php } ?>

	        <?php
	          if (!empty($short_bio = get_field('short_bio'))) {
	            echo $short_bio;
						}
						if (!empty(get_field('longer_bio'))) {
	            echo '<p><a href="' . get_permalink() . '">Read more >></a>';
	          }
	        ?>
	      </div>
	    </div>
		</div>

		<?php
		endwhile; endif; wp_reset_postdata(); ?>

	</div>

	<?php return ob_get_clean();
});


/**
 * News shortcode
 */
 add_shortcode('news', function($atts) {
	$news = new \WP_Query([
		'post_type' => 'simple-news',
		'posts_per_page' => 24,
		'orderby' => 'DES',
		'order' => 'DES',
	]);

	ob_start(); ?>

	<div class='news-container'>

	<?php if ($news->have_posts()) :
		$counter = 0;
		while ($news->have_posts()) : $news->the_post();
			if ($counter % 3 == 0) :
					echo $counter > 0 ? "</div>" : ""; // close div if it's not the first
					echo "<div class='news row'>";
			endif;
		?>

    <div class="article col s12 m4">
      <div class="article-info">
				<h4><?php echo get_the_date( 'Y' ); ?></h4>

				<?php
					$link = get_field('link');

					if (!empty($link['url'])) {  ?>
						<a href="<?php echo $link['url']?>" target="_blank">
					<?php }	?>

	        	<h3 itemprop="title"><?php the_title(); ?>
							<?php if (!empty($description = get_field('description'))) { ?> |	<?php echo $description;
							} ?>
						</h3>

					<?php if (!empty($link['url'])) {  ?>
						</a>
					<?php } ?>

				<?php if (!empty($publication = get_field('publication'))) { ?>
					<p class="publication" itemprop="publication"><?php echo $publication; ?></p>
				<?php } ?>
      </div>
    </div>

		<?php
		$counter++;
		endwhile; endif; wp_reset_postdata(); ?>

	</div>

	<?php return ob_get_clean();
});



/**
 * Projects shortcode
 */
 add_shortcode('projects', function($atts) {
	$news = new \WP_Query([
		'post_type' => 'simple-projects',
		'posts_per_page' => -1,
		'orderby' => 'DES',
		'order' => 'DES',
	]);

	ob_start(); ?>

	<div class='projects-container flex-grid l3x m2x s1x'>

	<?php if ($news->have_posts()) :
		while ($news->have_posts()) : $news->the_post(); ?>

		<div class="flex-item">
	    <div class="project">
				<div class="project-img">
					<a href="<?php the_permalink() ?>">
						<?php if (has_post_thumbnail()) :
								$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
								$alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
								$thumb_caption = get_the_post_thumbnail_caption(get_the_ID());
						?>
							<figure class="post-thumbnail">
								<?php echo get_the_post_thumbnail( get_the_ID(), 'large', ['alt' => $alt] );

								if (!empty($thumb_caption)) : ?>
									<figcaption class="thumb-caption"><?php echo $thumb_caption ?></figcaption>
								<?php endif ?>
							</figure>
						<?php endif ?>
					</a>
				</div>

				<div class="project-info">
					<h4>
						<?php
							$terms = wp_get_post_terms( get_the_id(), 'simple-projects-category');
							echo $terms[0]->name;
						?>
					</h4>
					<h2 itemprop="title"><?php the_title(); ?></h2>
					<a href="<?php the_permalink() ?>">View project details >></a>
	      </div>
	    </div>
		</div>

		<?php
		endwhile; endif; wp_reset_postdata(); ?>

	</div>

	<?php return ob_get_clean();
});
