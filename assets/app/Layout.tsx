import { Container, ChakraProvider } from '@chakra-ui/react'
import React from 'react'
import Header from './Header'

export default function Layout ({ children }: { children: JSX.Element }) {
  return (
    <React.StrictMode>
      <ChakraProvider>
        <Header />
        <Container maxW="container.xl" flex={1}>
          {children}
        </Container>
      </ChakraProvider>
    </React.StrictMode>
  )
}
