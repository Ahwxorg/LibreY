<h1 align="center">LibreY</h1>

[![Docker Image CI](https://github.com/Ahwxorg/LibreY/actions/workflows/docker-image.yml/badge.svg)](https://github.com/Ahwxorg/LibreY/actions/workflows/docker-image.yml)

> LibreY is a fork of LibreX, a framework-less and javascript-free privacy respecting meta search engine, made by [hnhx](https://github.com/hnhx). LibreY changed some features like automatic redirection (now instance fallback), added scrapers for more search engines and various other changes visible in the [commit log](https://github.com/hnhx/librex/compare/main...Ahwxorg:LibreY:main). The original code is written by [hnhx and contributors](https://github.com/hnhx/LibreX/contributors)

<p align="center">
  <img src="https://user-images.githubusercontent.com/49120638/215327189-76c54dec-8b19-4faf-8c39-29a61aa3b143.png" width="400">
  <img src="https://user-images.githubusercontent.com/49120638/215327239-b2a1cb07-3773-4ae7-bb3b-738de2cc3161.png" width="400">
</p>

<p align="center"></p>

<br>

## Matrix

If there's an important announcement, we do have a Matrix chatroom which you can join over at [#librey:ahwx.org](https://matrix.to/#/#librey:ahwx.org).

### Instances

You can find a list of instances on any LibreY instance by accessing /instances.php.<br>
Alternatively look at `instances.json` where the list is generated from.<br><br>
While the official instances may be more updated and have better uptime, please consider using another person's instances as these are heavily overloaded.<br>
Support the community. ❤️<br><br>
@codedipper's instance:<br>
[search.revvy.de](https://search.revvy.de/instances.php)<br>
[Tor](http://search.revvybrr6pvbx4n3j4475h4ghw4elqr4t5xo2vtd3gfpu2nrsnhh57id.onion/instances.php)<br>
[I2P](http://revekebotog64xrrammtsmjwtwlg3vqyzwdurzt2pu6botg4bejq.b32.i2p/instances.php)<br>
<br>
[@davidovski](https://github.com/davidovski)'s instance:<br>
[search.davidovski.xyz](https://search.davidovski.xyz/instances.php)<br>
<br>

### How to host LibreY

[This section has been moved](https://github.com/Ahwxorg/LibreY/tree/main/docs) to clear up some README space.

### About LibreY

LibreY gives you text results from Google, DuckDuckGo, Brave Search, Ecosia, Yandex Search and Mojeek. LibreY provides images from Qwant, and torrents from i.e. Ahmia and popular torrent sites. LibreY does this without spying on you.
<br>LibreY doesn't save **any** type of data about the user, there are no logs (except NGINX logs if the host sets them).

### LibreY compared to other metasearch engines

| Name    | Works without JS     | Privacy frontend redirect | Torrent results | API | No 3rd party libs used |
| ------- | -------------------- | ------------------------- | --------------- | --- | ---------------------- |
| LibreY  | ✅                   | ✅                        | ✅              | ✅  | ✅                     |
| SearXNG | ❓ Not user friendly | ❓ Only host can set it   | ✅              | ✅  | ❌                     |
| Whoogle | ✅                   | ❓ Only host can set it   | ❌              | ❌  | ❌                     |
