describe('Clinic Attendances (Admin)', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/attendances')
  })

  it('renders clinic attendances page', () => {
    cy.url().should('include', '/admin/attendances')
    cy.contains(/clinic attendance/i).should('be.visible')
  })

  it('shows clinic type options (Mother / Pediatric)', () => {
    cy.contains(/mother|pediatric/i).should('be.visible')
  })
})

describe('Mother Requests (Admin)', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/mother-requests')
  })

  it('renders mother requests page', () => {
    cy.url().should('include', '/admin/mother-requests')
    cy.contains(/mother request/i).should('be.visible')
  })
})

describe('Mother Requests (Midwife)', () => {
  beforeEach(() => {
    cy.login('sugandineenimthara@gmail.com', 'password')
    cy.visit('/midwife/mother-requests')
  })

  it('renders mother requests page for midwife', () => {
    cy.url().should('include', '/midwife/mother-requests')
    cy.contains(/mother request/i).should('be.visible')
  })
})

describe('High-Risk Alerts (Admin)', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/alerts')
  })

  it('renders alerts page', () => {
    cy.url().should('include', '/admin/alerts')
    cy.contains(/alert|high.risk|blood pressure/i).should('be.visible')
  })
})

describe('Triposha Book (Admin)', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/admin/triposha')
  })

  it('renders triposha book page', () => {
    cy.url().should('include', '/admin/triposha')
    cy.contains(/triposha/i).should('be.visible')
  })
})

describe('Password Change', () => {
  beforeEach(() => {
    cy.login('admin@carenest.com', 'password')
    cy.visit('/change-password')
  })

  it('renders change password page', () => {
    cy.url().should('include', '/change-password')
    cy.get('h1, h2').contains(/change password/i).should('exist')
    cy.get('input[name="current_password"]').should('exist')
    cy.get('input[name="password"]').should('exist')
    cy.get('input[name="password_confirmation"]').should('exist')
  })
})
