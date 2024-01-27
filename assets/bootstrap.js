
// пока не вижу смыла втягивать себе в приложение весь js и scss adminlte с его библиотеками.
import moment from "moment";

require('../vendor/kevinpapst/adminlte-bundle/Resources/assets/admin-lte');

moment.locale('ru');

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
