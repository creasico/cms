describe('Just for fun', () => {
    it('shoud do something', () => {
        browser.url('http://creasi.co');
        browser.getTitle().should.be.equal('Welcome to nginx!');
    });
});
