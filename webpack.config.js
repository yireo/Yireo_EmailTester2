const path = require("path");

const webpack = require('webpack');

var config = env => {
    return {
        entry: "./view/adminhtml/react/index.jsx",
        output: {
            path: path.resolve("view/adminhtml/web/js"),
            filename: "react.js"
        },
        devServer: {
            overlay: true
        },
        mode: env.mode,
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: "babel-loader"
                },
                {
                    test: /\.jsx$/,
                    exclude: /node_modules/,
                    use: "babel-loader"
                }
            ]
        },
        resolve: {
            extensions: ['.js', '.jsx'],
        }
    }
};

module.exports = config;
