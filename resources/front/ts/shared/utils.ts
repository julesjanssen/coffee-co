export function capitalize(value: string, locale = navigator.language) {
  return value.replace(/^\p{CWU}/u, (char) => char.toLocaleUpperCase(locale))
}
