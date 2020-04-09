require('./bootstrap');

window.Vue = require('vue');

import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import 'vue-material-design-icons/styles.css';
Vue.use(Vuetify)

Vue.component('goods-component', require('./components/GoodsListComponent.vue').default);
Vue.component('detail-component', require('./components/DetailComponent.vue').default);
Vue.component('buy-component', require('./components/BuyComponent.vue').default);
Vue.component('user-component', require('./components/UserComponent.vue').default);
Vue.component('appbar-component', require('./components/AppbarComponent.vue').default);
Vue.component('login-component', require('./components/LoginComponent.vue').default);
Vue.component('create-component', require('./components/CreateComponent.vue').default);
Vue.component('admin-component', require('./components/AdminComponent.vue').default);
Vue.component('forget-component', require('./components/ForgetComponent.vue').default);

const app = new Vue({
    el: '#app',
    vuetify: new Vuetify({
        defaultAssets: {
            font: true,
            icons: 'mdi'
        },
        icons: {
            iconfont: 'mdi',
        },
    }),
});
