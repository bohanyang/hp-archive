import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'
import LanguageDetector from 'i18next-browser-languagedetector'
import zh_CN_app from './zh-CN/app.json'
import zh_CN_settings from './zh-CN/settings.json'
import zh_CN_validation from './zh-CN/validation.json'

i18n
  .use(LanguageDetector)
  .use(initReactI18next)
  .init({
    fallbackLng: 'zh-CN',
    ns: ['app', 'settings', 'validation'],
    interpolation: {
      escapeValue: false // not needed for react as it escapes by default
    },
    resources: {
      'zh-CN': {
        app: zh_CN_app,
        settings: zh_CN_settings,
        validation: zh_CN_validation
      }
    }
  })

document.documentElement.lang = i18n.resolvedLanguage

i18n.on('languageChanged', (lng) => {
  document.documentElement.setAttribute('lang', lng)
})
