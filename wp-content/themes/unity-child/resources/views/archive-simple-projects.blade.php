@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  <div class="container">
    <div class="row">
      <a href="#" class="fwp-clear checked" onclick="FWP.reset('project_type')">All</a>
      {!! facetwp_display( 'facet', 'project_type' ) !!}
    </div>

    <div class="row facetwp-template projects-container flex-grid l3x m2x s1x">
      @while (have_posts()) @php the_post() @endphp
        @include('partials.content-'.get_post_type())
      @endwhile
    </div>

    <div class="row center"><button class="btn fwp-load-more">Load more</button></div>
  </div>
@endsection
