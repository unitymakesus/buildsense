@php
  $terms = wp_get_post_terms( get_the_id(), 'project-category');
@endphp

<div class="flex-item {{ $terms[0]->slug }}">
  <article class="project">
    @if (has_post_thumbnail())
  		<div class="project-img">
  			<a href="{{ get_permalink() }}">
          @php
  					$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
  					$alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
            $src = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
            $src_2x = wp_get_attachment_image_src( $thumbnail_id, 'medium_large' );
  				@endphp
					<figure class="post-thumbnail">
            @include('partials.lazy-image', [
              'src'    => $src[0],
              'src_2x' => $src_2x[0],
              'alt'    => $alt,
            ])
					</figure>
  			</a>
  		</div>
    @endif

		<div class="project-info" itemprop="description">
			<div class="h4">{{ $terms[0]->name }}</div>
			<h2 class="h3" itemprop="title" itemprop="name">{!! get_the_title() !!}</h2>
			<a href="{{ get_permalink() }}">View project details &raquo;</a>
    </div>
  </article>
</div>
