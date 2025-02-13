import path from 'node:path'

const buildEslintCommand = (filenames) =>
  `eslint --fix ${filenames.map((f) => path.relative(process.cwd(), f)).join(' ')}`

const buildTscCommand = () => `vue-tsc -p tsconfig.json --noEmit`

const buildStylelintCommand = (filenames) =>
  `stylelint --fix ${filenames.map((f) => path.relative(process.cwd(), f)).join(' ')}`

const buildPrettierCommand = (filenames) =>
  `prettier --write ${filenames.map((f) => path.relative(process.cwd(), f)).join(' ')}`

const buildPhpLintCommand = () => `./vendor/bin/phplint ./app --no-interaction --no-cache`
const buildPintCommand = () => `./vendor/bin/pint`
const buildPhpStanCommand = () => `./vendor/bin/phpstan analyse --memory-limit=2G`
const buildComposerDepAnalyserCommand = () => `./vendor/bin/composer-dependency-analyser`
const buildComposerCommand = () => `composer validate --no-check-publish --strict`

export default {
  '*.{js,ts,vue}': [buildTscCommand, buildEslintCommand],
  '*.{css,vue}': [buildStylelintCommand],
  '*.{css,yaml}': [buildPrettierCommand],
  '*.php': [buildPhpLintCommand, buildPintCommand, buildPhpStanCommand, buildComposerDepAnalyserCommand],
  'composer.json': [buildComposerCommand],
}
