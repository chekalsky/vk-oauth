# VK-OAuth #
A powerfull PHP library for the VK OAuth API (vk.com social network).

Supports both usual and [secure](https://vk.com/dev/secure) methods. Fully compatible with VK API version 5.0.
Easy to handle exceptions.

[VK API documentation](https://vk.com/dev)

## Example ##
For use example files you need to register application and set proper domain name in application settings.
You can create application [here](https://vk.com/editapp?act=create).

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

## Support ##
email: <ilya@chekaslkiy.ru>

vk: <https://vk.com/chekalskiy>

twitter: [@i_compman](https://twitter.com/i_compman)