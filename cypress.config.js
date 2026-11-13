import { defineConfig } from 'cypress'

export default defineConfig({
  projectId: '6eqmcu',
  e2e: {
    baseUrl: 'http://localhost:8000',
    viewportWidth: 1440,
    viewportHeight: 900,
    defaultCommandTimeout: 10000,
    video: false,
    screenshotOnRunFailure: true,
    screenshotsFolder: 'cypress/screenshots',
    specPattern: 'cypress/e2e/**/*.cy.js',
    supportFile: 'cypress/support/e2e.js',
  },
})
