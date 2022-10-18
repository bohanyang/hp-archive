import { Container, Flex, Heading } from '@chakra-ui/react'
import React from 'react'

export default function Header () {
  return (
    <Flex
      justify="flex-start"
      pos="fixed"
      top="0"
      w="full" // --chakra-sizes-full: 100%
      minH="60px"
      boxShadow="base" // --chakra-shadows-xs~2xl
      zIndex="999" // top layer
      align="center"
      backdropFilter="saturate(180%) blur(5px)"
      backgroundColor="rgba(255, 255, 255, 0.8)"
    >
      <Container maxW="container.xl" as={Flex} align="center" gap="10" h="60px">
        <Heading as="h1" size="xl">
          Store
        </Heading>
      </Container>
    </Flex>
  )
}
