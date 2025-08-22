import antfu from '@antfu/eslint-config'

export default antfu({
  formatters: true,
  vue: true,
  typescript: true,
}, {
  files: ['**/*.vue'],
  rules: {
    'vue/operator-linebreak': ['error', 'before'],
    'vue/component-name-in-template-casing': ['error', 'kebab-case', {
      registeredComponentsOnly: false,
      ignores: [],
    }],
  },
}, {
  files: ['**/*.html'],
  rules: {
    'format/prettier': 'off',
  },
}, {
  rules: {
    'style/semi': ['error', 'never'],
    'no-console': 'warn',
    'arrow-parens': ['error', 'as-needed'],
    'ts/ban-ts-comment': 'off',
    'style/eol-last': 'off',
    'style/arrow-parens': 'off',
    'unused-imports/no-unused-vars': 'warn',
    'node/prefer-global/process': 'off',
    'regexp/no-unused-capturing-group': ['error', { fixable: true }],
  },
})
