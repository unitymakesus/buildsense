import 'isotope-layout/dist/isotope.pkgd.min.js';

export default {
  init() {
    // JavaScript to be fired on the archive pages
  },
  finalize() {
    // JavaScript to be fired on the archive pages, after the init JS

    /*
    Isotope layout
     */
    var $grid = $('.projects-container');

    $grid.isotope({
      itemSelector: '.flex-item',
      percentPosition: true,
      layoutMode: 'fitRows',
    });

    /**
     * Refresh layout on lazyloaded image event.
     */
    $(document).on('lazyloaded', function () {
      $grid.isotope('layout');
    });

    /*
    Isotope filtering
     */
    $('.project-filters').on('click', 'a', function (e) {
      e.preventDefault();
      var filterValue = $(this).attr('data-filter');
      $grid.isotope({filter: filterValue});

      $('.project-filters a').removeClass('active');
      $(this).addClass('active');
    });
  },
};
