export function wait(ms: number) {
  return new Promise<void>((resolve) => setTimeout(resolve, ms))
}

export function takeAtLeast(p: Promise<any>, ms: number) {
  return new Promise((resolve, reject) => {
    Promise.all([p, wait(ms)]).then(([result]) => {
      resolve(result)
    }, reject)
  })
}

export const ucfirst = (str: string, locale = 'nl') =>
  str.replace(/^\p{CWU}/u, (char) => char.toLocaleUpperCase(locale))

export async function scrollErrorIntoView() {
  await wait(200)

  const el = document.querySelector('.form-error') as HTMLElement
  if (el) {
    el.closest('.field')?.scrollIntoView({
      behavior: 'smooth',
    })
  }
}
