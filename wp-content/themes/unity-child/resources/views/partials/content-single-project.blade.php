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
        </div>

        @if (!empty($photographer = get_field('photographer')))
          <div class="photographer">
            Photographer: {{ $photographer }}
          </div>
        @endif
      </div>

      <div class="project-img flex-item flex-item-single">
        @if (has_post_thumbnail())
          @php
            $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
            $alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
            $src = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
            $src_2x = wp_get_attachment_image_src( $thumbnail_id, 'medium_large' );
            $src_lightbox = wp_get_attachment_image_src( $thumbnail_id, 'large' );
          @endphp
          <figure class="post-thumbnail">
            <a href="{{ $src_lightbox[0] }}" data-group="project-gallery" data-modaal-desc="{!! get_the_excerpt($image['ID']) !!}">
              @include('partials.lazy-image', [
                'src'    => $src[0],
                'src_2x' => $src_2x[0],
                'alt'    => $alt,
              ])
            </a>
          </figure>
        @endif
      </div>

      @php
        $images = get_field('addl-images');
      @endphp

      @if( !empty($images) )
        @foreach( $images as $image )
          <div class="project-img flex-item flex-item-single">
            <a href="{{ $image['sizes']['large'] }}" data-group="project-gallery" data-modaal-desc="{!! get_the_excerpt($image['ID']) !!}">
              @php
                $alt = get_post_meta( $image['ID'], '_wp_attachment_image_alt', true );
                $src = wp_get_attachment_image_src( $image['ID'], 'medium' );
                $src_2x = wp_get_attachment_image_src( $image['ID'], 'medium_large' );
              @endphp
              @include('partials.lazy-image', [
                'src'    => $src[0],
                'src_2x' => $src_2x[0],
                'alt'    => $alt,
              ])
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
