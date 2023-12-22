/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/_scss/app.scss';
import './styles/_scss/global.scss';
import './styles/_scss/elements/_navbar.scss';
import './styles/_scss/elements/_navbar-admin.scss';
import './styles/_scss/elements/_footer.scss';

import './styles/js/navbar.js';
import './styles/js/navbar-admin.js';

import { initTE } from "tw-elements";

// Importez tous les modules de "tw-elements"
import * as twElements from "tw-elements";

// Appelez la fonction initTE et passez-lui tous les modules de "tw-elements"
initTE(twElements);