import client, { Method, Path, RequestArgs, ProblemDocument } from '.'

export default async function fetcher<P extends Path, M extends Method<P>> (
  path: P,
  method: M,
  args: RequestArgs<P, M>,
  init?: RequestInit
) {
  const typedFetch = client.path(path).method(method).create()

  try {
    const response = await typedFetch(args, init)

    return response.data
  } catch (e) {
    if (!(e instanceof typedFetch.Error)) {
      console.error(e)
      throw {
        type: 'unknown',
        status: 500,
        title: 'Unknown error occurred.'
      } as ProblemDocument
    }

    const error = e.data as ProblemDocument
    error.status ??= e.status

    throw error
  }
}
