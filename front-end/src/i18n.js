import Vue from 'vue'
import VueI18n from 'vue-i18n'
import en from './locales/en.json'
import ar from './locales/ar.json'
import fr from './locales/fr.json'
import es from './locales/es.json'

let lang = localStorage.getItem("lang") || "en";

Vue.use(VueI18n)

export default new VueI18n({
  locale: lang,
  fallbackLocale: 'en',
  messages: { en, ar, fr, es }
})
