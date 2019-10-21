<article class="container projects-container" {!! post_class() !!}>
  <div class="project">
    <h1 itemprop="name">{!! get_the_title() !!}</h1>

    <div class='flex-grid flex-grid-single l3x m2x s1x'>
      <div class="project-info flex-item project-info-single">
        <div>
          @if (!empty($location = get_field('location')))
            <h2 class="h4">Location</h2>
            <p>{{ $location }}</p>
          @endif

          @if (!empty($designer = get_field('designer')))
            <h2 class="h4">Designer</h2>
            <p>{{ $designer }}</p>
          @endif

          @if (!empty($builder = get_field('builder')))
            <h2 class="h4">Builder</h2>
            <p>{{ $builder }}</p>
          @endif

          @if (!empty($photographer = get_field('photographer')))
            <h2 class="h4">Photographer</h2>
            <p>{{ $photographer }}</p>
          @endif
        </div>
      </div>

      <div class="project-img flex-item flex-item-single">
        @if (has_post_thumbnail())
          @php
            $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
            $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
            $image_src = get_the_post_thumbnail_url( get_the_ID() );
          @endphp
          <figure class="post-thumbnail">
            <a href="{{ $image_src }}" data-group="project-gallery">
              {!! get_the_post_thumbnail( get_the_ID(), 'large', ['alt' => $alt] ) !!}
            </a>
          </figure>
        @endif
      </div>

      @php
        $images = get_field('addl-images');
        $size = 'full';
      @endphp

      @if( !empty($images) )
        @foreach( $images as $image )
          <div class="project-img flex-item flex-item-single">
            <a href="{{ $image['url'] }}" data-group="project-gallery">
              {!! wp_get_attachment_image( $image['ID'], $size ) !!}
            </a>
          </div>
        @endforeach
      @endif

    </div>
  </div>

  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
</article>
