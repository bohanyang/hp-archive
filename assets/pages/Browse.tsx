import useSettingValue from '@/app/useSettingValue'
import { Link, Head } from '@inertiajs/inertia-react'
import React from 'react'
import { useTranslation } from 'react-i18next'
import Layout from '../app/Layout'

interface Props {
  image_origin: string
  images: {
    name: string
    urlbase: string
  }[]
  prevCursor?: string
  nextCursor?: string
}

function Browse ({ image_origin, images, prevCursor, nextCursor }: Props) {
  const { t } = useTranslation()
  const { image_size } = useSettingValue('browseSize')
  return (
    <>
      <Head title={t('browse')} />
      <div className="row">
        <div className="col-xs-12 pgr">
          <h5 className="text-primary">{t('browse')}</h5>
        </div>
      </div>
      {images.map((image) => (
        <div className="row" key={image.name}>
          <div className="col-xs-12">
            <Link href={`/images/${image.name}`}>
              <img
                src={`${image_origin}${image.urlbase}_${image_size}.jpg`}
                alt={image.name}
                className="img-rounded wallpaper"
              />
            </Link>
          </div>
          <div className="col-xs-12">
            <p className="text-left">
              <Link href={`/images/${image.name}`}>{image.name}</Link>
            </p>
          </div>
        </div>
      ))}
      <div className="row">
        <div className="col-xs-6 pgr">
          {prevCursor
            ? (
            <Link
              href={`/browse/${prevCursor}`}
              className="btn btn-embossed btn-primary btn-lg btn-block"
            >
              <i className="fui-arrow-left"></i>
            </Link>
              )
            : (
            <button
              className="btn btn-embossed btn-default btn-lg btn-block"
              disabled
            >
              <i className="fui-arrow-left"></i>
            </button>
              )}
        </div>
        <div className="col-xs-6 pgr">
          {nextCursor
            ? (
            <Link
              href={`/browse/${nextCursor}`}
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
    </>
  )
}

Browse.layout = (page: JSX.Element) => <Layout children={page} />

export default Browse
