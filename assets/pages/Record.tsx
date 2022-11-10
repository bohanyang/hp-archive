import ImageModal from '@/app/ImageModal'
import Layout from '@/app/Layout'
import useSettingValue from '@/app/useSettingValue'
import { Head, Link } from '@inertiajs/inertia-react'
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
  record: {
    description: string
    market: string
    link?: string
    coverstory?: {
      attribute: string
      para1: string
      title: string
    }
    hotspots?: {
      desc: string
      link: string
      query: string
    }[]
    messages?: {
      title: string
      link: string
      text: string
    }[]
  }
  video: string
  formattedDate: string
  date: {
    previous: string
    current: string
    next?: string
  }
}

function Record ({
  image,
  image_origin,
  record,
  video,
  formattedDate,
  date
}: Props) {
  const { t } = useTranslation()
  const { image_size } = useSettingValue('viewSize')
  return (
    <>
      <Head title={record.description} />
      <div className="row">
        <div className="col-xs-12">
          <Link href={`/images/${image.name}`}>
            <img
              src={`${image_origin}${image.urlbase}_${image_size}.jpg`}
              alt={record.description}
              className="img-rounded wallpaper"
            />
          </Link>
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
        <div className="col-xs-12 col-md-6 pgr">
          <Link
            href={`/${date.current}`}
            className="btn btn-embossed btn-primary btn-lg btn-block"
          >
            {t(`markets.${record.market}`) + ` (${formattedDate})`}
          </Link>
        </div>
        <div className="col-xs-6 col-md-3 pgr">
          <Link
            href={`/${record.market}/${date.previous}`}
            className="btn btn-embossed btn-primary btn-lg btn-block"
          >
            <i className="fui-arrow-left"></i>
          </Link>
        </div>
        <div className="col-xs-6 col-md-3 pgr">
          {date.next
            ? (
            <Link
              href={`/${record.market}/${date.next}`}
              className="btn btn-embossed btn-primary btn-lg btn-block"
            >
              <i className="fui-arrow-right"></i>
            </Link>
              )
            : (
            <button
              className="btn btn-embossed btn-default btn-lg btn-block"
              disabled
            >
              <i className="fui-arrow-right"></i>
            </button>
              )}
        </div>
      </div>
      {record.coverstory && (
        <div className="row">
          <div className="col-xs-12 story" lang={record.market}>
            <blockquote>
              <p>
                <strong>
                  {record.coverstory.attribute}: {record.coverstory.title}
                </strong>
              </p>
              <p>{record.coverstory.para1}</p>
            </blockquote>
          </div>
        </div>
      )}
      <div className="row">
        <div className="col-xs-12">
          <table className="table table-striped">
            <tbody>
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
                        <strong>{record.description}</strong>
                      </a>
                        )
                      : (
                      <span className="text-link">
                        <strong>{record.description}</strong>
                      </span>
                        )}
                  </span>
                </td>
              </tr>
              {record.hotspots &&
                record.hotspots.map((hotspot, index) => (
                  <tr key={index}>
                    <td>
                      <span lang={record.market}>
                        {hotspot.desc}
                        <a
                          href={hotspot.link}
                          target="_blank"
                          rel="nofollow noreferrer"
                        >
                          <strong>{hotspot.query}</strong>
                        </a>
                      </span>
                    </td>
                  </tr>
                ))}
              {record.messages &&
                record.messages.map((message, index) => (
                  <tr key={index}>
                    <td>
                      <span lang={record.market}>
                        {message.title}
                        <a
                          href={message.link}
                          target="_blank"
                          rel="nofollow noreferrer"
                        >
                          <strong>{message.text}</strong>
                        </a>
                      </span>
                    </td>
                  </tr>
                ))}
            </tbody>
          </table>
        </div>
      </div>
      <ImageModal {...{ image, image_origin }} />
    </>
  )
}

Record.layout = (page: JSX.Element) => <Layout children={page} />

export default Record
