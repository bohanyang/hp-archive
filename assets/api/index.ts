import { Fetcher, OpArgType } from 'openapi-typescript-fetch'
import { paths } from './docs'

const client = Fetcher.for<paths>()

export type Path = keyof paths
export type Method<P extends Path> = keyof paths[P]
export type RequestArgs<P extends Path, M extends Method<P>> = OpArgType<paths[P][M]>

export default client

export interface Violation {
  propertyPath: string
  message: string
  code: string
}

export interface ProblemDocument {
  type: string
  status: number
  title?: string
  detail?: string
}

export interface ValidationError extends ProblemDocument {
  violations: Violation[]
}
