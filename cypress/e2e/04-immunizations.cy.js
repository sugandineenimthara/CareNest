describe('Immunizations Page (Admin)', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/immunizations')
  })

  it('renders immunizations page', () => {
    cy.url().should('include', '/admin/immunizations')
    cy.contains(/immunization|vaccine/i).should('be.visible')
  })

  it('shows Children and Mothers tabs', () => {
    cy.contains(/children/i).should('be.visible')
    cy.contains(/mothers/i).should('be.visible')
  })

  it('children tab shows EPI schedule stats', () => {
    cy.contains(/children/i).click({ force: true })
    cy.contains(/vaccinated|schedule|epi/i).should('be.visible')
  })

  it('mothers tab shows Tetanus records', () => {
    cy.contains(/mothers/i).click({ force: true })
    cy.contains(/tetanus|mother|TT/i).should('be.visible')
  })
})

describe('Immunizations Page (Midwife)', () => {
  beforeEach(() => {
    cy.login('sugandineenimthara@gmail.com', 'password')
    cy.visit('/midwife/immunizations')
  })

  it('renders immunizations page for midwife', () => {
    cy.url().should('include', '/midwife/immunizations')
    cy.contains(/immunization|vaccine/i).should('be.visible')
  })
})
