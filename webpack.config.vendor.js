const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const merge = require('webpack-merge');
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");

const extractCSS = new ExtractTextPlugin('vendor.css');
const ASSET_PATH = process.env.ASSET_PATH || 'http://dev.siggy.borkedlabs.com:8083';

module.exports = {
    stats: { modules: false },
    resolve: { extensions: [ '.js' ] },
    module: {
        rules: [
            { test: /\.(png|woff|woff2|eot|ttf|svg)(\?|$)/, use: 'url-loader?limit=100' },
			{ test: /\.css(\?|$)/, use: extractCSS.extract({ use: 'css-loader' }) },
			{
				test: require.resolve('jquery'),
				use: [{
					loader: 'expose-loader',
					options: 'jQuery'
				},{
					loader: 'expose-loader',
					options: '$'
				}]
			},
			{
				test: /\.js$/,
				include: [
					path.join(__dirname, 'frontend','legacy')
				],
				use: [ 'script-loader' ]
			},
			{ 
				test: /jquery\/.+\.(jsx|js)$/,
				loader: 'imports?jQuery=jquery,$=jquery,this=>window'
			}
        ]
    },
    entry: {
        vendor: [
			'jquery',
			'moment',
			'moment-timezone',
			'chart.js',
			'validate.js',
			'handlebars-form-helpers',
			'corejs-typeahead',
			'qtip2',
			'./frontend/legacy/jquery.jsPlumb.js',
			'./frontend/jquery/bootstrap.dropdown.js',
			'./frontend/jquery/bootstrap.tab.js',
			'./frontend/jquery/jquery-ui.js',
			'./frontend/jquery/jquery.contextMenu.js',
			'./frontend/jquery/jquery.blockUI.js',
			'./frontend/jquery/jquery.flash.js',
			'./frontend/jquery/jquery.hotkeys.js',
			'./frontend/jquery/jquery.idle.js',
			'./frontend/jquery/jquery.serializeObject.js',
			'./frontend/jquery/jquery.simplePagination.js',
			'./frontend/jquery/jquery.tablesorter.js',
			'./frontend/jquery/translate.js',
			'./frontend/vendor/handlebars.js',
			'./frontend/vendor/handlebars.form-helpers.js',
			'./frontend/vendor/handlebars.helpers.js'
        ],
    },
    output: {
		publicPath: ASSET_PATH,
		path: path.join(__dirname, 'public','assets'),
		filename: 'vendor.js',
        library: '[name]_[hash]',

        // Bundle absolute resource paths in the source-map,
        // so VSCode can match the source file.
        devtoolModuleFilenameTemplate: '[absolute-resource-path]'
    },
    plugins: [
		new webpack.NormalModuleReplacementPlugin(/\/iconv-loader$/, require.resolve('node-noop')), // Workaround for https://github.com/andris9/encoding/issues/16
		new webpack.DefinePlugin({
			'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development'),
		}),
		extractCSS,
		new webpack.ProvidePlugin({
			$: "jquery",
			jQuery: "jquery",
			"window.jQuery": "jquery"
		}),
        new webpack.DllPlugin({
            path: path.join(__dirname, 'public','assets', '[name]-manifest.json'),
            name: '[name]_[hash]'
		})
    ].concat(process.env.NODE_ENV === 'production' ? [ 
		//production
		new UglifyJsPlugin()
	] : [ 
		//development
	])
};
