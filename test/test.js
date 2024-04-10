
test()
async function test(){
    const response = await fetch('http://localhost:3000/api?st=alfgoto@gmail.com&fr=alfredgauthier@free.fr&c=les espaces marchent ?')
    const jason = await response.json()
    console.log(jason)
}