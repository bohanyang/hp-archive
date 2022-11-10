import ImageModal from '@/app/ImageModal'
import Layout from '@/app/Layout'
import useSettingValue from '@/app/useSettingValue'
import flags from '@/images/flags'
import { Link, Head } from '@inertiajs/inertia-react'
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
  video: string
  records: {
    description: string
    market: string
    link?: string
    date: string
    formattedDate: string
  }[]
}

function Image ({ image_origin, image, records, video }: Props) {
  const { t } = useTranslation()
  const { image_size } = useSettingValue('viewSize')
  return (
    <>
      <Head title={image.name} />
      <div className="row">
        <div className="col-xs-12">
          <img
            src={`${image_origin}${image.urlbase}_${image_size}.jpg`}
            alt={image.name}
            className="img-rounded wallpaper"
          />
        </div>
        <div className="col-xs-12">
          <button
            type="button"
            className="btn btn-link btn-block"
            data-toggle="modal"
            data-target="#about-image"
          >
            {t('download_image')}
          </button>
        </div>
        {video && (
          <div className="col-xs-12">
            <a
              className="btn btn-link btn-block"
              href={video}
              target="_blank"
              rel="noreferrer"
            >
              <strong>{t('play_video')}</strong>
            </a>
          </div>
        )}
      </div>

      <div className="row">
        <div className="col-xs-12">
          <table className="table table-striped">
            <tbody>
              {records.map((record) => (
                <React.Fragment key={`${record.market}_${record.date}`}>
                  <tr>
                    <td>
                      <Link
                        href={`/${record.market}/${record.date}`}
                        style={{
                          display: 'flex',
                          alignItems: 'center',
                          gap: '4px'
                        }}
                      >
                        <img
                          className="flag"
                          src={flags(record.market)}
                          alt={record.market}
                        />
                        {record.formattedDate}
                      </Link>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <span lang={record.market}>
                        {record.link
                          ? (
                          <a
                            href={record.link}
                            target="_blank"
                            rel="nofollow noreferrer"
                          >
                            {record.description}
                          </a>
                            )
                          : (
                          <span className="text-link">
                            {record.description}
                          </span>
                            )}
                      </span>
                    </td>
                  </tr>
                </React.Fragment>
              ))}
            </tbody>
          </table>
        </div>
      </div>
      <ImageModal {...{ image, image_origin }} />
    </>
  )
}

Image.layout = (page: JSX.Element) => <Layout children={page} />

export default Image
