<article class="container" {!! post_class() !!}>
  <div class="row person">
    <span class="h4 roles">
      <?php
        $terms = wp_get_post_terms( get_the_id(), 'simple-team-category');
        echo join(' <span class="interpunct">&#183;</span> ', wp_list_pluck($terms, 'name'));
      ?>
    </span>

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
