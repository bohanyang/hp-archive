import client from '@/api'
import { AuthProvider } from 'react-admin'

const authProvider: AuthProvider = {
  // called when the user attempts to log in
  login: ({ username, password }) => {
    const request = client.path('/api/login').method('post').create()
    return request({ username, password })
  },
  // called when the user clicks on the logout button
  logout: () => {
    const request = client.path('/api/logout').method('post').create()
    return request({}).then(() => Promise.resolve(), () => Promise.reject())
  },
  // called when the API returns an error
  checkError: (data) => {
    console.log(data)

    if (data.status === 401) {
      return Promise.reject()
    }

    return Promise.resolve()
  },
  // called when the user navigates to a new location, to check for authentication
  checkAuth: () => {
    const request = client.path('/api/user').method('get').create()
    return request({}).then(() => Promise.resolve(), () => Promise.reject())
  },
  // called when the user navigates to a new location, to check for permissions / roles
  getPermissions: () => Promise.resolve()
}

export default authProvider
