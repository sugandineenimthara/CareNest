describe('Mother Portal - My Profile', () => {
  beforeEach(() => {
    cy.login('Nimthara87@gmail.com', 'password')
    cy.visit('/mother/profile')
  })

  it('shows My Profile page after login', () => {
    cy.url().should('include', '/mother/profile')
    cy.contains('My Profile').should('be.visible')
  })

  it('sidebar does not contain Dashboard link', () => {
    cy.contains('.sidebar', 'Dashboard').should('not.exist')
  })

  it('sidebar shows My Profile link', () => {
    cy.contains('.sidebar', 'My Profile').should('be.visible')
  })

  it('sidebar shows My Children link', () => {
    cy.contains('.sidebar', 'My Children').should('be.visible')
  })

  it('sidebar shows Immunizations link', () => {
    cy.contains('.sidebar', 'Immunizations').should('be.visible')
  })

  it('sidebar shows Notifications link', () => {
    cy.contains('.sidebar', 'Notifications').should('be.visible')
  })

  it('displays personal details section', () => {
    cy.contains(/personal details|profile/i).should('be.visible')
  })
})

describe('Mother Portal - My Children', () => {
  beforeEach(() => {
    cy.login('Nimthara87@gmail.com', 'password')
    cy.visit('/mother/children')
  })

  it('renders My Children page', () => {
    cy.contains('My Children').should('be.visible')
    cy.url().should('include', '/mother/children')
  })

  it('does not show Register New Child button', () => {
    cy.contains('Register New Child').should('not.exist')
  })

  it('does not show area filter pills', () => {
    cy.contains(/filter area/i).should('not.exist')
    cy.contains(/separated by clinic areas/i).should('not.exist')
  })

  it('shows children cards or empty state', () => {
    cy.get('body').then(($body) => {
      if ($body.find('.child-card').length > 0) {
        cy.get('.child-card').first().should('be.visible')
      } else {
        cy.contains(/no children|your registered children will appear/i).should('be.visible')
      }
    })
  })
})

describe('Mother Portal - Immunizations', () => {
  beforeEach(() => {
    cy.login('Nimthara87@gmail.com', 'password')
    cy.visit('/mother/immunizations')
  })

  it('renders immunizations page for mother', () => {
    cy.url().should('include', '/mother/immunizations')
    cy.contains(/my maternal immunization|immunization record/i).should('be.visible')
  })

  it('shows Ja-Ela District Hospital as venue', () => {
    cy.contains('Ja-Ela District Hospital').should('be.visible')
  })

  it('shows 09:00 AM time for appointments', () => {
    cy.contains('09:00 AM').should('be.visible')
  })

  it("shows children's immunization section", () => {
    cy.contains(/children.*immunization/i).should('be.visible')
  })
})

describe('Mother Portal - Notifications', () => {
  beforeEach(() => {
    cy.login('Nimthara87@gmail.com', 'password')
    cy.visit('/mother/notifications')
  })

  it('renders notifications page', () => {
    cy.url().should('include', '/mother/notifications')
    cy.contains('Ja-Ela District Hospital').should('be.visible')
    cy.contains('09:00 AM').should('be.visible')
  })
})
