export default {
  init() {
    // JavaScript to be fired on the archive pages
  },
  finalize() {
    // JavaScript to be fired on the archive pages, after the init JS
    /*
     Materialize form select
     */
    $('.team-filters select').formSelect();

    /*
    Isotope layout
     */
    var $grid = $('.team-container');

    $grid.isotope({
      itemSelector: '.flex-item',
      percentPosition: true,
      layoutMode: 'fitRows',
    });

    /*
     Lazy load images a la David Walsh
     https://davidwalsh.name/lazyload-image-fade
     */
    $('noscript.lazy').each(function() {
      if (!$(this).hasClass('gtm')) {
        var img = new Image();
        img.setAttribute('data-src', '');
        $(this).before(img);
        img.setAttribute('class', $(this).attr('data-class'));
        img.setAttribute('alt', $(this).attr('data-alt'));
        img.onload = function() {
          img.removeAttribute('data-src');
          $grid.isotope('layout');
        };
        img.src = $(this).attr('data-src');
      }
    });

    /*
    Isotope filtering
     */
    $('.team-filters').on('change', 'select', function () {
      var filterValue = $(this).find(':selected').attr('data-filter');
      $grid.isotope({filter: filterValue});
    });
  },
};
