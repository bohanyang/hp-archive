import { useAtom } from 'jotai'
import React, { useState } from 'react'
import { useTranslation } from 'react-i18next'
import RadioGroup from '@/app/RadioGroup'
import settingsAtom from '@/app/settingsAtom'
import { Inertia } from '@inertiajs/inertia'

export default function SettingsModal () {
  const { t } = useTranslation('settings')
  const viewSizes = t('image_sizes', { returnObjects: true }) as {
    [value: string]: string
  }
  const browseSizes = t('thumbnail_sizes', { returnObjects: true }) as {
    [value: string]: string
  }
  const [settings, setSettings] = useAtom(settingsAtom)
  const [viewSize, setViewSize] = useState(settings.viewSize)
  const [browseSize, setBrowseSize] = useState(settings.browseSize)

  return (
    <>
      <div
        className="modal fade"
        id="settings"
        tabIndex={-1}
        role="dialog"
        aria-labelledby="settings_label"
      >
        <div className="modal-dialog" role="document">
          <div className="modal-content">
            <div className="modal-header">
              <button
                type="button"
                className="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">{'×'}</span>
              </button>
              <h4 className="modal-title" id="settings_label">
                {t('settings')}
              </h4>
            </div>
            <div className="modal-body" style={{ paddingBottom: '5px' }}>
              <RadioGroup
                name="view_size"
                label={t('image_size')}
                values={viewSizes}
                required={true}
                state={viewSize}
                setState={(value) => {
                  setViewSize(value)
                }}
              />
              <RadioGroup
                name="browse_size"
                label={t('thumbnail_size')}
                values={browseSizes}
                required={true}
                state={browseSize}
                setState={(value) => {
                  setBrowseSize(value)
                }}
              />
            </div>
            <div className="modal-footer">
              <button
                type="button"
                className="btn btn-embossed btn-primary btn-lg"
                data-dismiss="modal"
                onClick={() => {
                  setSettings({ viewSize, browseSize })
                  Inertia.visit('')
                }}
              >
                {t('submit')}
              </button>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
