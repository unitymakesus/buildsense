<article class="container" {!! post_class() !!}>
  <div class="row person">
    <span class="roles">Business Resources</span>
    <span class="roles">Marketing</span>
    <span class="roles">Sales and Marketing</span>
    <h1 itemprop="name"><?php the_title(); ?></h1>
    <?php if (!empty($title = get_field('title'))) { ?>
      <h2 class="title" itemprop="jobTitle"><?php echo $title; ?></h2>
    <?php } ?>

    <div class="col m6 s12">
      <?php
        echo get_field('longer_bio');
      ?>
    </div>

    <div class="col m6 s12">
      <?php if (!empty($image = get_field('primary_image'))) { ?>
        <img class="biopic" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
      <?php } ?>
  </div>
  </div>

  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
  @php comments_template('/partials/comments.blade.php') @endphp
</article>
