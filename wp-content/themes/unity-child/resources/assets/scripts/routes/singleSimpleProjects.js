import 'modaal';

export default {
  init() {
    // JavaScript to be fired on the archive pages

  },
  finalize() {
    // JavaScript to be fired on the archive pages, after the init JS
    $('.project-img a').modaal({
      type: 'image',
    });
  },
};
