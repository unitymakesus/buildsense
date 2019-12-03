export default {
  init() {
    // JavaScript to be fired on the archive pages
  },
  finalize() {
    // JavaScript to be fired on the archive pages, after the init JS
    /*
    Isotope layout
     */
    var $grid = $('.team-container');

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
    $('.team-filters').on('change', 'select', function () {
      var filterValue = $(this).find(':selected').attr('data-filter');
      $grid.isotope({filter: filterValue});
    });

    /*
    Modaal for leadership team bios
     */
    $('.person a').modaal();
  },
};
