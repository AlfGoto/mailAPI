
test()
async function test(){
    const response = await fetch('https://alfpi.top:3000/api?st=alfgoto@gmail.com&fr=alfredgauthier@free.fr&c=les espaces marchent ?', {mode: 'no-cors'})
    const jason = await response.json()
    console.log(jason)
}