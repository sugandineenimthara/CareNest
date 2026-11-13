describe('Authentication', () => {
  beforeEach(() => {
    cy.visit('/login')
  })

  it('shows login page with CareNest branding', () => {
    cy.contains('CareNest').should('be.visible')
    cy.get('input[name="email"]').should('be.visible')
    cy.get('input[name="password"]').should('be.visible')
    cy.get('button[type="submit"]').should('be.visible')
  })

  it('shows error on invalid credentials', () => {
    cy.get('input[name="email"]').type('wrong@example.com')
    cy.get('input[name="password"]').type('badpassword')
    cy.get('button[type="submit"]').click()
    cy.url().should('include', '/login')
    cy.contains(/invalid|credentials|email|password/i).should('be.visible')
  })

  it('admin can log in and reach dashboard', () => {
    cy.get('input[name="email"]').type('admin@carenest.com')
    cy.get('input[name="password"]').type('password')
    cy.get('button[type="submit"]').click()
    cy.url().should('include', '/admin/dashboard')
    cy.contains('Dashboard').should('be.visible')
  })

  it('midwife can log in and reach dashboard', () => {
    cy.get('input[name="email"]').type('sugandineenimthara@gmail.com')
    cy.get('input[name="password"]').type('password')
    cy.get('button[type="submit"]').click()
    cy.url().should('include', '/midwife/dashboard')
    cy.contains('Dashboard').should('be.visible')
  })

  it('mother logs in and is redirected to My Profile (no dashboard)', () => {
    cy.get('input[name="email"]').type('Nimthara87@gmail.com')
    cy.get('input[name="password"]').type('password')
    cy.get('button[type="submit"]').click()
    cy.url().should('include', '/mother/profile')
    cy.contains('My Profile').should('be.visible')
    cy.contains('Dashboard').should('not.exist')
  })

  it('forgot password page is accessible and shows OTP form', () => {
    cy.contains(/forgot|reset/i).click()
    cy.url().should('include', '/forgot-password')
    cy.get('input[name="email"]').should('be.visible')
    cy.contains(/send code|send reset/i).should('be.visible')
  })

  it('pending user cannot log in', () => {
    cy.visit('/register/mother')
    cy.url().should('include', '/register/mother')
  })
})
