let mix = require("laravel-mix");

mix.js(["resources/scripts/app.js"], "public/scripts/app.js")
	.js(["resources/scripts/navigation.js"], "public/scripts/navigation.js")
	.js(["resources/scripts/admin.js"], "public/scripts/admin.js")
	.postCss("resources/styles/app.css", "public/styles", [
		require("@tailwindcss/postcss"),
	])
	.webpackConfig({
		watchOptions: { ignored: /node_modules|public|mix-manifest.json/ },
	})
	.browserSync({
		proxy: "http://fishingfanatic.test/",
		files: [
			"./resources/scripts/**/*.js",
			"./resources/styles/**/*.css",
			"./resources/images/**/*.+(png|jpg|svg)",
			"./**/*.+(html|php)",
			"./views/**/*.+(html|twig)",
		],
	});

mix.disableSuccessNotifications();
