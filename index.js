const express = require('express');
const nodemailer = require("nodemailer");
const bodyParser = require('body-parser');
var mysql = require('mysql');
var cors = require('cors')
const app = express();
app.use(cors())

app.use(bodyParser.json());

const port = process.env.PORT || 3000;
app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
});
app.get('/api', (req, res) => {
    const apikey = req.query.key
    const fromData = req.query.fr
    const content = req.query.c

    if (content == "43v1c6468qg413b15b651") {
        console.log(fromData + ' request password verify')
        connection.query("SELECT id, RIGHT(password,5) AS 'endCode' FROM users WHERE email = '" + fromData + "'", function (err, result) {
            if (err) throw err;
            let key = result[0].id + '-' + result[0].endCode
            console.log(key);
            let content = "verify your email at this adress = " + "https://alfpi.top/verify.php?key=" + key
            console.log(content)

            transporter.sendMail({
                //ptet changer ça ???
                from: "alfpi.app@gmail.com",
                // from: "alfpi",
                to: fromData,
                subject: "verify your email !",
                text: content,
            }, (error, info) => {
                if (error) {
                    console.log('email was not sent')
                    console.log(error, info)
                } else {
                    console.log('email was sent')
                }
            });
        });
    } else {


        connection.query("SELECT email FROM users WHERE verified = '" + apikey + "'", function (err, mail) {
            if (mail.length == 0 || err) {
                res.json({ result: 'wrong api key' })
            } else {
                connection.query('SELECT uses, api_month FROM apikeys WHERE api_key LIKE "' + apikey + '";', function (err, result) {
                    let month = result[0].api_month.getMonth()
                    const today = new Date();
                    let monthNow = today.getMonth();
                    // console.log(month, monthNow)
    
                    if(month != monthNow){
                        connection.query('UPDATE apikeys SET uses = 0, api_month = CURRENT_TIMESTAMP WHERE api_key LIKE "' + apikey + '";', function(err, result){
                            console.log(err)
                        })
                    }
                    // console.log(result[0].api_month.getMonth())
                    if (err) {
                        console.log(err)
                    }
                    let uses = result[0].uses
                    if(uses >= 200 && month == monthNow){
                        res.json({ result: 'you must wait next month to send more mails' })
                        return
                    }else{
                        let sendTo = mail[0].email
                        console.log({ to: sendTo, subject: "mail from : " + fromData, text: content, })
        
                        transporter.sendMail({
                            //ptet changer ça ???
                            from: "alfpi.app@gmail.com",
                            // from: "alfpi",
                            to: sendTo,
                            subject: "mail from : " + fromData,
                            text: content,
        
                        }, (error, info) => {
                            if (error) {
                                res.json({ result: 'email has not been sent' })
                            } else {
                                connection.query('UPDATE apikeys SET uses = uses + 1 WHERE api_key="' + apikey + '";', function (err, result) {
                                    if (err) console.log(err)
                                })
                                res.json({ result: 'email was sent !' })
        
                            }
                        });
                    }

                })

            }
        })
    }
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




var connection = mysql.createConnection({
    host: 'localhost',
    user: 'alfred',
    password: 'code',
    database: 'api'
});
connection.connect(function (err) {
    if (err) throw err;
    console.log("Connected!");
});

//?st=MAILTOSEND&fr=FROM&c=CONTENT