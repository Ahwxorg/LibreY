### Introduction

- [Introduction](#introduction)
  - [Running a Docker container](#running-a-docker-container)
  - [Running a Docker container with Docker Compose](#running-a-docker-container-with-docker-compose)
  - [Running a Docker container with the Docker CLI (not recommended)](#running-a-docker-container-with-the-docker-cli-not-recommended)
  - [Environment variables that can be set in the Docker container](#environment-variables-that-can-be-set-in-the-docker-container)
    - [Search Configuration](#search)
    - [Frontend Configuration](#frontends)
    - [Search Engine Configuration](#engines)
    - [cURL Configuration](#curl)
    - [OpenSearch Configuration](#opensearch)
- [Docker version issues](#docker-version-issues)
- [Building a Docker image](#building-a-docker-image)
- [Support for different architectures](#support-for-different-architectures)

### Running a Docker container

Dockerized LibreY is a simple way to host LibreY with a view to privacy. If you wish to help, please start by looking for bugs in the example Docker configurations.

### Running a Docker container with Docker Compose

```yml
services:
  librey:
    image: ghcr.io/ahwxorg/librey:latest
    container_name: librey
    network_mode: bridge
    ports:
      - 8080:8080
    environment:
      - CONFIG_GOOGLE_DOMAIN=com
      - CONFIG_LANGUAGE=en
      - CONFIG_NUMBER_OF_RESULTS=10
      - CONFIG_INVIDIOUS_INSTANCE=https://inv.nadeko.net
      - CONFIG_DISABLE_BITTORRENT_SEARCH=false
      - CONFIG_HIDDEN_SERVICE_SEARCH=false
      - CONFIG_INSTANCE_FALLBACK=true
      - CONFIG_RATE_LIMIT_COOLDOWN=25
      - CONFIG_CACHE_TIME=20
      - CONFIG_DISABLE_API=false
      - CONFIG_TEXT_SEARCH_ENGINE=auto
      - CURLOPT_PROXY_ENABLED=false
      - CURLOPT_PROXY=192.0.2.53:8388
      - CURLOPT_PROXYTYPE=CURLPROXY_HTTP
      - CURLOPT_USERAGENT=Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0
      - CURLOPT_FOLLOWLOCATION=true
    volumes:
      # - ./nginx_logs:/var/log/nginx # Disabled by default. These are the NGINX request logs.
      - ./php_logs:/var/log/php84 # Enabled by default. These are the PHP error logs.
    restart: unless-stopped
  watchtower: # Watchtower is not required but highly recommended, since Watchtower will re-pull and restart the LibreY container automatically whenever there's an update.
    image: containrrr/watchtower
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
```

<br>

### Running a Docker container with the Docker CLI (not recommended)

This is still here for people that want to use this method of deployment, however it's neither recommended nor supported. To run LibreY in a Docker container, you can simply use the command:

```sh
docker run -d \
  --name librey \
  -e TZ="America/New_York" \
  -e CONFIG_GOOGLE_DOMAIN="com" \
  -e CONFIG_GOOGLE_LANGUAGE="en" \
  -e CONFIG_WIKIPEDIA_LANGUAGE="en" \
  -p 8080:8080 \
  ghcr.io/ahwxorg/librey:latest
```

Also run with watchtower for auto-updating: (optional)

```sh
docker run -d \
  --name librey-watchtower-1 \
  -v /var/run/docker.sock:/var/run/docker.sock \
  containrrr/watchtower
```

<br>

### Environment variables that can be set in the Docker container

This Docker image was developed with high configurability in mind, so here is the list of environment variables that can be changed according to your use case, no matter how specific.

<br>

### Search

| Variables                        | Default                                                                                                                                                                                                             | Examples                     | Description                                                                                                                                                      |
| :------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | :--------------------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| CONFIG_GOOGLE_DOMAIN             | "com"                                                                                                                                                                                                               | "com", "com.br", "cat", "se" | Defines which Google domain the search will be done on, change according to your country.                                                                        |
| CONFIG_LANGUAGE                  | "en"                                                                                                                                                                                                                | "zh-Hans", "fil", "no"       | Defines the language in which searches will be done, see the list of supported languages [here](https://developers.google.com/custom-search/docs/ref_languages). |
| CONFIG_NUMBER_OF_RESULTS         | 10                                                                                                                                                                                                                  | integer                      | Number of results for Google to return each page.                                                                                                                |
| CONFIG_INVIDIOUS_INSTANCE        | "https://invidious.snopyta.org"                                                                                                                                                                                     | string                       | Defines the host that will be used to do video searches using Invidious.                                                                                         |
| CONFIG_DISABLE_BITTORRENT_SEARCH | false                                                                                                                                                                                                               | boolean                      | Defines whether bittorrent search will be disabled                                                                                                               |
| CONFIG_BITTORRENT_TRACKERS       | "&tr=http://nyaa.tracker.wf:7777/announce&tr=udp://open.stealth.si:80/announce&tr=udp://tracker.opentrackr.org:1337/announce&tr=udp://exodus.desync.com:6969/announce&tr=udp://tracker.torrent.eu.org:451/announce" | string                       | Set list of bittorrent trackers for torrent search.                                                                                                              |
| CONFIG_HIDDEN_SERVICE_SEARCH     | false                                                                                                                                                                                                               | boolean                      | Defines whether hidden service search will be disabled                                                                                                           |
| CONFIG_INSTANCE_FALLBACK         | true                                                                                                                                                                                                                | boolean                      | Choose whether or not to use the API on the backend to request to another LibreX/Y instance in case of rate limiting.                                            |
| CONFIG_RATE_LIMIT_COOLDOWN       | 25                                                                                                                                                                                                                  | integer                      | Time in minutes to wait before sending requests to Google again after a rate limit.                                                                              |
| CONFIG_CACHE_TIME                | 20                                                                                                                                                                                                                  | integer                      | Time in minutes to store results for in the cache. -1 will disable caching.                                                                                      |
| CONFIG_DISABLE_API               | false                                                                                                                                                                                                               | boolean                      | Disable requests to /api.php                                                                                                                                     |
| CONFIG_SHOW_RESULT_SOURCE        | true                                                                                                                                                                                                                | boolean                      | Whether to show where the result is from on the results page                                                                                                     |
| CONFIG_DEFAULT_THEME             | "dark"                                                                                                                                                                                                              | "nord", "darker", "discord"  | The default theme for users without cookies or request parameters set.                                                                                           |

### Frontends

| Variables              | Default | Examples                      | Description                                                             |
| :--------------------- | :------ | :---------------------------- | :---------------------------------------------------------------------- |
| APP_INVIDIOUS          | ""      | "https://example.com", string | Integration with external self-hosted apps, configure the desired host. |
| APP_RIMGO              | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_SCRIBE             | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_GOTHUB             | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_NITTER             | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_REDLIB             | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_PROXITOK           | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_WIKILESS           | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_QUETRE             | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_LIBREMDB           | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_BREEZEWIKI         | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_ANONYMOUS_OVERFLOW | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_SUDS               | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |
| APP_BIBLIOREADS        | ""      | string                        | Integration with external self-hosted apps, configure the desired host. |

### Engines

| Variables                 | Default | Examples                                                      | Description                                                                 |
| :------------------------ | :------ | :------------------------------------------------------------ | :-------------------------------------------------------------------------- |
| CONFIG_TEXT_SEARCH_ENGINE | "auto"  | "brave", "duckduckgo", "ecosia", "google", "mojeek", "yandex" | Use a specific search engine, or "auto" to use any available search engine. |

### cURL

| Variables              | Default                                                                            | Examples                                                             | Description                                                                                           |
| :--------------------- | :--------------------------------------------------------------------------------- | :------------------------------------------------------------------- | :---------------------------------------------------------------------------------------------------- |
| CURLOPT_PROXY_ENABLED  | false                                                                              | boolean                                                              | If you want to use a proxy, you need to set this variable to true.                                    |
| CURLOPT_PROXY          | ""                                                                                 | "192.0.2.53:8388"                                                    | Set the proxy using the ip and port to be used.                                                       |
| CURLOPT_PROXYTYPE      | "CURLPROXY_HTTP"                                                                   | "CURLPROXY_SOCKS4A", "CURLPROXY_SOCKS5", "CURLPROXY_SOCKS5_HOSTNAME" | Set the type of proxy connection (if you enabled it).                                                 |
| CURLOPT_RETURNTRANSFER | true                                                                               | boolean                                                              | Return the transfer as a string of the return value of curl_exec() instead of outputting it directly. |
| CURLOPT_ENCODING       | ""                                                                                 | string                                                               | Return the transfer as a string of the return value of curl_exec() instead of outputting it directly. |
| CURLOPT_USERAGENT      | "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0" | string                                                               | This variable defines the 'User-Agent' that curl will use to attempt to avoid being blocked.          |
| CURLOPT_IPRESOLVE      | "CURL_IPRESOLVE_WHATEVER"                                                          | "CURL_IPRESOLVE_V4", "CURL_IPRESOLVE_V6"                             | Use a fixed IP version for making requests, or what DNS prefers.                                      |
| CURLOPT_CUSTOMREQUEST  | "GET"                                                                              | "POST", "CONNECT"                                                    | Defines the HTTP method that curl will use to make the request.                                       |
| CURLOPT_MAXREDIRS      | 5                                                                                  | integer                                                              | The maximum amount of HTTP redirections to follow, only enabled with CURLOPT_FOLLOWLOCATION.          |
| CURLOPT_TIMEOUT        | 3                                                                                  | integer                                                              | The maximum amount of time for cURL requests to complete.                                             |
| CURLOPT_VERBOSE        | false                                                                              | boolean                                                              | Whether to output verbose information.                                                                |
| CURLOPT_FOLLOWLOCATION | true                                                                               | boolean                                                              | Whether to follow any Location header. Required for instance fallback.                                |

### OpenSearch

| Variables               | Default                                                               | Examples | Description                                                               |
| :---------------------- | :-------------------------------------------------------------------- | :------- | :------------------------------------------------------------------------ |
| OPEN_SEARCH_TITLE       | "LibreY"                                                              | string   | [OpenSearch XML](https://developer.mozilla.org/en-US/docs/Web/OpenSearch) |
| OPEN_SEARCH_DESCRIPTION | "Framework and javascript free privacy respecting meta search engine" | string   | [OpenSearch XML](https://developer.mozilla.org/en-US/docs/Web/OpenSearch) |
| OPEN_SEARCH_ENCODING    | "UTF-8"                                                               | "UTF-8"  | [OpenSearch XML](https://developer.mozilla.org/en-US/docs/Web/OpenSearch) |
| OPEN_SEARCH_LONG_NAME   | "LibreY Search"                                                       | string   | [OpenSearch XML](https://developer.mozilla.org/en-US/docs/Web/OpenSearch) |
| OPEN_SEARCH_HOST        | "http://localhost:80"                                                 | string   | Host used to identify librey on the network                               |

<br>

### Docker version issues

If you are going to build your own Docker image based on this repository, pay attention to your Docker version, because depending on how recent the installed version is, maybe you should use the `buildx` command instead of `build`.

Docker <= 20.10: `docker build`

Docker > 20.10: `docker buildx build`

<br>

### Building a docker image

If you don't want to use the image that is already available on the GitHub container registry, then you can simply build the Dockerfile directly from the GitHub repository using the command:

```sh
docker build https://github.com/Ahwxorg/librey.git -t librey:latest
```

```sh
docker run -d --name librey \
    -e CONFIG_GOOGLE_DOMAIN="com" \
    -e CONFIG_GOOGLE_LANGUAGE="en" \
    -p 8080:8080 \
    librey:latest
```

Or, instead of doing the build remotely, you still have the opportunity to `git clone` the repository, and build it locally with the command:

```sh
git clone https://github.com/Ahwxorg/librey.git
cd librey/
docker build -t librey:latest .
```

<br>

### Support for different architectures

Supported architectures for the official librey images include the same ones supported by Alpine itself, which are typically denoted as `linux/386`, `linux/amd64`, `linux/arm/v6`. If you need support for a different architecture, such as `linux/arm/v7`, you can modify the 'Dockerfile' to use a more comprehensive base image like `ubuntu:latest` instead.

In this case, you must run the `buildx` process specifying the desired architecture as shown in the example below:

```sh
docker buildx build \
    --no-cache \
    --platform linux/arm/v7 \
    --tag ahwxorg/librey:latest .
```

**OBS:** Keep in mind that this can cause some issues at build time, so you need to know a little about Dockerfiles to solve this problem for your specific case.
