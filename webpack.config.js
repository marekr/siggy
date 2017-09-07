const { join, resolve, relative } = require('path')
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CheckerPlugin = require('awesome-typescript-loader').CheckerPlugin;
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");

const ASSET_PATH = process.env.ASSET_PATH || 'http://dev.siggy.borkedlabs.com:8083';

const extractCSS = new ExtractTextPlugin({
	filename: 'site.css', 
	allChunks: true
});

module.exports = {

	stats: { modules: false },

	entry: { 
			'bundle': join(__dirname, 'frontend', 'siggy.ts'),
			},
	output: {
		path: join(__dirname, 'public','assets'),
		filename: 'siggy.js',
		publicPath: ASSET_PATH,
		// Bundle absolute resource paths in the source-map,
		// so VSCode can match the source file.
		devtoolModuleFilenameTemplate: '[absolute-resource-path]',
		libraryTarget: 'var',
		library: 'Siggy'
	},

	resolve: {
		extensions: ['.ts', '.tsx', '.js', '.jsx'],
		modules: [resolve(__dirname, 'frontend'), 'node_modules']
	},

	module: {
		rules: [
			{ test: /\.(tsx?|jsx?)$/, include: /frontend/, use: 'awesome-typescript-loader' },
			{ test: /\.(png|jpg|jpeg|gif|woff|woff2|eot|ttf|svg)(\?|$)/, use: 'url-loader?limit=100' },
			{
				test: /\.(scss|css)$/,
				use: extractCSS.extract({
					fallback: 'style-loader',
					//resolve-url-loader may be chained before sass-loader if necessary
					use: ['css-loader', 'sass-loader']
				})
			},
			{
				test: require.resolve('jquery'),
				use: [{
					loader: 'expose-loader',
					options: 'jQuery'
				},{
					loader: 'expose-loader',
					options: '$'
				}]
			}
		]
	},
	plugins: [
		new webpack.DllReferencePlugin({
			context: __dirname,
			manifest: require('./public/assets/vendor-manifest.json')
		}),
		new webpack.DefinePlugin({
			'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development'),
		}),
		new CheckerPlugin(),
		extractCSS,
		new webpack.SourceMapDevToolPlugin({
			filename: '[file].map', // Remove this line if you prefer inline source maps
			moduleFilenameTemplate: relative('./public/assets/', '[resourcePath]') // Point sourcemap entries to the original file locations on disk
		})
	].concat(process.env.NODE_ENV === 'production' ? [ 
		//production
		new UglifyJsPlugin()
	] : [ 
		//development
	]),

	devServer: {
		public: 'dev.siggy.borkedlabs.com:8083',
		port: 8083,
		historyApiFallback: {
			index: 'index.html'
		},
		contentBase: 'public/assets',
		headers: {
		  "Access-Control-Allow-Origin": "*",
		  "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, PATCH, OPTIONS",
		  "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization"
		}
	}
}