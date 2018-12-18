
/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');

window.Vue = require('vue');
require('./components/selectDistrict');
require('./components/UserAddressesCreateAndEdit');
const App = new Vue({
    el: '#app',
})