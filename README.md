# Mautic Remote Login

## Install instructions

1. Run command on terminal
```
git clone https://github.com/deakao/mautic-plugin-remote-login.git plugins/RemoteLoginBundle
```
2. Clean mautic cache
```
php app/console cache:clear
```
3. Install plugins on mautic
- Go to plugins and press "Install/Upgrade plugins"



## Usage:

### GENERATE SECRET
Get PrivateKey from Controller/DefaultController.php on line 16

STEP 1 - MD5 the PrivateKey
https://www.md5hashgenerator.com/

STEP 2- SHA1 (PrivateKey+Email) = EXAMPLE: MD5HASHmyemail@gmail.com
http://www.sha1-online.com/

secret = STEP2-HASHED-KEY-HERE

## Process Remote Login
### POST METHOD
 - http://yourmaticurl.com/remotelogin/{$useremail}
 - Params: { secret }
### GET METHOD
 - http://yourmaticurl.com/remotelogin/{$useremail}?secret={secret}