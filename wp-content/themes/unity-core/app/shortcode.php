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

	<div class='team row'>

	<?php if ($people->have_posts()) :
		$counter = 0;
		while ($people->have_posts()) : $people->the_post();
			if ($counter % 3 == 0) :
					echo $counter > 0 ? "</div>" : ""; // close div if it's not the first
					echo "<div class='team row'>";
			endif;
		?>

    <div class="person col s12 m4">
      <div class="person-img">
				<?php if (!empty($image = get_field('primary_image'))) { ?>
					<img class="biopic" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
				<?php } ?>

				<?php if (!empty($imagehov = get_field('hover_image'))) { ?>
					<img class="biopic-hover" src="<?php echo $imagehov['url']; ?>" alt="<?php echo $image['alt']; ?>" />
				<?php } ?>
      </div>
      <div class="person-info">
				<span class="roles">Business Resources</span>
				<span class="roles">Marketing</span>
				<span class="roles">Sales and Marketing</span>

        <h2 itemprop="name"><?php the_title(); ?></h2>

        <?php if (!empty($title = get_field('title'))) { ?>
          <h3 class="title" itemprop="jobTitle"><?php echo $title; ?></h3>
        <?php } ?>

        <?php
          if (!empty($short_bio = get_field('short_bio'))) {
            echo $short_bio;
					}
					if (!empty(get_field('longer_bio'))) {
            echo '<p><a href="' . get_permalink() . '">Read more</a>';
          }
        ?>
      </div>
    </div>

		<?php
		$counter++;
		endwhile; endif; wp_reset_postdata(); ?>

	</div>

	<?php return ob_get_clean();
});



add_shortcode('news', function($atts) {
	$news = new \WP_Query([
		'post_type' => 'simple-news',
		'posts_per_page' => 24,
		'orderby' => 'DES',
		'order' => 'DES',
	]);

	ob_start(); ?>

	<div class='news row'>

	<?php if ($news->have_posts()) :
		$counter = 0;
		while ($news->have_posts()) : $news->the_post();
			if ($counter % 3 == 0) :
					echo $counter > 0 ? "</div>" : ""; // close div if it's not the first
					echo "<div class='team row'>";
			endif;
		?>

    <div class="article col s12 m4">
      <div class="article-img">
				<!-- <img src="https://placekitten.com/200/300"> -->
      </div>
      <div class="article-info">
				<p><?php echo get_the_date( 'Y' ); ?></p>
        	<h2 itemprop="title"><?php the_title(); ?></h2>

        <?php if (!empty($description = get_field('description'))) { ?>
          <h3 class="description" itemprop="description"><?php echo $description; ?></h3>
        <?php } ?>

				<?php if (!empty($publication = get_field('publication'))) { ?>
					<h3 class="publication" itemprop="publication"><?php echo $publication; ?></h3>
				<?php } ?>
      </div>
    </div>

		<?php
		$counter++;
		endwhile; endif; wp_reset_postdata(); ?>

	</div>

	<?php return ob_get_clean();
});
