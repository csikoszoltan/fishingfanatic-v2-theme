# WordPress Starter Theme:

-   [Timber](https://wordpress.org/plugins/timber-library/)
-   [Tailwind CSS](https://tailwindcss.com/)
-   [Sass](https://sass-lang.com/)
-   [Browsersync](https://www.browsersync.io/)
-   [Laravel Mix](https://laravel.com/docs/master/mix)
-   [ES6](https://es6.io/)

## Installing the Theme

1. Repo clone to: /wp-content/themes/
2. Move to this theme then:
    - [Composer](https://getcomposer.org/) install
    - [NPM](https://www.npmjs.com/get-npm) install
3. webpack.mix.js: Change BrowserSync proxy URL

## Theme commands

-   npm run dev - compiles css + js files to public directory.
-   npm run watch - watches css + js + twig file changes and compiles in real time.
-   npm run prod - compiles css + js files to public directory and minifies them. Use this one in production!