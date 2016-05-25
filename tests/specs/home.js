describe('Just for fun', () => {
    it ('shoud do something', () => {
        browser.url('http://localhost:8080')
        browser.getTitle().should.be.equal('Anu')
    })
})
