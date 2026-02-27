/* eslint-disable */

// const EncodingPlugin = require('webpack-encoding-plugin');

module.exports = (env, option) => ({
    mode: 'development',
    entry: './src/index.jsx',
    output: {
        filename: 'script.js',
        path: __dirname,
    },
    resolve: {
        extensions: ['.js', '.jsx']
    },
    externals: {
      BX: 'BX'
    },
    devtool: option.mode === 'development' ? 'source-map' : false,
    module: {
        rules: [
          {
            test: /\.m?js$/,
            exclude: /node_modules/,
            use: {
              loader: 'babel-loader',
              options: {
                presets: ['@babel/preset-env']
              }
            }
          },
          {
            test: /\.jsx$/,
            exclude: /node_modules/,
            use: {
              loader: "babel-loader",
              options: {
                presets: ['@babel/preset-react']
              }
            }
          },
        ]
      },
    // plugins: [new EncodingPlugin({
    //     encoding: 'Windows-1251',
    // })],
})