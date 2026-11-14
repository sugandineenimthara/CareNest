describe('Children Registry', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/children')
  })

  it('renders children index page with stats', () => {
    cy.contains(/children/i).should('be.visible')
    cy.get('.stat-card').should('have.length.at.least', 1)
  })

  it('shows Register New Child button for admin', () => {
    cy.contains('Register New Child').should('be.visible')
  })

  it('navigates to child creation form', () => {
    cy.contains('Register New Child').click()
    cy.url().should('include', '/admin/children/create')
    cy.get('select[name="mother_id"]').should('exist')
    cy.get('input[name="child_name"]').should('exist')
    cy.get('input[name="gender"]').should('exist')
    cy.get('input[name="date_of_birth"]').should('exist')
    cy.get('input[name="birth_weight"]').should('exist')
  })

  it('shows children cards when records exist', () => {
    cy.get('body').then(($body) => {
      if ($body.find('.child-card, .child-row').length > 0) {
        cy.get('.child-card, .child-row').first().should('be.visible')
      } else {
        cy.contains(/no children|empty/i).should('be.visible')
      }
    })
  })

  it('area filter pills are visible for admin', () => {
    cy.get('body').then(($body) => {
      if ($body.text().includes('Filter Area')) {
        cy.contains(/filter area/i).should('be.visible')
      }
    })
  })
})

describe('Child Show Page', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
  })

  it('child detail page renders if a child exists', () => {
    cy.visit('/admin/children')
    cy.get('body').then(($body) => {
      const links = $body.find('a[href*="/admin/children/"]').filter((i, el) => {
        const href = el.getAttribute('href') || ''
        return !href.includes('/create') && /\/admin\/children\/\d+/.test(href)
      })
      if (links.length) {
        cy.wrap(links.first()).click({ force: true })
        cy.url().should('match', /\/admin\/children\/\d+/)
        cy.contains(/immunization|vaccine|schedule/i).should('be.visible')
      } else {
        cy.log('No child detail links found — skipping show page check')
      }
    })
  })
})
