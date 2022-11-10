import React from 'react'
import { useTranslation } from 'react-i18next'
import { Link } from '@inertiajs/inertia-react'
import SettingsModal from './SettingsModal'

export default function Layout ({ children }: { children: JSX.Element }) {
  const { t } = useTranslation()
  return (
    <React.StrictMode>
      <div
        className="container"
        style={{ width: 'initial', maxWidth: '1536px' }}
      >
        {children}
        <div className="row">
          <div className="col-xs-12 footer">
            <p className="text-right">
              <Link href="/browse">{t('browse')}</Link>
              {' / '}
              <a
                href="https://www.microsoft.com/store/apps/9nblggh6cxp8"
                target="_blank"
                rel="noreferrer"
              >
                {t('get_app')}
              </a>
              {' / '}
              <a
                href="https://chat.daovoice.io/?id=59385141"
                target="_blank"
                rel="nofollow noreferrer"
              >
                {t('contact')}
              </a>
              {' / '}
              <a
                data-toggle="modal"
                data-target="#settings"
                href=""
                onClick={(e) => {
                  e.preventDefault()
                }}
              >
                {t('settings')}
              </a>
            </p>
          </div>
        </div>
        <SettingsModal />
      </div>
    </React.StrictMode>
  )
}
