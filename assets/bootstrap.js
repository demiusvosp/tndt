
// пока не вижу смыла втягивать себе в приложение весь js и scss adminlte с его библиотеками.
require('../vendor/kevinpapst/adminlte-bundle/Resources/assets/admin-lte');


/*
 В данный момент select2entity не видит select2. Кроме того он приносит много своей магии, которая не совмещается ни с
  adminLTE, ни с моим кодом (так нужен ли контроллер, как он к моим entity будет цепляться и т.д.) Накладываясь на
  магию Encore это делает проходную задачу слишком сложной и ресурсоемкой.
  До релиза и на первой установке у нас будет 2 реальных пользователя, и 5-7 для тестов, так что такой умный выбор
  нам просто сейчас не нужен. Добавим потом в другом релизе, и вероятно самостоятельно реализуя функционал select2entity
 */
// global.select2 = require('select2');
// require('../vendor/tetranz/select2entity-bundle/Resources/public/js/select2entity');

// require JQuery adn bootstrap (уже идет с adminLTE)
// const $ = require('jquery');
// global.$ = global.jQuery = $;
// require('jquery-ui');
// require('bootstrap-sass');

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
//import './stimulus';