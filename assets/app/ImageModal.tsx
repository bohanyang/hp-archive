import React from 'react'
import { useTranslation } from 'react-i18next'

interface Props {
  image_origin: string
  image: {
    copyright: string
    name: string
    urlbase: string
    uhd: boolean
    wp: boolean
  }
}

export default function ImageModal ({ image, image_origin }: Props) {
  const { t } = useTranslation()
  return (
    <>
      <div
        className="modal fade"
        id="about-image"
        tabIndex={-1}
        role="dialog"
        aria-labelledby="about-image_label"
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
              <h4 className="modal-title" id="about-image_label">
                {t('download_image')}
              </h4>
            </div>
            <div className="modal-body" style={{ paddingBottom: '5px' }}>
              <ul>
                {image.uhd && (
                  <li>
                    <a
                      href={`${image_origin}${image.urlbase}_UHD.jpg`}
                      target="_blank"
                      rel="noreferrer"
                    >
                      {t('image_sizes.UHD')}
                    </a>
                  </li>
                )}
                {image.wp && (
                  <li>
                    <a
                      href={`${image_origin}${image.urlbase}_1920x1200.jpg`}
                      target="_blank"
                      rel="noreferrer"
                    >
                      {t('image_sizes.1920x1200')}
                    </a>
                  </li>
                )}
                <li>
                  <a
                    href={`${image_origin}${image.urlbase}_1920x1080.jpg`}
                    target="_blank"
                    rel="noreferrer"
                  >
                    {t('image_sizes.1920x1080')}
                  </a>
                </li>
                <li>
                  <a
                    href={`${image_origin}${image.urlbase}_1080x1920.jpg`}
                    target="_blank"
                    rel="noreferrer"
                  >
                    {t('image_sizes.1080x1920')}
                  </a>
                </li>
                <li>
                  <a
                    href={`${image_origin}${image.urlbase}_1366x768.jpg`}
                    target="_blank"
                    rel="noreferrer"
                  >
                    {t('image_sizes.1366x768')}
                  </a>
                </li>
                <li>
                  <a
                    href={`${image_origin}${image.urlbase}_768x1280.jpg`}
                    target="_blank"
                    rel="noreferrer"
                  >
                    {t('image_sizes.768x1280')}
                  </a>
                </li>
              </ul>
              <small className="text-muted">{`© ${image.copyright}`}</small>
            </div>
            <div className="modal-footer">
              <button
                type="button"
                className="btn btn-primary"
                data-dismiss="modal"
              >
                {t('close_modal')}
              </button>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
