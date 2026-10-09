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
```

Step 3: Apply the docker-compose.yml patch.

```sh
cd LibreY/
git apply ../LibreY-Tor/LibreY_docker-compose-yml.diff
```

Step 4: Manually edit `LibreY/docker-compose.yml` and `LibreY-Tor/torrc` to configure LibreY and Tor to your liking.

Step 5: Run it! `docker compose up -d`

To update containers:

```sh
cd LibreY/
docker compose down
docker compose pull
docker compose up -d
```

It is recommended to build the images yourself to avoid an outdated Tor version.
