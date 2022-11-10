export default function flags(market: string) {
  return new URL(`./${market}.png`, import.meta.url).href
}
