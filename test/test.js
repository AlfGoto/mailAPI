
const key = '69C00DD51F3F35ABFB1F599AC94668B5827B84BACAE3C1CDE118B3B68D619E64';
const sender = 'alfredgauthier@free.fr'
const content = 'content to send'

const request = 'http://localhost:3000/api?key=' + key + '&fr=' + sender + '&c=' + content
// const request = 'http://localhost:3000/api?key=' + key 


sendMail(request);

async function sendMail(arg) {
    const response = await fetch(arg);
    const json = await response.json(arg);
    console.log(json);
}