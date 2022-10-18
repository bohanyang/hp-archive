import { Container } from '@chakra-ui/react'
import React from 'react'
import { Outlet } from 'react-router-dom'
import Header from './Header'

export default function App () {
  return (
    <>
      <Header />
      <Container maxW="container.xl" flex={1}>
        <Outlet />
      </Container>
    </>
  )
}
