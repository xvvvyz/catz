import webpack from 'webpack';
import { resolve } from 'path';
import { readdirSync } from 'fs';
import WebpackShellPlugin from 'webpack-shell-plugin';
import LiveReloadPlugin from 'webpack-livereload-plugin';
import nodeExternals from 'webpack-node-externals';

module.exports = env => {
  return {
    resolve: {
      modules: [
        resolve(__dirname, 'src', 'app'),
        resolve(__dirname, 'node_modules'),
      ],
    },
    entry: [
      'babel-polyfill',
      resolve(__dirname, 'src', 'app', 'app.jsx'),
    ],
    target: 'electron-renderer',
    output: {
      path: resolve(__dirname, 'src', 'bundle'),
      filename: 'app.js',
      pathinfo: !env.prod,
    },
    devtool: 'cheap-module-source-map',
    bail: env.prod,
    module: {
      rules: [
        {
          test: /\.jsx$/,
          use: ['babel-loader'],
        },
        {
          test: /\.css$/,
          use: ['style-loader', 'css-loader'],
        },
      ],
    },
    plugins: [].concat(env.prod ? [
      new webpack.optimize.UglifyJsPlugin({
        output: { screw_ie8: true, comments: false },
      }),
    ] : [
      new WebpackShellPlugin({ onBuildEnd: ['yarn run electron'] }),
      new webpack.HotModuleReplacementPlugin(),
      new LiveReloadPlugin({ appendScriptTag: true }),
    ]),
    externals: [nodeExternals()],
  };
};
