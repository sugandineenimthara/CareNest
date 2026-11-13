describe('Admin Dashboard', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/dashboard')
  })

  it('shows key stat cards', () => {
    cy.contains(/registered mothers/i).should('be.visible')
    cy.contains(/children enrolled/i).should('be.visible')
    cy.contains(/immunizations due/i).should('be.visible')
    cy.contains(/active alerts|high.risk/i).should('be.visible')
  })

  it('sidebar navigation links are present', () => {
    cy.contains('Dashboard').should('be.visible')
    cy.contains('Manage Mothers').should('be.visible')
    cy.contains('Children').should('be.visible')
    cy.contains('Immunizations').should('be.visible')
    cy.contains('Vaccine Reports').should('be.visible')
    cy.contains('Mother Requests').should('be.visible')
  })

  it('sidebar does not show Register Mother button', () => {
    cy.contains('Register Mother').should('not.exist')
  })

  it('navigates to Manage Mothers page', () => {
    cy.contains('Manage Mothers').click()
    cy.url().should('include', '/admin/mothers')
    cy.contains(/mothers|maternal/i).should('be.visible')
  })

  it('navigates to Children page', () => {
    cy.contains('Children').click()
    cy.url().should('include', '/admin/children')
    cy.contains(/children|registered/i).should('be.visible')
  })

  it('navigates to Immunizations page', () => {
    cy.contains('Immunizations').click()
    cy.url().should('include', '/admin/immunizations')
    cy.contains(/immunization|vaccine/i).should('be.visible')
  })

  it('navigates to Vaccine Reports page', () => {
    cy.contains('Vaccine Reports').click()
    cy.url().should('include', '/admin/vaccine-reports')
    cy.contains(/vaccine report/i).should('be.visible')
  })

  it('navigates to Alerts page', () => {
    cy.contains(/high.risk|alerts/i).first().click()
    cy.url().should('include', '/admin/alerts')
  })

  it('navigates to Clinic Attendances page', () => {
    cy.contains('Clinic Attendances').click()
    cy.url().should('include', '/admin/attendances')
    cy.contains(/attendance|clinic/i).should('be.visible')
  })
})
