const base = require('./webpack.config.base.js');
const LiveReloadPlugin = require('webpack-livereload-plugin');
const sassLintPlugin = require('sasslint-webpack-plugin');

module.exports = base.merge({
  devtool: 'eval',
  plugins: [
    new LiveReloadPlugin({appendScriptTag: true}),
    new sassLintPlugin({glob: 'app/scss/*scss'})
  ]
});
