> [!WARNING]
> This page relies on the `codedipper/LibreY-Tor` helper repository and on the
> `ghcr.io/codedipper/librey-tor` image. That account no longer exists, so the
> `git clone https://github.com/codedipper/LibreY-Tor.git` step below cannot be
> followed and that image can no longer be pulled.
>
> To run LibreY behind a Tor SOCKS proxy without it:
>
> - run the LibreY container with `CURLOPT_PROXY_ENABLED=true`,
>   `CURLOPT_PROXY=<tor-host>:9050` and
>   `CURLOPT_PROXYTYPE=CURLPROXY_SOCKS5_HOSTNAME`, with Tor running separately
>   (see [docker/README.md](../docker/README.md) and
>   [docker-compose.yml](../docker-compose.yml))
> - or use the self-contained [system tor proxy](system-tor-proxy.md) setup
>
> The steps below are kept for reference. Originally based on work by
> @codedipper.

Step 0: Install and configure Docker.\
Step 1: Enter a root shell on the host machine.

Step 2: Clone the required git repositories to the host machine.
```sh
git clone https://github.com/Ahwxorg/LibreY.git LibreY/
git clone https://github.com/codedipper/LibreY-Tor.git LibreY-Tor/
docker network create --driver=bridge --subnet=172.4.0.0/16 librey_tor_net
```

Step 3: Manually edit `LibreY-Tor/torrc` to configure Tor to your liking.

Step 4: Build LibreY + LibreY-Tor image.
```sh
cd LibreY/
docker buildx build --rm --no-cache --pull -t librey:latest .
cd ../LibreY-Tor/
docker buildx build --rm --no-cache --pull -t librey-tor:latest .
```

Step 5: Start your desired containers with the options you want.

```sh
cd ../LibreY/
docker run -d --name librey --ip=172.4.0.6 --network librey_tor_net -p 8080:8080 -e TZ="America/New_York" -e CONFIG_GOOGLE_DOMAIN="com" -e CONFIG_LANGUAGE="en" -e CONFIG_NUMBER_OF_RESULTS="10" -e CONFIG_INVIDIOUS_INSTANCE="https://inv.nadeko.net" -e CONFIG_DISABLE_BITTORRENT_SEARCH=false -e CONFIG_HIDDEN_SERVICE_SEARCH=false -e CONFIG_INSTANCE_FALLBACK=true -e CONFIG_RATE_LIMIT_COOLDOWN=25 -e CONFIG_CACHE_TIME=20 -e CONFIG_DISABLE_API=false -e CONFIG_TEXT_SEARCH_ENGINE="auto" -e CURLOPT_PROXY_ENABLED=true -e CURLOPT_PROXY="172.4.0.5:9050" -e CURLOPT_PROXYTYPE="CURLPROXY_SOCKS5_HOSTNAME" -e CURLOPT_USERAGENT="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0" -e CURLOPT_FOLLOWLOCATION=true -v ./nginx_logs:/var/log/nginx -v ./php_logs:/var/log/php84 --restart unless-stopped librey:latest
docker run -d --name librey-librey_tor-1 --network librey_tor_net --ip=172.4.0.5 -v $PWD/../LibreY-Tor/torrc:/etc/tor/torrc librey-tor:latest

```

To update containers:
```sh
cd LibreY/
docker stop librey librey-librey_tor-1
docker rm librey librey-librey_tor-1
docker buildx build --rm --no-cache --pull -t librey:latest .
cd ../LibreY-Tor/
docker buildx build --rm --no-cache --pull -t librey-tor:latest .
# Repeat run commands
```
