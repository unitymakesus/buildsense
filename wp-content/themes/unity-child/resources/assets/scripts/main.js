// Import parent JS
import '../../../../unity-core/dist/scripts/main.js';
import './materializejs/core.js';

/** Import local dependencies */
import Router from './util/Router';
import common from './routes/common';
// import home from './routes/home';
// import aboutUs from './routes/about';
import archive from './routes/archive';
import team from './routes/team';
import singleSimpleProjects from './routes/singleSimpleProjects';

/** Populate Router instance with DOM routes */
const routes = new Router({
  common,
//   home,
//   aboutUs,
  archive,
  team,
  singleSimpleProjects,
});

/** Load Events */
jQuery(document).ready(() => routes.loadEvents());
