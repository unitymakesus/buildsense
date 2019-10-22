@extends('layouts.app')

@section('content')

  <div class="container">
    <header class="page-header">
      <h1>{!! App::title() !!}</h1>
    </header>

    <div class="row project-filters">
      <a href="#" data-filter="*" class="active">All</a>
      <a href="#" data-filter=".new-construction">New</a>
      <a href="#" data-filter=".renovation-addition">Reno</a>
    </div>

    <div class="row projects-container flex-grid l3x m2x s1x">
      @while (have_posts()) @php the_post() @endphp
        @include('partials.content-'.get_post_type())
      @endwhile
    </div>

    @php
      the_posts_pagination([
        'prev_text' => '&laquo; Previous <span class="screen-reader-text">page</span>',
        'next_text' => 'Next <span class="screen-reader-text">page</span> &raquo;',
        'before_page_number' => '<span class="meta-nav screen-reader-text">Page</span>',
      ]);
    @endphp
  </div>
@endsection
