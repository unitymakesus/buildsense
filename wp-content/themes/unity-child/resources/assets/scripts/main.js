// Import parent JS
import '../../../../unity-core/dist/scripts/main.js';
import './materializejs/core.js';
import 'picturefill';
import 'lazysizes';

/** Import local dependencies */
import Router from './util/Router';
import common from './routes/common';
import archive from './routes/archive';
import team from './routes/team';
import singleProject from './routes/singleProject';

/** Populate Router instance with DOM routes */
const routes = new Router({
  common,
  archive,
  team,
  singleProject,
});

/** Load Events */
jQuery(document).ready(() => routes.loadEvents());
