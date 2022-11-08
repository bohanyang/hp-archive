import React from 'react'
import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/inertia-react';
import { InertiaProgress } from '@inertiajs/progress';

createInertiaApp({
  resolve: name => import(`./pages/${name}.tsx`),
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />)
  },
})

InertiaProgress.init({ color: '#4B5563' });
