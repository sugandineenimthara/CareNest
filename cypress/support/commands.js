Cypress.Commands.add('login', (email, password) => {
  cy.session([email, password], () => {
    cy.visit('/login')
    cy.get('input[name="email"]').type(email)
    cy.get('input[name="password"]').type(password)
    cy.get('button[type="submit"]').click()
    cy.url().should('not.include', '/login')
  })
})

Cypress.Commands.add('logout', () => {
  cy.get('form[action*="logout"] button, button[onclick*="logout"]').first().click({ force: true })
  cy.url().should('include', '/login')
})
