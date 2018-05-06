# easylanweb

Easylanweb is an intranet site in a box.

Originally developed for PHP v4.x to client specification the alpha version was produced as proof of concept in about five development hours excluding research time.

## Intended operation for client

1. [Setup xampp](https://www.apachefriends.org/faq_linux.html) on target server and start (only apache module is required), ensure reachable from the LAN (only!) and that apache module will auto-start.

2. Drop in htdocs from project, replacing existing htdocs and ensuring correct permissions.

3. Set full write permissions for `cache` folder.

4. Setup restricted share of `contents` folder and setup access for responsible admin curator. An easy method for their access should also be setup.

5. Contents of `contents` folder is automatically handled.

6. You have an intranet.

## The future

See [TODO](/TODO.MD).
