# VK-OAuth #
A powerfull PHP library for the VK OAuth API (vk.com social network).

Supports both usual and [secure](https://vk.com/dev/secure) methods. Fully compatible with VK API version 5.0.  
Easy to handle exceptions.

[VK API documentation](https://vk.com/dev)

## Example ##
You can find example code for authentication and API calling in `examples/` directory.

You need to [create application](https://vk.com/editapp?act=create) and set proper domain name in application settings.

Then replace `{APP_ID}` and `{SECRET}` in example files.


Basically you can use library like that:

```
$wall = $vk->wall_get(array(
    'count' => 10,
    'filter' => 'owner'
));
```

If you want to get endless token use the `offline` scope parameter:  
`$vk->getAuthenticationUrl($currentUrl, 'wall,offline');`

## Installation ##
You can install `vk-oauth` by using [Composer](http://getcomposer.org/)
```
"require": {
    "chekalskiy/vk-oauth": "dev-master"
}
```
  
or by using simple `include`.

## Support ##
email: <ilya@chekalskiy.ru>  
vk: [chekalskiy](https://vk.com/chekalskiy)  
twitter: [@i_compman](https://twitter.com/i_compman)