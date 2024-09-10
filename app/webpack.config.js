const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/js/app.js')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableVueLoader(() => {}, { runtimeCompilerBuild: false })
    .addPlugin(
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: true,
            __VUE_I18N_FULL_INSTALL__: true,
            __INTLIFY_PROD_DEVTOOLS__: false,
            __VUE_I18N_LEGACY_API__: false,
            __VUE_PROD_DEVTOOLS__: false,
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false
        })
    )
    .addAliases({
        '@': path.resolve('assets/js'),
        vue$: 'vue/dist/vue.esm-bundler',
    })
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
