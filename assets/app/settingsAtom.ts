import atomWithLocalStorage from "./atomWithLocalStorage"

export interface Settings {
  viewSize: string
  browseSize: string
}

const settingsAtom = atomWithLocalStorage<Settings>('settings', {
  viewSize: '1920x1080',
  browseSize: '1366x768'
})

export default settingsAtom
