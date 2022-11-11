import React from 'react'
import { Datagrid, EmailField, List, TextField } from 'react-admin'

export default function UserList () {
  return (
    <List>
      <Datagrid rowClick="edit">
        <TextField source="id" />
        <TextField source="name" />
        <TextField source="username" />
        <EmailField source="email" />
        <TextField source="address.street" />
        <TextField source="phone" />
        <TextField source="website" />
        <TextField source="company.name" />
      </Datagrid>
    </List>
  )
}
