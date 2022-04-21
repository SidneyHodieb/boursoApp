import './plugins/vue-composition-api'
import '../../sass/styles/styles.scss'
import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import store from './store'
import axios from 'axios'
axios.interceptors.response.use((response) => {
  if(response.status === 401) {
       console("You are not authorized");
  }
  return response;
}, (error) => {


  if (error.response && error.response.data) {
      return Promise.reject(error.response.data);
  }
  return Promise.reject(error.message);
});


/* import mixin from "../src/Mixins/default.js";
 */



Vue.config.productionTip = false

new Vue({
 /*  mixins: [mixin], */
  router,
  store,
  vuetify,
  axios,
  render: h => h(App),
}).$mount('#app')
