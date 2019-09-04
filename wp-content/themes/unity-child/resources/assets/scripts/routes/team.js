export default {
  init() {
    // JavaScript to be fired on the archive pages
  },
  finalize() {
    // JavaScript to be fired on the archive pages, after the init JS
    $(document).on('facetwp-loaded', function() {

      /*
       * Materialize form select
       */
      $('.facetwp-type-dropdown select').formSelect();


      /*
       * Add labels above each facet
       */
      $('.facetwp-facet').each(function() {
        let facet_name = $(this).attr('data-name');
        // eslint-disable-next-line no-undef
        let facet_label = FWP.settings.labels[facet_name];
        if ($('.facet-label[data-for="' + facet_name + '"]').length < 1) {
          $(this).before('<div class="h6 facet-label" data-for="' + facet_name + '">' + facet_label + '</div>');
        }
        // Add aria support for search field
        if (facet_name == 'search') {
          $(this).find('input').attr('aria-label', facet_label);
        }
      });
    });
  },
};
