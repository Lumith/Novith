
require("./bootstrap");

import '@/plugins/vue-composition-api'
import '@/styles/styles.scss'
import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import store from './store'
import "./axios";
import "./firebaseConfig";
import Notifications from 'vue-notification'
import VueSweetalert2 from 'vue-sweetalert2';

import VueProgressBar from 'vue-progressbar'
import i18n from './i18n'

const options = {
  color: '#9155fd',
  failedColor: '#874b4b',
  thickness: '2px',
  transition: {
    speed: '0.5s',
    opacity: '0.6s',
    termination: 500
  },
  autoRevert: true,
  location: 'top',
  inverse: false
}

Vue.use(VueProgressBar, options)

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(VueSweetalert2);
Vue.use(Notifications);
import moment from "moment";
import "moment/locale/ar"; // Arabic locale
import "moment/locale/en-gb"; // English locale
import "moment/locale/fr"; //French locale
import "moment/locale/es"; // Spanish locale
import VueMoment from "vue-moment";
Vue.use(VueMoment, { moment });

import browserDetect from "vue-browser-detect-plugin";
Vue.use(browserDetect);

import * as VueGoogleMaps from 'vue2-google-maps'
Vue.use(VueGoogleMaps);

Vue.config.productionTip = false

const lang = localStorage.getItem("lang") || "en";

// Apply RTL before Vue renders
document.documentElement.setAttribute("dir", lang === "ar" ? "rtl" : "ltr");
// Set vue-i18n locale
i18n.locale = lang;
moment.locale(lang);
// Define a global function to set the lang and dir attributes
Vue.prototype.$setDocumentLangDir = function(lang) {
  document.documentElement.setAttribute("lang", lang);
  document.documentElement.setAttribute("dir", lang === "ar" ? "rtl" : "ltr");
  //save to localStorage
  localStorage.setItem("lang", lang);
  localStorage.setItem("dir", lang === "ar" ? "rtl" : "ltr");
  // change moment locale
  moment.locale(lang);

  // Vuetify RTL
  this.$vuetify.rtl = (lang === "ar");
}
Vue.prototype.$availableLanguages = ['en', 'ar', 'fr', 'es'];
Vue.prototype.$getAvailableLanguages = function() {
    return this.$availableLanguages.map(lang => {
        return { code: lang, name: this.$t(`languages.${lang}`) };
    });
};

const app = new Vue({
    i18n,
  router,
  store,
  vuetify,
  render: h => h(App),
}).$mount('#app')

global.vm = app
