import React from 'react'
import { Admin, Resource } from 'react-admin';
import jsonServerProvider from 'ra-data-json-server'
import UserList from './UserList'
import authProvider from './authProvider'

const dataProvider = jsonServerProvider('https://jsonplaceholder.typicode.com')

export default function App () {
  return <Admin dataProvider={dataProvider} authProvider={authProvider}>
    <Resource name="users" list={UserList} />
  </Admin>
}
