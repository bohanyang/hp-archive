import { Settings } from './settingsAtom'
import { useAtom } from 'jotai'
import settingsAtom from '@/app/settingsAtom'

export default function useSettingValue (key: keyof Settings) {
  const [settings] = useAtom(settingsAtom)
  const value = settings[key]

  return {
    image_size: value
  }
}
