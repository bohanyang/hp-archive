import { Settings } from './settingsAtom'
import { useAtom } from 'jotai'
import settingsAtom from '@/app/settingsAtom'

export default function useSettingValue (key: keyof Settings) {
  const [settings] = useAtom(settingsAtom)
  const value = settings[key]
  const [width, height] = value.split('x', 2)

  return {
    image_size: value,
    width: parseInt(width) + 'px',
    height: parseInt(height) + 'px',
  }
}
