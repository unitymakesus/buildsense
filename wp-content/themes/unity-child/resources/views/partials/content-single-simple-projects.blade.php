<article class="container projects-container" {!! post_class() !!}>
  <div class="project">
    <h1 itemprop="name"><?php the_title(); ?></h1>

    <div class='flex-grid flex-grid-single l3x m2x s1x'>
      <div class="project-info flex-item project-info-single">
        <div>
          <?php if (!empty($location = get_field('location'))) { ?>
            <h4>Location</h4>
            <p><?php echo $location; ?></p>
          <?php } ?>

          <?php if (!empty($designer = get_field('designer'))) { ?>
            <h4>Designer</h4>
            <p><?php echo $designer; ?></p>
          <?php } ?>

          <?php if (!empty($builder = get_field('builder'))) { ?>
            <h4>Builder</h4>
            <p><?php echo $builder; ?></p>
          <?php } ?>
        </div>
      </div>

      <div class="project-img flex-item flex-item-single">
        <a href="#">
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

      <?php
        $images = get_field('addl-images');
        $size = 'full';

        if( $images ):
          foreach( $images as $image ): ?>
            <div class="project-img flex-item flex-item-single">
              <a href="#">
                <?php echo wp_get_attachment_image( $image['ID'], $size ); ?>
              </a>
            </div>
          <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </div>

  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
</article>
