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

    /*
     Lazy load images a la David Walsh
     https://davidwalsh.name/lazyload-image-fade
     */
    $('noscript.lazy').each(function() {
      if (!$(this).hasClass('gtm')) {
        var img = new Image();
        img.setAttribute('data-src', '');
        img.setAttribute('alt', $(this).attr('data-alt'));
        img.setAttribute('srcset', $(this).attr('data-srcset'));
        $(this).before(img);
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
    $('.project-filters').on('click', 'a', function (e) {
      e.preventDefault();
      var filterValue = $(this).attr('data-filter');
      $grid.isotope({filter: filterValue});

      $('.project-filters a').removeClass('active');
      $(this).addClass('active');
    });
  },
};
