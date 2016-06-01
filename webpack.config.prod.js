const base = require('./webpack.config.base.js');
const webpack = require('webpack');

module.exports = base.merge({
  devtool: 'source-map',
  plugins: [
    new webpack.DefinePlugin({__DEV__: false, 'process.env': {NODE_ENV: JSON.stringify('production')}}),
    new webpack.optimize.OccurenceOrderPlugin(),
    new webpack.optimize.UglifyJsPlugin({compressor: {screw_ie8: true, warnings: false}}),
  ]
});
