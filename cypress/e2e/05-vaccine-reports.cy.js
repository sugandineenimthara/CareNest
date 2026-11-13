describe('Vaccine Reports (Admin)', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/vaccine-reports')
  })

  it('renders vaccine reports page', () => {
    cy.url().should('include', '/admin/vaccine-reports')
    cy.contains(/vaccine report/i).should('be.visible')
  })

  it('shows date filter input', () => {
    cy.get('input[name="date_from"], input[name="from_date"], input[type="date"]')
      .should('have.length.at.least', 1)
  })

  it('shows clinic type filter (Pediatric / Mother)', () => {
    cy.get('select[name="clinic_type"], input[name="clinic_type"]').then(($el) => {
      if ($el.length) {
        cy.wrap($el).should('be.visible')
      } else {
        cy.contains(/pediatric|mother clinic/i).should('be.visible')
      }
    })
  })

  it('shows vaccine table with stock information', () => {
    cy.contains(/vaccine name|type/i).should('be.visible')
    cy.contains(/received|stock|storage/i).should('be.visible')
    cy.contains(/used|administered/i).should('be.visible')
    cy.contains(/remaining|left/i).should('be.visible')
  })

  it('filter by date submits without error', () => {
    cy.get('input[type="date"]').first().type('2026-11-01')
    cy.get('input[type="date"]').first().closest('form').then(($form) => {
      const method = $form.attr('method') || 'GET'
      if (method.toUpperCase() === 'GET') {
        cy.wrap($form).submit()
      } else {
        cy.get('button[type="submit"]').first().click({ force: true })
      }
    })
    cy.url().should('include', '/admin/vaccine-reports')
    cy.contains(/vaccine report/i).should('be.visible')
  })
})

describe('Vaccine Reports (Midwife)', () => {
  beforeEach(() => {
    cy.login('sugandineenimthara@gmail.com', 'password')
    cy.visit('/midwife/vaccine-reports')
  })

  it('renders vaccine reports page for midwife', () => {
    cy.url().should('include', '/midwife/vaccine-reports')
    cy.contains(/vaccine report/i).should('be.visible')
  })
})
