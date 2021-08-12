
// пока не вижу смыла втягивать себе в приложение весь js и scss adminlte с его библиотеками.
//require('../vendor/kevinpapst/adminlte-bundle/Resources/assets/admin-lte');

// require JQuery adn bootstrap
const $ = require('jquery');
global.$ = global.jQuery = $;
require('jquery-ui');
require('bootstrap-sass');

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './stimulus';