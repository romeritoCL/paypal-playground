var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .copyFiles({
        from: './assets/fonts',
        to: 'fonts/[path][name].[ext]',
    })

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('braintree-api', './assets/js/braintree/api/api.js')
    .addEntry('braintree-paylater', './assets/js/braintree/paylater.js')
    .addEntry('dropui', './assets/js/braintree/dropui.js')
    .addEntry('apm', './assets/js/braintree/apm.js')
    .addEntry('3ds', './assets/js/braintree/3ds.js')
    .addEntry('vault', './assets/js/braintree/vault.js')
    .addEntry('hosted-fields', './assets/js/braintree/hosted-fields.js')
    .addEntry('request', './assets/js/braintree/request.js')
    .addEntry('login', './assets/js/security/login.js')
    .addEntry('settings', './assets/js/settings/settings.js')
    .addEntry('pay-ins', './assets/js/paypal/pay-ins.js')
    .addEntry('ecs', './assets/js/paypal/ecs.js')
    .addEntry('adyen', './assets/js/paypal/adyen.js')
    .addEntry('billing-agreements', './assets/js/paypal/billing-agreements.js')
    .addEntry('bopis', './assets/js/paypal/bopis.js')
    .addEntry('paylater', './assets/js/paypal/paylater.js')
    .addEntry('pay-outs', './assets/js/paypal/pay-outs.js')
    .addEntry('invoices', './assets/js/paypal/invoices.js')
    .addEntry('subscriptions', './assets/js/paypal/subscriptions.js')
    .addEntry('connect', './assets/js/paypal/connect.js')
    .addEntry('authenticated', './assets/js/paypal/authenticated.js')
    .addEntry('users', './assets/js/hyperwallet/users.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
