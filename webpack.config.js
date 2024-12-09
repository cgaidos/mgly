let ExtractTextPlugin = require('extract-text-webpack-plugin');
let webpack = require('webpack');
let path = require('path');
let BrowserSyncPlugin = require('browser-sync-webpack-plugin');

module.exports = {
    entry: ['./pilote/src/js/main.js'],
    cache: true,
    output: {
        filename: 'js/main.js',
        publicPath: '/moowgly/',
        path: __dirname+'/pilote/public/'
    },
    // target: "node",
    // node: {
    //     fs: "empty"
    // },
    devtool: "source-map",
    resolve: {
        extensions: [".js"],
        modules: ['*', 'bower_components', 'node_modules', 'pilote/src', 'pilote/src/js/vue'],
        alias: {
            'vue': 'vue/dist/vue.js'
        }
    },
    module: {
        rules: [
            {
                test: /\.(js)$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015']
                }
            },
            {
                test: /(font|fonts).*\.(woff2?|ttf|eot|svg|otf)$/,
                loader: 'file-loader',
                options: {
                    name: 'fonts/[name].[ext]?[hash]',
                }
            },
            {
                test: /\.(png|jpg|gif|svg)$/,
                exclude: /(node_modules|bower_components)/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path][name].[ext]?[hash]',
                            context: './pilote/src'
                        }
                    }
                ]
            },
            {
                test: /\.css$/,
                loader: ExtractTextPlugin.extract({
                    fallbackLoader: 'style-loader',
                    loader: [
                        'css-loader?-discardDuplicates',
                        'resolve-url-loader',
                    ]
                })
            },
            {
                test: /\.scss$/,
                exclude: /(node_modules|bower_components)/,
                loader: ExtractTextPlugin.extract({
                    fallbackLoader: 'style-loader',
                    loader: [
                        'css-loader?-discardDuplicates',
                        'resolve-url-loader',
                        'sass-loader?sourceMap&precision=8'
                        // 'fast-sass-loader?sourceMap&precision=8'
                    ]
                })
            }
        ]
    },

    plugins: [
        new BrowserSyncPlugin({
            host: 'http://moowgly.local',
            port: 3000,
        }),

        new ExtractTextPlugin({
            filename: 'css/main.css',
            disable: false,
            allChunks: true
        }),

        new webpack.ProvidePlugin({
            jQuery: 'jquery',
            $: 'jquery',
            jquery: 'jquery',
            'window.jQuery': 'jquery'
        }),
    ]
};