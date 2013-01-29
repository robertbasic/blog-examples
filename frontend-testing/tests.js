var casper = require('casper').create();
phantom.cookiesEnabled = true;

casper.start('http://localhost/frontend-testing', function () {
    this.test.assertUrlMatch(/login.php$/, 'Redirected to login page');
    this.test.assertExist("#login_form", 'Login form exists');

    this.fill('#login_form', {
        'email': 'email@example.com'
    }, false); // false means don't autosubmit the form

    this.test.assertField('email', 'email@example.com');
});

casper.thenClick('#login', function () {
    this.test.assertUrlMatch(/index.php$/, 'Redirected to index page after login');
});

casper.then(function () {
    this.test.assertTextExist('Hello email@example.com.', 'Greeting message exists');
    // I could swear I had this one working
    // this.test.assertEquals(this.getElementAttribute('#enable_ajax', 'checked'), '', 'Checkbox is not checked');
    this.test.assertFalse(this.evaluate(function () { 
        return document.getElementById("enable_ajax").checked;
    }), 'Checkbox is not checked');
});

casper.thenClick('#do_ajax', function () {
    this.test.assertTextDoesntExist('Just some ajax response.', 'Ajax request was not made');
});

casper.thenClick('#enable_ajax', function () {
    // I could swear I had this one working
    // this.test.assertEquals(this.getElementAttribute('#enable_ajax', 'checked'), 'checked', 'Checkbox is checked');
    this.test.assertTrue(this.evaluate(function () { 
        return document.getElementById("enable_ajax").checked;
    }), 'Checkbox is checked');
});

casper.thenClick('#do_ajax', function () {
    this.waitForResource('http://localhost/frontend-testing/ajax.php');
});

casper.then(function () {
    // Sometimes we need to wait a bit more for ajax requests ...
    this.wait(50);
});

casper.then(function () {
    this.test.assertTextExist('Just some ajax response.', 'Ajax request was made');
});

casper.thenClick('#clear_ajax', function () {
    this.test.assertTextDoesntExist('Just some ajax response.', 'Ajax request response was cleared');
});

casper.run(function () {
    this.test.done();
    this.test.renderResults(true); // true means exit when it's done
});
