import React from 'react'
import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/inertia-react'
import { InertiaProgress } from '@inertiajs/progress'
import 'bootstrap/dist/css/bootstrap.css'
import '@/styles/flat-ui/css/flat-ui.css'
import '@/styles/app.css'
import 'bootstrap/js/transition'
import 'bootstrap/js/modal'
import '@/i18n'

createInertiaApp({
  resolve: (name) => import(`./pages/${name}.tsx`),
  setup ({ el, App, props }) {
    createRoot(el).render(<App {...props} />)
  }
})

InertiaProgress.init({ showSpinner: true, delay: 40 })
