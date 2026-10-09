> [!NOTE]
> Originally based on work by `@codedipper`, whose GitHub account no longer
> exists. The steps below are self-contained and do not need that
> repository.

0. This was tested on an Arch Linux setup, packages `base` and `tor` must be installed to get the proxy working.\
LibreY has other requirements as well.\
Slight differences in Tor configurations may be needed across different systems.

1. Create `/etc/tor/torrc` and edit it to your liking
```
SOCKSPort 127.0.0.1:9050
SOCKSPolicy accept 127.0.0.1/32
SOCKSPolicy reject *

DataDirectory /var/lib/tor
User tor

Log notice stdout
RunAsDaemon 0

# UseBridges 1
# Bridge xx.xxx.xxx.xxx:xx
```

2. Secure directory permissions
```
chown -R tor: /usr/share/tor
chown -R tor: /var/lib/tor
chown -R tor: /etc/tor
chmod -R 0700 /usr/share/tor
chmod -R 0700 /var/lib/tor
chmod -R 0700 /etc/tor
```

3. Start Tor and enable it on boot
```
systemctl enable --now tor.service
```

4. Install LibreY
https://github.com/Ahwxorg/LibreY/tree/main/docs#readme

5. Edit configuration files
In your `config.php`, uncomment and change the following options to match the port you're running Tor on.
```
            // CURLOPT_PROXY => "ip:port",
            // CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
```

For our configuration, they would be changed to:
```
            CURLOPT_PROXY => "127.0.0.1",
            CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,
```


6. Start LibreY! You can use a tool like [iftop](https://archlinux.org/packages/extra/x86_64/iftop/) to make sure your LibreY instance is connecting to guard nodes or a bridge instead of Google servers.

To update Tor and LibreY:
```
pacman -Syu
git pull
```
