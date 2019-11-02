@extends('layouts.app')

@section('content')
  @while(have_posts()) @php the_post() @endphp
    @if (has_post_thumbnail())
      @php
        $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
        $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
      @endphp
      <figure class="post-thumbnail">
        {!! get_the_post_thumbnail( get_the_ID(), 'full', ['alt' => $alt] ) !!}
      </figure>
    @endif
    <article class="container" {!! post_class() !!}>
      <h1>{{ get_the_title() }}</h1>

      @php
        $latest_news = new \WP_Query([
          'post_type' => 'simple-news',
          'posts_per_page' => 1,
        ]);
      @endphp

      @if ($latest_news->have_posts())
        @while ($latest_news->have_posts())
          @php $latest_news->the_post() @endphp

          <div class="row" style="padding: 30px 0 50px;">
            @if (has_post_thumbnail())
              <div class="col m4">
                @php
                  $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
                  $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                @endphp
                {!! get_the_post_thumbnail( get_the_ID(), 'medium', ['alt' => $alt] ) !!}
              </div>
              <div class="col m8">
            @endif

              <div class="article-info">
                <div class="h4">{{ get_the_date( 'F j, Y' ) }}</div>

                @php $link = get_field('link') @endphp

                <h2 itemprop="title">
                  @if (!empty($link['url']))<a href="{{ $link['url'] }}" target="_blank">@endif
                    {{ get_the_title() }}
                  @if (!empty($link['url']))</a>@endif
                </h2>

                @if (!empty($publication = get_field('publication')))
                  <p class="publication" itemprop="publication">{{ $publication }}</p>
                @endif
              </div>

            @if (has_post_thumbnail())
              </div>
            @endif
          </div>

        @endwhile
      @endif
      @php wp_reset_postdata() @endphp

      @php
        $news = new \WP_Query([
      		'post_type' => 'simple-news',
      		'posts_per_page' => 24,
          'offset' => 1,
      	]);
        $counter = 0;
      @endphp

    	<div class='news-container'>

      	@if ($news->have_posts())
      		@while ($news->have_posts())
            @php $news->the_post() @endphp

      			@if ($counter % 3 == 0)
      			   {!! $counter > 0 ? "</div>" : "" !!} {{-- close div if it's not the first --}}
      			   {!! "<div class='news row'>" !!}
      			@endif

            <div class="article col s12 m4">
              <div class="article-info">
        				<div class="h4">{{ get_the_date( 'Y' ) }}</div>

        				@php $link = get_field('link') @endphp

    	        	<h3 itemprop="title">
        					@if (!empty($link['url']))<a href="{{ $link['url'] }}" target="_blank">@endif
                    {{ get_the_title() }}
                  @if (!empty($link['url']))</a>@endif
                </h3>

        				@if (!empty($publication = get_field('publication')))
        					<p class="publication" itemprop="publication">{{ $publication }}</p>
        				@endif
              </div>
            </div>

      	    @php $counter++ @endphp
  		    @endwhile
        @endif

        @php wp_reset_postdata() @endphp

    	</div>
    </article>
  @endwhile
@endsection
