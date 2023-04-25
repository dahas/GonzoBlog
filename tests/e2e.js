import { Selector } from 'testcafe';

fixture('E2E Testing')
    .page('http://localhost:2400/Blog/Article?article=2&expanded=true');

test('Leave a comment', async t => {
    await t
        .typeText('#simple-mde-editor', 'Hello End to End!')
        .click('.card-body .btn-primary')
        // .expect(Selector('#article-header').innerText).eql('Thank you, John Smith!')
        ;
});