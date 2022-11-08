import {
  Code,
  Flex,
  Heading,
  Table,
  TableContainer,
  Tbody,
  Td,
  Th,
  Thead,
  Tr
} from '@chakra-ui/react'
import React from 'react'
import Layout from '../app/Layout'

function Homepage ({
  version,
  projectDir,
  docVersion
}: {
  version: string
  projectDir: string
  docVersion: string
}) {
  return (
      <Flex align="center" justify="center" direction='column' gap={8} mt="120px">
        <Heading fontSize="5xl">Hello World!</Heading>
        <TableContainer>
          <Table variant="simple">
            <Thead>
              <Tr>
                <Th>Version</Th>
                <Th>Project Dir</Th>
                <Th>Doc Version</Th>
              </Tr>
            </Thead>
            <Tbody>
              <Tr>
                <Td>{version}</Td>
                <Td><Code>{projectDir}</Code></Td>
                <Td>{docVersion}</Td>
              </Tr>
            </Tbody>
          </Table>
        </TableContainer>
      </Flex>
  )
}

Homepage.layout = (page: JSX.Element) => <Layout children={page} />

export default Homepage
