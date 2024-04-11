const express = require('express');
const nodemailer = require("nodemailer");
const bodyParser = require('body-parser');
var cors = require('cors')
const app = express();
app.use(cors())

app.use(bodyParser.json());

const port = process.env.PORT || 3000;
app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
});
app.get('/api', (req, res) => {
    const sendTo = req.query.st
    const from = req.query.fr
    const content = req.query.c

    console.log({from: "alfpi.app@gmail.com",to: sendTo,subject: "mail from : " + from,text: content,})

    transporter.sendMail({
        from: "alfpi.app@gmail.com",
        to: sendTo,
        subject: "mail from : " + from,
        text: content,

    }, (error, info) => {
        if (error) {
            res.json({ result: 'email has not been sent' })
        } else {
            res.json({ result: 'email was sent !' })
        }
    });
    // res.json({ destinataire: sendTo, envoyeur: from, contenu: content });
});

// let transporter = nodemailer.createTransport({
//     service: 'gmail', auth: {
//         user: 'alfpi.app@gmail.com', pass: "qpxe jsxa rywa jwab "
//     }
// });
const transporter = nodemailer.createTransport({
    service: "Gmail",
    host: "smtp.gmail.com",
    port: 465,
    secure: true,
    auth: {
        user: "alfpi.app@gmail.com",
        pass: "qpxe jsxa rywa jwab ",
    },
});



//?st=MAILTOSEND&fr=FROM&c=CONTENT