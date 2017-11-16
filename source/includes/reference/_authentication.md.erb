# Authentication

Zoom API version 2 implements [JSON Web Tokens](https://jwt.io) (JWT) for authentication. It is recommended you use one of the existing JWT [libraries](https://jwt.io/#libraries) to generate the token.

You can pass the token to the API either in the HTTP Authorization Header using 'Bearer' or via Query Parameter in your API call as 'access_token'.

Header

`Authorization: Bearer <token>`

Query Parameter

`<api_path>?access_token=<token>`

## Generating Token

You can find more details and specifics about JWT at [jwt.io](https://jwt.io), below is and example of the minimum properties needed for the Zoom API.

### Header
```json
{
    "alg": "HS256",
    "typ": "JWT"
}
```

`alg` refers to the algorithm being used, Zoom API uses HMAC SHA256 (HS256 for short)

`typ` refers to the type of token, always JWT

### Payload
```json
{
    "iss": "API_KEY",
    "exp": 1496091964000
}
```

`iss` is the issuer of the token, this is your Zoom API Key

`exp` is the expiration timestamp of the token. It is recommended to use one of the JWT libraries to generate your tokens and to set this timestamp for a short period (like seconds), that way if someone intercepts your token it won't be valid for very long.

### Signature

> Algorithm

```java
HMACSHA256(
    base64UrlEncode(header) + "." +
    base64UrlEncode(payload),
    api_secret)
``` 

> Node.js Library example

```javascript
var jwt = require('jsonwebtoken');

var payload = {
    iss: api_key,
    exp: ((new Date()).getTime() + 5000)
};

//Automatically creates header, and returns JWT
var token = jwt.sign(payload, api_secret);
``` 

To the right you'll find an example algorithm for generating a signature, again highly recommend using a library to do this for you


## Example

The example below is written in PHP and utilizes the [Firebase JWT Library](https://github.com/firebase/php-jwt) installed via [Composer](https://getcomposer.org/). However the concept and idea can be ported to any language and workflow and you can find libraries for your specific language on [jwt.io](https://jwt.io/#libraries)

The key thing to understand is that the JWT token isn't static, and that it is generated each time you make a request. This makes it unique and more secure; if somehow your token is intercepted it won't be good for long (as in this example 60 seconds)

<div style='width:55%'>
<script src="https://gist.github.com/joshuawoodward/929e1743036a9512692c1c02c606d04d.js"></script>
</div>