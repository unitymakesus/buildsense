<article class="container" {!! post_class() !!}>
  <div class="row project">
    <h1 itemprop="name"><?php the_title(); ?></h1>

    <div class="col l4 m6 s12">
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

  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
</article>
