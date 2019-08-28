@php
  $footer_color = get_theme_mod( 'footer_color' );
  $text_color = get_theme_mod( 'footer_text_color' );
@endphp
<footer class="content-info page-footer" role="contentinfo">
  <div class="container footer-content row flex space-between">
    <div class="footer-left col m3 s12">
      @php dynamic_sidebar('footer-left') @endphp
    </div>
    <div class="footer-center col m5 s12">
      @php dynamic_sidebar('footer-center') @endphp
    </div>
    <div class="footer-right col m4 s12">
      @php dynamic_sidebar('footer-right') @endphp
    </div>
  </div>

  <div class="footer-copyright">
    <div class="container flex flex-end">
      <p class="copyright">BuildSense Inc, BuildSense Architecture PC &copy; {!! current_time('Y') !!}, All Rights Reserved</p>
      <a href="{{ get_home_url() }}/privacy-policy/">Privacy Policy</a>
    </div>
  </div>

</footer>
